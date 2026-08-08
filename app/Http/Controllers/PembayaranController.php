<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PembayaranController extends Controller
{
    private array $statuses = ['menunggu', 'terverifikasi', 'ditolak', 'dibatalkan'];

    private array $jenisPembayarans = ['Angsuran', 'Wisuda', 'Almamater'];

    private array $fileFields = [
        'bukti_bayar' => ['column' => 'bukti_bayar_path', 'drive_url_column' => 'bukti_bayar_drive_url', 'label' => 'Bukti Bayar'],
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $pembayarans = Pembayaran::query()
            ->with(['mahasiswa.kampus', 'mahasiswa.jurusan'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('kode_pembayaran', 'like', "%{$search}%")
                        ->orWhere('jenis_pembayaran', 'like', "%{$search}%")
                        ->orWhere('bukti_bayar_path', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%")
                        ->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($search) {
                            $mahasiswaQuery
                                ->where('nama_mahasiswa', 'like', "%{$search}%")
                                ->orWhere('kode_pmb', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, fn ($query) => $query->where('status_bayar', $status))
            ->latest('tanggal_bayar')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $statuses = $this->statuses;

        return view('pembayaran.index', compact('pembayarans', 'search', 'status', 'statuses'));
    }

    public function create(Request $request): View
    {
        $mahasiswas = Mahasiswa::query()
            ->with(['kampus', 'jurusan', 'pembayarans:id,mahasiswa_id,jenis_pembayaran,angsuran_ke,nominal,status_bayar'])
            ->orderBy('nama_mahasiswa')
            ->get();
        $selectedMahasiswa = $request->query('mahasiswa_id');
        $statuses = $this->statuses;
        $jenisPembayarans = $this->jenisPembayarans;

        $usedAngsuran = $mahasiswas->mapWithKeys(fn (Mahasiswa $mahasiswa) => [
            $mahasiswa->id => $mahasiswa->pembayarans
                ->where('jenis_pembayaran', 'Angsuran')
                ->pluck('angsuran_ke')
                ->filter()
                ->values(),
        ]);

        $infoByMahasiswa = $mahasiswas->mapWithKeys(fn (Mahasiswa $mahasiswa) => [
            $mahasiswa->id => [
                'Angsuran' => [
                    'harga_kesepakatan' => (float) $mahasiswa->harga_kesepakatan,
                    'sudah_dibayar' => $mahasiswa->totalDibayarMahasiswa(),
                    'tunggakan' => max(0, $mahasiswa->totalTagihan()),
                    'sudah_lunas' => $mahasiswa->sudahLunas(),
                ],
                'Wisuda' => [
                    'status' => $mahasiswa->statusPembayaranJenis('Wisuda'),
                    'referensi' => $mahasiswa->resolvedBiayaWisuda(),
                ],
                'Almamater' => [
                    'status' => $mahasiswa->statusPembayaranJenis('Almamater'),
                    'referensi' => $mahasiswa->resolvedBiayaAlmamater(),
                ],
            ],
        ]);

        return view('pembayaran.create', compact('mahasiswas', 'selectedMahasiswa', 'statuses', 'jenisPembayarans', 'usedAngsuran', 'infoByMahasiswa'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswas,id'],
            'nominal' => ['required', 'array'],
            'nominal.*' => ['nullable', 'numeric', 'min:0'],
            'angsuran_ke' => ['nullable', 'integer', 'min:1', 'max:99'],
            'tanggal_bayar' => ['required', 'date'],
            'status_bayar' => ['required', 'in:' . implode(',', $this->statuses)],
            'bukti_bayar' => ['nullable', 'file', 'max:5120'],
            'catatan' => ['nullable', 'string'],
        ]);

        $items = collect($validated['nominal'])
            ->only($this->jenisPembayarans)
            ->filter(fn ($nominal) => $nominal !== null && $nominal !== '' && (float) $nominal > 0);

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'nominal' => 'Isi nominal untuk setidaknya satu jenis pembayaran.',
            ]);
        }

        if ($items->has('Angsuran') && blank($validated['angsuran_ke'] ?? null)) {
            throw ValidationException::withMessages([
                'angsuran_ke' => 'Pilih angsuran ke berapa untuk Biaya Pendidikan.',
            ]);
        }

        $mahasiswa = Mahasiswa::findOrFail($validated['mahasiswa_id']);

        $buktiBayarPath = null;

        if ($request->hasFile('bukti_bayar')) {
            $buktiBayarPath = $request->file('bukti_bayar')->store('pembayaran/' . $mahasiswa->kode_pmb, 'public');
        }

        $inputBy = auth()->id();
        $isVerified = $validated['status_bayar'] === 'terverifikasi';
        $first = null;

        DB::transaction(function () use ($items, $mahasiswa, $validated, $buktiBayarPath, $inputBy, $isVerified, &$first) {
            foreach ($items as $jenis => $nominal) {
                $payload = [
                    'mahasiswa_id' => $mahasiswa->id,
                    'jenis_pembayaran' => $jenis,
                    'angsuran_ke' => $jenis === 'Angsuran' ? $validated['angsuran_ke'] : null,
                    'tanggal_bayar' => $validated['tanggal_bayar'],
                    'nominal' => $nominal,
                    'status_bayar' => $validated['status_bayar'],
                    'bukti_bayar_path' => $buktiBayarPath,
                    'catatan' => $validated['catatan'] ?? null,
                    'input_by' => $inputBy,
                ];

                if ($isVerified) {
                    $payload['verified_by'] = $inputBy;
                    $payload['verified_at'] = now();
                }

                $pembayaran = Pembayaran::create($payload);
                $first ??= $pembayaran;
            }
        });

        $message = $items->count() > 1
            ? $items->count() . ' pembayaran (' . $items->keys()->implode(', ') . ') berhasil disimpan.'
            : 'Pembayaran manual berhasil disimpan.';

        return redirect()->route('pembayaran.show', $first)->with('success', $message);
    }

    public function show(Pembayaran $pembayaran): View
    {
        $pembayaran->load(['mahasiswa.kampus', 'mahasiswa.jurusan', 'inputBy', 'verifiedBy']);

        return view('pembayaran.show', compact('pembayaran'));
    }

    public function edit(Pembayaran $pembayaran): View
    {
        $pembayaran->load('mahasiswa.kampus');
        $statuses = $this->statuses;

        return view('pembayaran.edit', compact('pembayaran', 'statuses'));
    }

    public function update(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $validated = $request->validate([
            'nominal' => ['required', 'numeric', 'min:0'],
            'status_bayar' => ['required', 'in:' . implode(',', $this->statuses)],
            'bukti_bayar' => ['nullable', 'file', 'max:5120'],
            'catatan' => ['nullable', 'string'],
        ]);

        $pembayaran->loadMissing('mahasiswa');

        if ($request->hasFile('bukti_bayar')) {
            if ($pembayaran->bukti_bayar_path) {
                Storage::disk('public')->delete($pembayaran->bukti_bayar_path);
            }

            $validated['bukti_bayar_path'] = $request->file('bukti_bayar')->store('pembayaran/' . $pembayaran->mahasiswa->kode_pmb, 'public');
            $validated['bukti_bayar_drive_url'] = null;
        }

        unset($validated['bukti_bayar']);

        if ($validated['status_bayar'] === 'terverifikasi' && $pembayaran->status_bayar !== 'terverifikasi') {
            $validated['verified_by'] = auth()->id();
            $validated['verified_at'] = now();
        }

        $pembayaran->update($validated);

        return redirect()->route('pembayaran.show', $pembayaran)->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Pembayaran $pembayaran): RedirectResponse
    {
        if ($pembayaran->bukti_bayar_path) {
            Storage::disk('public')->delete($pembayaran->bukti_bayar_path);
        }

        $pembayaran->delete();

        return redirect()->route('pembayaran.index')->with('success', 'Data pembayaran berhasil dihapus.');
    }

    public function viewFile(Pembayaran $pembayaran, string $field = 'bukti_bayar'): BinaryFileResponse|RedirectResponse
    {
        abort_unless(isset($this->fileFields[$field]), 404);

        $meta = $this->fileFields[$field];
        $path = $pembayaran->{$meta['column']};

        abort_if(blank($path), 404, 'File belum tersedia.');

        if (Storage::disk('public')->exists($path)) {
            return response()->file(Storage::disk('public')->path($path));
        }

        $driveFileUrl = $meta['drive_url_column'] ? $pembayaran->{$meta['drive_url_column']} : null;

        if (filled($driveFileUrl)) {
            return redirect()->away($driveFileUrl);
        }

        $pembayaran->loadMissing('mahasiswa');
        $driveFolderUrl = $pembayaran->mahasiswa->google_drive_pembayaran_folder_url
            ?: $pembayaran->mahasiswa->google_drive_folder_url;

        abort_if(blank($driveFolderUrl), 404, 'File sudah di-backup ke Google Drive tetapi link folder tidak ditemukan.');

        return redirect()->away($driveFolderUrl);
    }
}
