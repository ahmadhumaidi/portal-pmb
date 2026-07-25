<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HasilController extends Controller
{
    private array $statuses = ['belum_siap', 'siap_dikirim', 'sudah_dikirim', 'sudah_diterima', 'perlu_revisi'];

    private array $fileFields = [
        'screenshot_pisn' => ['column' => 'screenshot_pisn_path', 'label' => 'Screenshot PISN'],
        'screenshot_satudikti' => ['column' => 'screenshot_satudikti_path', 'label' => 'Screenshot Satudikti'],
        'scan_ijazah' => ['column' => 'scan_ijazah_path', 'label' => 'Scan Ijazah'],
        'scan_transkrip' => ['column' => 'scan_transkrip_path', 'label' => 'Scan Transkrip'],
    ];

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

        return view('hasil.show', [
            'hasil' => $hasil,
            'fileFields' => $this->fileFields,
        ]);
    }

    public function viewFile(Hasil $hasil, string $field): BinaryFileResponse|RedirectResponse
    {
        abort_unless(isset($this->fileFields[$field]), 404);

        $column = $this->fileFields[$field]['column'];
        $path = $hasil->{$column};

        abort_if(blank($path), 404, 'File belum tersedia.');

        if (Storage::disk('public')->exists($path)) {
            return response()->file(Storage::disk('public')->path($path));
        }

        $hasil->loadMissing('mahasiswa');
        $driveFolderUrl = $hasil->mahasiswa->google_drive_folder_url;

        abort_if(blank($driveFolderUrl), 404, 'File sudah di-backup ke Google Drive tetapi link folder tidak ditemukan.');

        return redirect()->away($driveFolderUrl);
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

        $hasil->loadMissing('mahasiswa');
        $directory = 'hasil/' . $hasil->mahasiswa->kode_pmb;

        foreach ($this->fileFields as $input => $meta) {
            $column = $meta['column'];

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
