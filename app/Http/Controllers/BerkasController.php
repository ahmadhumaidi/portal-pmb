<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BerkasController extends Controller
{
    private array $statuses = ['belum_upload', 'belum_lengkap', 'menunggu_verifikasi', 'lengkap', 'perlu_revisi'];

    private array $fileFields = [
        'ijazah' => ['column' => 'ijazah_path', 'label' => 'Ijazah'],
        'transkrip_awal' => ['column' => 'transkrip_awal_path', 'label' => 'Transkrip Awal'],
        'ktp' => ['column' => 'ktp_path', 'label' => 'KTP'],
        'kk' => ['column' => 'kk_path', 'label' => 'KK'],
        'pas_foto' => ['column' => 'pas_foto_path', 'label' => 'Pas Foto'],
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $berkas = Berkas::query()
            ->with(['mahasiswa.kampus', 'mahasiswa.jurusan'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('kode_berkas', 'like', "%{$search}%")
                        ->orWhere('ijazah_path', 'like', "%{$search}%")
                        ->orWhere('ktp_path', 'like', "%{$search}%")
                        ->orWhere('kk_path', 'like', "%{$search}%")
                        ->orWhere('pas_foto_path', 'like', "%{$search}%")
                        ->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($search) {
                            $mahasiswaQuery
                                ->where('nama_mahasiswa', 'like', "%{$search}%")
                                ->orWhere('kode_pmb', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, fn ($query) => $query->where('status_verifikasi', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statuses = $this->statuses;

        return view('berkas.index', compact('berkas', 'search', 'status', 'statuses'));
    }

    public function show(Berkas $berkas): View
    {
        $berkas->load(['mahasiswa.kampus', 'mahasiswa.jurusan', 'inputBy']);

        return view('berkas.show', [
            'berkas' => $berkas,
            'fileFields' => $this->fileFields,
        ]);
    }

    public function edit(Berkas $berkas): View
    {
        $berkas->load('mahasiswa');
        $statuses = $this->statuses;

        return view('berkas.edit', [
            'berkas' => $berkas,
            'statuses' => $statuses,
            'fileFields' => $this->fileFields,
        ]);
    }

    public function viewFile(Berkas $berkas, string $field): BinaryFileResponse|RedirectResponse
    {
        abort_unless(isset($this->fileFields[$field]), 404);

        $column = $this->fileFields[$field]['column'];
        $path = $berkas->{$column};

        abort_if(blank($path), 404, 'File belum tersedia.');

        if (Storage::disk('public')->exists($path)) {
            return response()->file(Storage::disk('public')->path($path));
        }

        $berkas->loadMissing('mahasiswa');
        $driveFolderUrl = $berkas->mahasiswa->google_drive_folder_url;

        abort_if(blank($driveFolderUrl), 404, 'File sudah di-backup ke Google Drive tetapi link folder tidak ditemukan.');

        return redirect()->away($driveFolderUrl);
    }

    public function update(Request $request, Berkas $berkas, GoogleDriveService $googleDrive): RedirectResponse
    {
        $berkas->loadMissing('mahasiswa');

        $validated = $request->validate([
            'ijazah' => ['nullable', 'file', 'max:5120'],
            'transkrip_awal' => ['nullable', 'file', 'max:5120'],
            'ktp' => ['nullable', 'file', 'max:5120'],
            'kk' => ['nullable', 'file', 'max:5120'],
            'pas_foto' => ['nullable', 'file', 'image', 'max:5120'],
            'status_verifikasi' => ['required', 'in:' . implode(',', $this->statuses)],
            'keterangan' => ['nullable', 'string'],
        ]);

        $directory = 'berkas/' . $berkas->mahasiswa->kode_pmb;
        $driveErrors = [];
        $hasUploadedFile = false;

        foreach ($this->fileFields as $input => $meta) {
            if ($request->hasFile($input)) {
                $hasUploadedFile = true;
                $column = $meta['column'];

                if ($berkas->{$column}) {
                    Storage::disk('public')->delete($berkas->{$column});
                }

                $result = $googleDrive->storeAndBackup($berkas->mahasiswa, $request->file($input), $directory, $meta['label']);
                $validated[$column] = $result['path'];

                if ($result['failed']) {
                    $driveErrors[] = $meta['label'];
                }
            }
        }

        unset($validated['ijazah'], $validated['transkrip_awal'], $validated['ktp'], $validated['kk'], $validated['pas_foto']);

        $berkas->update($validated);

        $message = 'Berkas mahasiswa berhasil diperbarui.';
        $redirect = redirect()->route('berkas.show', $berkas)->with('success', $message);

        if ($hasUploadedFile && !$googleDrive->configured()) {
            return $redirect->with('warning', 'Upload lokal berhasil. Google Drive belum aktif karena credential belum diisi.');
        }

        if ($driveErrors) {
            return $redirect->with('warning', 'Upload lokal berhasil, tetapi sebagian file gagal masuk Google Drive: ' . implode(', ', $driveErrors) . '.');
        }

        if ($hasUploadedFile) {
            return redirect()->route('berkas.show', $berkas)->with('success', 'Berkas mahasiswa berhasil diperbarui dan dikirim ke Google Drive.');
        }

        return $redirect;
    }
}
