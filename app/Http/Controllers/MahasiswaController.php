<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\Hasil;
use App\Models\Jurusan;
use App\Models\Kampus;
use App\Models\Mahasiswa;
use App\Models\Pembayaran;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->with(['kampus', 'jurusan', 'picStaff'])
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
            ->paginate(10)
            ->withQueryString();

        $statuses = $this->statuses;

        return view('mahasiswa.index', compact('mahasiswas', 'search', 'status', 'statuses'));
    }

    public function create(): View
    {
        return view('mahasiswa.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMahasiswa($request);
        $validated['harga_kesepakatan'] = $validated['harga_kesepakatan'] ?? 0;

        DB::transaction(function () use ($validated) {
            $mahasiswa = Mahasiswa::create($validated);
            $inputBy = auth()->id();

            Berkas::firstOrCreate(['mahasiswa_id' => $mahasiswa->id], ['input_by' => $inputBy, 'status_verifikasi' => 'belum_upload']);
            Pembayaran::firstOrCreate([
                'mahasiswa_id' => $mahasiswa->id,
                'jenis_pembayaran' => 'Pendaftaran',
            ], [
                'input_by' => $inputBy,
                'tanggal_bayar' => now()->toDateString(),
                'nominal' => $validated['harga_kesepakatan'],
                'status_bayar' => 'menunggu',
            ]);
            Hasil::firstOrCreate(['mahasiswa_id' => $mahasiswa->id], ['input_by' => $inputBy, 'status_kirim' => 'belum_siap']);
        });

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function show(Mahasiswa $mahasiswa): View
    {
        $mahasiswa->load(['kampus', 'jurusan', 'picStaff', 'berkas', 'pembayarans', 'hasil']);

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

    private function formData(): array
    {
        return [
            'kampuses' => Kampus::query()->where('status_aktif', true)->orderBy('nama_kampus')->get(),
            'jurusans' => Jurusan::query()->with('kampus')->where('status_aktif', true)->orderBy('nama_jurusan')->get(),
            'staffs' => Staff::query()->where('status_aktif', true)->orderBy('nama_staff')->get(),
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
            'tahun_lulus' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'harga_kesepakatan' => ['nullable', 'numeric', 'min:0'],
            'status_pendaftaran' => ['required', 'in:' . implode(',', $this->statuses)],
            'keterangan' => ['nullable', 'string'],
        ]);
    }
}


