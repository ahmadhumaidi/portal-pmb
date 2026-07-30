<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\Hasil;
use App\Models\Jurusan;
use App\Models\Kampus;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    private array $statuses = ['baru', 'proses', 'berkas_kurang', 'siap_registrasi', 'terdaftar', 'selesai', 'dibatalkan'];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $mahasiswas = Mahasiswa::query()
            ->with([
                'kampus',
                'jurusan',
                'picStaff',
                'pembayarans:id,mahasiswa_id,status_bayar,nominal',
                'setoranKampus:id,mahasiswa_id,nominal',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('nama_mahasiswa', 'like', "%{$search}%")
                        ->orWhere('kode_pmb', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nomor_whatsapp', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('nama_ibu', 'like', "%{$search}%")
                        ->orWhere('asal_sekolah', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status_pendaftaran', $status))
            ->latest()
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $statuses = $this->statuses;

        return view('mahasiswa.index', compact('mahasiswas', 'search', 'status', 'statuses'));
    }

    public function create(Request $request): View
    {
        $cameFromOcr = session('ocr_draft_active');
        $isValidationRetry = $request->session()->has('errors');

        if (!$cameFromOcr && !$isValidationRetry) {
            $this->clearOcrDraft();
        }

        return view('mahasiswa.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMahasiswa($request);
        $validated['harga_kesepakatan'] = $validated['harga_kesepakatan'] ?? 0;

        $ocrDocuments = session('ocr_draft.documents', []);

        $berkas = null;

        DB::transaction(function () use ($validated, $ocrDocuments, &$berkas) {
            $mahasiswa = Mahasiswa::create($validated);
            $inputBy = auth()->id();

            $berkas = Berkas::firstOrCreate(
                ['mahasiswa_id' => $mahasiswa->id],
                array_merge(
                    ['input_by' => $inputBy, 'status_verifikasi' => 'belum_upload'],
                    $this->moveOcrDocuments($mahasiswa->kode_pmb, $ocrDocuments)
                )
            );

            Hasil::firstOrCreate(['mahasiswa_id' => $mahasiswa->id], ['input_by' => $inputBy, 'status_kirim' => 'belum_siap']);
        });

        $this->clearOcrDraft();

        return redirect()->route('berkas.edit', $berkas)->with('success', 'Data mahasiswa berhasil ditambahkan. Silakan lengkapi berkas.');
    }

    /**
     * Move documents uploaded during the AI OCR flow into this mahasiswa's berkas
     * directory so staff don't have to re-upload what they already fed to the OCR.
     */
    private function moveOcrDocuments(string $kodePmb, array $ocrDocuments): array
    {
        $columnByType = ['ktp' => 'ktp_path', 'kk' => 'kk_path', 'ijazah' => 'ijazah_path'];
        $files = [];

        foreach ($columnByType as $jenis => $column) {
            $path = $ocrDocuments[$jenis]['path'] ?? null;

            if ($path && Storage::disk('public')->exists($path)) {
                $destination = 'berkas/' . $kodePmb . '/' . basename($path);
                Storage::disk('public')->move($path, $destination);
                $files[$column] = $destination;
            }
        }

        return $files;
    }

    private function clearOcrDraft(): void
    {
        $token = session('ocr_draft.token');

        if ($token) {
            Storage::disk('public')->deleteDirectory('ocr-temp/' . $token);
        }

        session()->forget('ocr_draft');
    }

    public function show(Mahasiswa $mahasiswa): View
    {
        $mahasiswa->load(['kampus', 'jurusan', 'picStaff', 'koordinator', 'berkas', 'pembayarans', 'setoranKampus', 'hasil']);

        return view('mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        return view('mahasiswa.edit', array_merge($this->formData(), compact('mahasiswa')));
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $validated = $this->validateMahasiswa($request);
        $validated['harga_kesepakatan'] = $validated['harga_kesepakatan'] ?? 0;

        $mahasiswa->update($validated);

        return redirect()->route('mahasiswa.show', $mahasiswa)->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        DB::transaction(function () use ($mahasiswa) {
            $mahasiswa->loadMissing(['berkas', 'pembayarans', 'setoranKampus', 'hasil']);

            if ($mahasiswa->berkas) {
                foreach (['ijazah_path', 'ktp_path', 'kk_path', 'pas_foto_path'] as $column) {
                    if ($mahasiswa->berkas->{$column}) {
                        Storage::disk('public')->delete($mahasiswa->berkas->{$column});
                    }
                }
                $mahasiswa->berkas->delete();
            }

            foreach ($mahasiswa->pembayarans as $pembayaran) {
                if ($pembayaran->bukti_bayar_path) {
                    Storage::disk('public')->delete($pembayaran->bukti_bayar_path);
                }
                $pembayaran->delete();
            }

            foreach ($mahasiswa->setoranKampus as $setoran) {
                if ($setoran->bukti_setor_path) {
                    Storage::disk('public')->delete($setoran->bukti_setor_path);
                }
                $setoran->delete();
            }

            if ($mahasiswa->hasil) {
                foreach (['screenshot_pisn_path', 'screenshot_satudikti_path', 'scan_ijazah_path', 'scan_transkrip_path'] as $column) {
                    if ($mahasiswa->hasil->{$column}) {
                        Storage::disk('public')->delete($mahasiswa->hasil->{$column});
                    }
                }
                $mahasiswa->hasil->delete();
            }

            $mahasiswa->delete();
        });

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa beserta berkas dan pembayarannya berhasil dihapus.');
    }

    public function trash(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $mahasiswas = Mahasiswa::onlyTrashed()
            ->with(['kampus', 'jurusan'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('nama_mahasiswa', 'like', "%{$search}%")
                        ->orWhere('kode_pmb', 'like', "%{$search}%");
                });
            })
            ->latest('deleted_at')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        return view('mahasiswa.trash', compact('mahasiswas', 'search'));
    }

    public function restore(Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->restore();

        return redirect()->route('mahasiswa.trash')->with('success', 'Data mahasiswa berhasil dipulihkan.');
    }

    public function forceDelete(Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->forceDelete();

        return redirect()->route('mahasiswa.trash')->with('success', 'Data mahasiswa berhasil dihapus permanen.');
    }

    private function formData(): array
    {
        return [
            'kampuses' => Kampus::query()->where('status_aktif', true)->orderBy('nama_kampus')->get(),
            'jurusans' => Jurusan::query()->with('kampus')->where('status_aktif', true)->orderBy('nama_jurusan')->get(),
            'staffs' => Staff::query()->where('status_aktif', true)->orderBy('nama_staff')->get(),
            'koordinators' => Koordinator::query()->where('status_aktif', true)->orderBy('nama_koordinator')->get(),
            'statuses' => $this->statuses,
        ];
    }

    private function validateMahasiswa(Request $request): array
    {
        return $request->validate([
            'kampus_id' => ['required', 'exists:kampuses,id'],
            'jurusan_id' => [
                'required',
                Rule::exists('jurusans', 'id')->where(fn ($query) => $query->where('kampus_id', $request->input('kampus_id'))),
            ],
            'pic_staff_id' => ['nullable', 'exists:staff,id'],
            'nama_mahasiswa' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:30'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'agama' => ['nullable', 'string', 'max:255'],
            'kewarganegaraan' => ['nullable', 'string', 'max:255'],
            'nomor_whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'asal_sekolah' => ['nullable', 'string', 'max:255'],
            'koordinator_id' => ['nullable', 'exists:koordinators,id'],
            'tahun_lulus' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'harga_kesepakatan' => ['nullable', 'numeric', 'min:0'],
            'status_pendaftaran' => ['required', 'in:' . implode(',', $this->statuses)],
            'keterangan' => ['nullable', 'string'],
        ]);
    }
}


