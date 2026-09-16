<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use App\Models\Kampus;
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HasilController extends Controller
{
    private array $statuses = ['belum_siap', 'siap_dikirim', 'sudah_dikirim', 'sudah_diterima', 'perlu_revisi'];

    private array $kelulusanStatuses = ['Input SIAKAD', 'Input NeoFeeder', 'Online Pddikti', 'LULUS Pddikti'];

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
        $kampusId = $request->query('kampus_id');
        $jurusanId = $request->query('jurusan_id');

        $hasils = Hasil::query()
            ->select('hasils.*')
            ->join('mahasiswas', 'mahasiswas.id', '=', 'hasils.mahasiswa_id')
            ->with(['mahasiswa.kampus', 'mahasiswa.jurusan', 'mahasiswa.pembayarans'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('hasils.kode_hasil', 'like', "%{$search}%")
                        ->orWhere('hasils.nim', 'like', "%{$search}%")
                        ->orWhere('hasils.nomor_seri_ijazah', 'like', "%{$search}%")
                        ->orWhere('hasils.status_kelulusan', 'like', "%{$search}%")
                        ->orWhere('hasils.link_pddikti', 'like', "%{$search}%")
                        ->orWhere('hasils.keterangan', 'like', "%{$search}%")
                        ->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($search) {
                            $mahasiswaQuery
                                ->where('nama_mahasiswa', 'like', "%{$search}%")
                                ->orWhere('kode_pmb', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, fn ($query) => $query->where('hasils.status_kirim', $status))
            ->when($kampusId, fn ($query) => $query->where('mahasiswas.kampus_id', $kampusId))
            ->when($jurusanId, fn ($query) => $query->where('mahasiswas.jurusan_id', $jurusanId))
            ->orderBy('mahasiswas.kode_pmb')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $statuses = $this->statuses;
        $kelulusanStatuses = $this->kelulusanStatuses;
        $kampuses = Kampus::query()->where('status_aktif', true)->orderBy('nama_kampus')->get();
        $jurusans = Jurusan::query()
            ->with('kampus:id,nama_kampus')
            ->where('status_aktif', true)
            ->when($kampusId, fn ($query) => $query->where('kampus_id', $kampusId))
            ->orderBy('nama_jurusan')
            ->get();

        return view('hasil.index', compact('hasils', 'search', 'status', 'kampusId', 'jurusanId', 'kampuses', 'jurusans', 'statuses', 'kelulusanStatuses'));
    }
    public function show(Hasil $hasil): View
    {
        $hasil->load(['mahasiswa.kampus', 'mahasiswa.jurusan', 'mahasiswa.pembayarans', 'inputBy']);

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
        $kelulusanStatuses = $this->kelulusanStatuses;

        return view('hasil.edit', compact('hasil', 'statuses', 'kelulusanStatuses'));
    }

    public function update(Request $request, Hasil $hasil): RedirectResponse
    {
        $validated = $request->validate([
            'status_kelulusan' => ['nullable', 'in:' . implode(',', $this->kelulusanStatuses)],
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
            'return_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $returnTo = $validated['return_to'] ?? null;
        unset($validated['return_to']);

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

        if ($returnTo && str_starts_with($returnTo, $request->getSchemeAndHttpHost())) {
            return redirect()->to($returnTo)->with('success', 'Hasil mahasiswa berhasil diperbarui.');
        }

        return redirect()->back()->with('success', 'Hasil mahasiswa berhasil diperbarui.');
    }
}
