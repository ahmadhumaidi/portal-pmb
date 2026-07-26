<?php

namespace App\Http\Controllers;

use App\Models\BiayaKampus;
use App\Models\Jurusan;
use App\Models\Kampus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BiayaKampusController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $kampusId = $request->query('kampus_id');

        $biayaKampus = BiayaKampus::query()
            ->with(['kampus', 'jurusan'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('kode_biaya_kampus', 'like', "%{$search}%")
                        ->orWhereHas('kampus', function ($kampusQuery) use ($search) {
                            $kampusQuery->where('nama_kampus', 'like', "%{$search}%");
                        })
                        ->orWhereHas('jurusan', function ($jurusanQuery) use ($search) {
                            $jurusanQuery->where('nama_jurusan', 'like', "%{$search}%");
                        });
                });
            })
            ->when($kampusId, fn ($query) => $query->where('kampus_id', $kampusId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kampuses = Kampus::query()->orderBy('nama_kampus')->get();

        return view('master.biaya-kampus.index', compact('biayaKampus', 'kampuses', 'search', 'kampusId'));
    }

    public function create(): View
    {
        $kampuses = Kampus::query()->where('status_aktif', true)->orderBy('nama_kampus')->get();
        $jurusans = Jurusan::query()->where('status_aktif', true)->orderBy('nama_jurusan')->get();

        return view('master.biaya-kampus.create', compact('kampuses', 'jurusans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kampus_id' => ['required', 'integer', 'exists:kampuses,id'],
            'semua_prodi' => ['nullable', 'boolean'],
            'jurusan_ids' => ['nullable', 'array'],
            'jurusan_ids.*' => [
                'integer',
                Rule::exists('jurusans', 'id')->where(fn ($query) => $query->where('kampus_id', $request->kampus_id)),
            ],
            'biaya' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $targets = $this->resolveJurusanTargets($request);

        if (empty($targets)) {
            throw ValidationException::withMessages([
                'jurusan_ids' => 'Pilih "Semua Prodi" atau minimal satu prodi tertentu.',
            ]);
        }

        $statusAktif = $request->boolean('status_aktif');
        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($request, $targets, $statusAktif, &$createdCount, &$skippedCount) {
            foreach ($targets as $jurusanId) {
                if ($this->ruleExists((int) $request->kampus_id, $jurusanId)) {
                    $skippedCount++;
                    continue;
                }

                BiayaKampus::create([
                    'kampus_id' => $request->kampus_id,
                    'jurusan_id' => $jurusanId,
                    'biaya' => $request->biaya,
                    'keterangan' => $request->keterangan,
                    'status_aktif' => $statusAktif,
                ]);
                $createdCount++;
            }
        });

        if ($createdCount === 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Semua prodi yang dipilih sudah memiliki aturan biaya sebelumnya.');
        }

        $message = "{$createdCount} aturan biaya berhasil ditambahkan.";

        if ($skippedCount > 0) {
            $message .= " {$skippedCount} dilewati karena sudah memiliki aturan biaya sebelumnya.";
        }

        return redirect()->route('biaya-kampus.index')->with('success', $message);
    }

    public function edit(BiayaKampus $biayaKampus): View
    {
        $kampuses = Kampus::query()->orderBy('nama_kampus')->get();
        $jurusans = Jurusan::query()->orderBy('nama_jurusan')->get();

        return view('master.biaya-kampus.edit', compact('biayaKampus', 'kampuses', 'jurusans'));
    }

    public function update(Request $request, BiayaKampus $biayaKampus): RedirectResponse
    {
        $request->validate([
            'kampus_id' => ['required', 'integer', 'exists:kampuses,id'],
            'semua_prodi' => ['nullable', 'boolean'],
            'jurusan_ids' => ['nullable', 'array'],
            'jurusan_ids.*' => [
                'integer',
                Rule::exists('jurusans', 'id')->where(fn ($query) => $query->where('kampus_id', $request->kampus_id)),
            ],
            'biaya' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $targets = $this->resolveJurusanTargets($request);

        if (empty($targets)) {
            throw ValidationException::withMessages([
                'jurusan_ids' => 'Pilih "Semua Prodi" atau satu prodi tertentu.',
            ]);
        }

        $jurusanId = $targets[0];

        if ($this->ruleExists((int) $request->kampus_id, $jurusanId, $biayaKampus->id)) {
            throw ValidationException::withMessages([
                'jurusan_ids' => 'Sudah ada aturan biaya untuk kampus dan prodi (atau semua prodi) ini.',
            ]);
        }

        $biayaKampus->update([
            'kampus_id' => $request->kampus_id,
            'jurusan_id' => $jurusanId,
            'biaya' => $request->biaya,
            'keterangan' => $request->keterangan,
            'status_aktif' => $request->boolean('status_aktif'),
        ]);

        return redirect()
            ->route('biaya-kampus.index')
            ->with('success', 'Biaya kampus berhasil diperbarui.');
    }

    public function destroy(BiayaKampus $biayaKampus): RedirectResponse
    {
        $biayaKampus->delete();

        return redirect()
            ->route('biaya-kampus.index')
            ->with('success', 'Biaya kampus berhasil dihapus.');
    }

    /**
     * @return array<int, int|null> null represents "semua prodi"
     */
    private function resolveJurusanTargets(Request $request): array
    {
        if ($request->boolean('semua_prodi')) {
            return [null];
        }

        $ids = array_filter((array) $request->input('jurusan_ids', []), fn ($value) => $value !== null && $value !== '');

        return array_values(array_unique(array_map('intval', $ids)));
    }

    private function ruleExists(int $kampusId, ?int $jurusanId, ?int $ignoreId = null): bool
    {
        return BiayaKampus::query()
            ->where('kampus_id', $kampusId)
            ->where(function ($query) use ($jurusanId) {
                $jurusanId ? $query->where('jurusan_id', $jurusanId) : $query->whereNull('jurusan_id');
            })
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
