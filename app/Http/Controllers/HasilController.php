<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HasilController extends Controller
{
    private array $statuses = ['belum_siap', 'siap_dikirim', 'sudah_dikirim', 'sudah_diterima', 'perlu_revisi'];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $hasils = Hasil::query()
            ->with(['mahasiswa.kampus', 'mahasiswa.jurusan'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('kode_hasil', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%")
                        ->orWhere('nomor_seri_ijazah', 'like', "%{$search}%")
                        ->orWhere('status_kelulusan', 'like', "%{$search}%")
                        ->orWhere('link_pddikti', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($search) {
                            $mahasiswaQuery
                                ->where('nama_mahasiswa', 'like', "%{$search}%")
                                ->orWhere('kode_pmb', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, fn ($query) => $query->where('status_kirim', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statuses = $this->statuses;

        return view('hasil.index', compact('hasils', 'search', 'status', 'statuses'));
    }

    public function show(Hasil $hasil): View
    {
        $hasil->load(['mahasiswa.kampus', 'mahasiswa.jurusan', 'inputBy']);

        return view('hasil.show', compact('hasil'));
    }

    public function edit(Hasil $hasil): View
    {
        $hasil->load('mahasiswa');
        $statuses = $this->statuses;

        return view('hasil.edit', compact('hasil', 'statuses'));
    }

    public function update(Request $request, Hasil $hasil): RedirectResponse
    {
        $validated = $request->validate([
            'status_kelulusan' => ['nullable', 'string', 'max:255'],
            'nim' => ['nullable', 'string', 'max:255'],
            'nomor_seri_ijazah' => ['nullable', 'string', 'max:255'],
            'link_pddikti' => ['nullable', 'url'],
            'screenshot_pisn' => ['nullable', 'file', 'max:5120'],
            'screenshot_satudikti' => ['nullable', 'file', 'max:5120'],
            'scan_ijazah' => ['nullable', 'file', 'max:5120'],
            'scan_transkrip' => ['nullable', 'file', 'max:5120'],
            'status_kirim' => ['required', 'in:' . implode(',', $this->statuses)],
            'tanggal_kirim' => ['nullable', 'date'],
            'metode_kirim' => ['nullable', 'string', 'max:255'],
            'nomor_resi' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $directory = 'hasil/' . $hasil->mahasiswa->kode_pmb;
        $files = [
            'screenshot_pisn' => 'screenshot_pisn_path',
            'screenshot_satudikti' => 'screenshot_satudikti_path',
            'scan_ijazah' => 'scan_ijazah_path',
            'scan_transkrip' => 'scan_transkrip_path',
        ];

        foreach ($files as $input => $column) {
            if ($request->hasFile($input)) {
                if ($hasil->{$column}) {
                    Storage::disk('public')->delete($hasil->{$column});
                }
                $validated[$column] = $request->file($input)->store($directory, 'public');
            }
            unset($validated[$input]);
        }

        $validated['input_by'] = auth()->id();
        $hasil->update($validated);

        return redirect()->route('hasil.show', $hasil)->with('success', 'Hasil mahasiswa berhasil diperbarui.');
    }
}
