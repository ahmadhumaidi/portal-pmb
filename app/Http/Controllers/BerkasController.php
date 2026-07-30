<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BerkasController extends Controller
{
    private array $statuses = ['belum_upload', 'belum_lengkap', 'menunggu_verifikasi', 'lengkap', 'perlu_revisi'];

    private array $fileFields = [
        'ijazah' => ['column' => 'ijazah_path', 'drive_url_column' => 'ijazah_drive_url', 'label' => 'Ijazah'],
        'ktp' => ['column' => 'ktp_path', 'drive_url_column' => 'ktp_drive_url', 'label' => 'KTP'],
        'kk' => ['column' => 'kk_path', 'drive_url_column' => 'kk_drive_url', 'label' => 'KK'],
        'pas_foto' => ['column' => 'pas_foto_path', 'drive_url_column' => 'pas_foto_drive_url', 'label' => 'Pas Foto'],
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
            ->paginate($this->resolvePerPage($request))
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

        $meta = $this->fileFields[$field];
        $path = $berkas->{$meta['column']};

        abort_if(blank($path), 404, 'File belum tersedia.');

        if (Storage::disk('public')->exists($path)) {
            return response()->file(Storage::disk('public')->path($path));
        }

        $driveFileUrl = $berkas->{$meta['drive_url_column']};

        if (filled($driveFileUrl)) {
            return redirect()->away($driveFileUrl);
        }

        $berkas->loadMissing('mahasiswa');
        $driveFolderUrl = $berkas->mahasiswa->google_drive_folder_url;

        abort_if(blank($driveFolderUrl), 404, 'File sudah di-backup ke Google Drive tetapi link folder tidak ditemukan.');

        return redirect()->away($driveFolderUrl);
    }

    public function update(Request $request, Berkas $berkas): RedirectResponse
    {
        $berkas->loadMissing('mahasiswa.pembayarans');

        $validated = $request->validate([
            'ijazah' => ['nullable', 'file', 'max:5120'],
            'ktp' => ['nullable', 'file', 'max:5120'],
            'kk' => ['nullable', 'file', 'max:5120'],
            'pas_foto' => ['nullable', 'file', 'image', 'max:5120'],
            'status_verifikasi' => ['required', 'in:' . implode(',', $this->statuses)],
            'keterangan' => ['nullable', 'string'],
        ]);

        $directory = 'berkas/' . $berkas->mahasiswa->kode_pmb;

        foreach ($this->fileFields as $input => $meta) {
            if ($request->hasFile($input)) {
                $column = $meta['column'];

                if ($berkas->{$column}) {
                    Storage::disk('public')->delete($berkas->{$column});
                }

                $validated[$column] = $request->file($input)->store($directory, 'public');
                $validated[$meta['drive_url_column']] = null;
            }
        }

        unset($validated['ijazah'], $validated['ktp'], $validated['kk'], $validated['pas_foto']);

        $berkas->update($validated);

        $pembayaran = $berkas->mahasiswa->pembayarans->firstWhere('jenis_pembayaran', 'Pendaftaran')
            ?? $berkas->mahasiswa->pembayarans->first();

        if ($pembayaran) {
            return redirect()->route('pembayaran.edit', $pembayaran)->with('success', 'Berkas mahasiswa berhasil diperbarui. Silakan lengkapi pembayaran.');
        }

        return redirect()->route('berkas.show', $berkas)->with('success', 'Berkas mahasiswa berhasil diperbarui.');
    }

    public function destroy(Berkas $berkas): RedirectResponse
    {
        foreach ($this->fileFields as $meta) {
            if ($berkas->{$meta['column']}) {
                Storage::disk('public')->delete($berkas->{$meta['column']});
            }
        }

        $berkas->delete();

        return redirect()->route('berkas.index')->with('success', 'Data berkas berhasil dihapus.');
    }
}
