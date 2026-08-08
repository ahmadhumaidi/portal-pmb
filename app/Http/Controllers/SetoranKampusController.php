<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\SetoranKampus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SetoranKampusController extends Controller
{
    private array $jenisSetorans = ['Biaya Pendidikan', 'Wisuda', 'Almamater'];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $setoranKampus = SetoranKampus::query()
            ->with('mahasiswa.kampus')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('kode_setoran_kampus', 'like', "%{$search}%")
                        ->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($search) {
                            $mahasiswaQuery
                                ->where('nama_mahasiswa', 'like', "%{$search}%")
                                ->orWhere('kode_pmb', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('tanggal_setor')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        return view('setoran-kampus.index', compact('setoranKampus', 'search'));
    }

    public function create(Request $request): View
    {
        $mahasiswas = Mahasiswa::query()
            ->with(['kampus', 'setoranKampus:id,mahasiswa_id,jenis_setoran,nominal', 'pembayarans:id,mahasiswa_id,jenis_pembayaran'])
            ->orderBy('nama_mahasiswa')
            ->get();
        $selectedMahasiswa = $request->query('mahasiswa_id');
        $jenisSetorans = $this->jenisSetorans;

        $kewajibanByMahasiswa = $mahasiswas->mapWithKeys(function (Mahasiswa $mahasiswa) {
            $perJenis = collect($this->jenisSetorans)->mapWithKeys(function (string $jenis) use ($mahasiswa) {
                $target = $mahasiswa->targetBiayaKampusJenis($jenis);
                $kewajiban = $mahasiswa->kewajibanKampusJenis($jenis);
                $opsionalBelumOptIn = $jenis === 'Almamater' && !$mahasiswa->sudahOptInAlmamater();

                return [
                    $jenis => [
                        'target' => $target,
                        'sudah_disetor' => $mahasiswa->totalSetorKampusJenis($jenis),
                        'tunggakan' => $kewajiban !== null ? max(0, $kewajiban) : null,
                        'opsional_belum_opt_in' => $opsionalBelumOptIn,
                    ],
                ];
            });

            return [$mahasiswa->id => $perJenis];
        });

        return view('setoran-kampus.create', compact('mahasiswas', 'selectedMahasiswa', 'jenisSetorans', 'kewajibanByMahasiswa'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswas,id'],
            'nominal' => ['required', 'array'],
            'nominal.*' => ['nullable', 'numeric', 'min:0'],
            'tanggal_setor' => ['required', 'date'],
            'bukti_setor' => ['nullable', 'file', 'max:5120'],
            'catatan' => ['nullable', 'string'],
        ]);

        $items = collect($validated['nominal'])
            ->only($this->jenisSetorans)
            ->filter(fn ($nominal) => $nominal !== null && $nominal !== '' && (float) $nominal > 0);

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'nominal' => 'Isi nominal untuk setidaknya satu jenis setoran.',
            ]);
        }

        $mahasiswa = Mahasiswa::findOrFail($validated['mahasiswa_id']);

        $buktiSetorPath = null;

        if ($request->hasFile('bukti_setor')) {
            $buktiSetorPath = $request->file('bukti_setor')->store('setoran-kampus/' . $mahasiswa->kode_pmb, 'public');
        }

        $inputBy = auth()->id();
        $first = null;

        DB::transaction(function () use ($items, $mahasiswa, $validated, $buktiSetorPath, $inputBy, &$first) {
            foreach ($items as $jenis => $nominal) {
                $setoranKampus = SetoranKampus::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'jenis_setoran' => $jenis,
                    'nominal' => $nominal,
                    'tanggal_setor' => $validated['tanggal_setor'],
                    'bukti_setor_path' => $buktiSetorPath,
                    'catatan' => $validated['catatan'] ?? null,
                    'input_by' => $inputBy,
                ]);

                $first ??= $setoranKampus;
            }
        });

        $message = $items->count() > 1
            ? $items->count() . ' setoran ke kampus (' . $items->keys()->implode(', ') . ') berhasil disimpan.'
            : 'Setoran ke kampus berhasil disimpan.';

        return redirect()->route('setoran-kampus.show', $first)->with('success', $message);
    }

    public function show(SetoranKampus $setoranKampus): View
    {
        $setoranKampus->load(['mahasiswa.kampus', 'inputBy']);

        return view('setoran-kampus.show', compact('setoranKampus'));
    }

    public function edit(SetoranKampus $setoranKampus): View
    {
        $setoranKampus->load('mahasiswa.kampus');

        return view('setoran-kampus.edit', compact('setoranKampus'));
    }

    public function update(Request $request, SetoranKampus $setoranKampus): RedirectResponse
    {
        $validated = $request->validate([
            'nominal' => ['required', 'numeric', 'min:0'],
            'tanggal_setor' => ['required', 'date'],
            'bukti_setor' => ['nullable', 'file', 'max:5120'],
            'catatan' => ['nullable', 'string'],
        ]);

        $setoranKampus->loadMissing('mahasiswa');

        if ($request->hasFile('bukti_setor')) {
            if ($setoranKampus->bukti_setor_path) {
                Storage::disk('public')->delete($setoranKampus->bukti_setor_path);
            }

            $validated['bukti_setor_path'] = $request->file('bukti_setor')->store('setoran-kampus/' . $setoranKampus->mahasiswa->kode_pmb, 'public');
        }

        unset($validated['bukti_setor']);

        $setoranKampus->update($validated);

        return redirect()->route('setoran-kampus.show', $setoranKampus)->with('success', 'Setoran ke kampus berhasil diperbarui.');
    }

    public function destroy(SetoranKampus $setoranKampus): RedirectResponse
    {
        if ($setoranKampus->bukti_setor_path) {
            Storage::disk('public')->delete($setoranKampus->bukti_setor_path);
        }

        $setoranKampus->delete();

        return redirect()->route('setoran-kampus.index')->with('success', 'Setoran ke kampus berhasil dihapus.');
    }

    public function viewFile(SetoranKampus $setoranKampus): BinaryFileResponse
    {
        abort_if(blank($setoranKampus->bukti_setor_path), 404, 'File belum tersedia.');

        abort_unless(Storage::disk('public')->exists($setoranKampus->bukti_setor_path), 404, 'File tidak ditemukan.');

        return response()->file(Storage::disk('public')->path($setoranKampus->bukti_setor_path));
    }
}
