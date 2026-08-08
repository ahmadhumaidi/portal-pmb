<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class KoordinatorHasilController extends Controller
{
    private array $fileFields = [
        'screenshot_pisn' => 'screenshot_pisn_path',
        'scan_ijazah' => 'scan_ijazah_path',
        'scan_transkrip' => 'scan_transkrip_path',
    ];

    public function index(Request $request): View
    {
        $koordinator = Auth::guard('koordinator')->user();

        $search = trim((string) $request->query('search'));

        $mahasiswas = $koordinator->mahasiswas()
            ->with(['kampus', 'jurusan', 'hasil', 'pembayarans'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_mahasiswa', 'like', "%{$search}%")
                        ->orWhere('kode_pmb', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_mahasiswa')
            ->paginate(20)
            ->withQueryString();

        $pengumumans = Pengumuman::where('status_aktif', true)->orderByDesc('tanggal')->get();

        return view('portal.koordinator.hasil', [
            'koordinator' => $koordinator,
            'mahasiswas' => $mahasiswas,
            'search' => $search,
            'pengumumans' => $pengumumans,
        ]);
    }

    public function viewFile(Mahasiswa $mahasiswa, string $field): BinaryFileResponse|RedirectResponse
    {
        $koordinator = Auth::guard('koordinator')->user();

        abort_unless($mahasiswa->koordinator_id === $koordinator->id, 403);
        abort_unless(isset($this->fileFields[$field]), 404);

        $hasil = $mahasiswa->hasil;
        abort_if(! $hasil, 404);

        $column = $this->fileFields[$field];
        $path = $hasil->{$column};

        abort_if(blank($path), 404, 'File belum tersedia.');

        if (Storage::disk('public')->exists($path)) {
            return response()->file(Storage::disk('public')->path($path));
        }

        $driveFolderUrl = $mahasiswa->google_drive_folder_url;

        abort_if(blank($driveFolderUrl), 404, 'File belum tersedia.');

        return redirect()->away($driveFolderUrl);
    }
}
