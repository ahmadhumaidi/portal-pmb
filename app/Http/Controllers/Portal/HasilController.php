<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Hasil;
use App\Models\Pengumuman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HasilController extends Controller
{
    private array $fileFields = [
        'screenshot_pisn' => 'screenshot_pisn_path',
        'scan_ijazah' => 'scan_ijazah_path',
        'scan_transkrip' => 'scan_transkrip_path',
    ];

    public function index(): View
    {
        $mahasiswa = Auth::guard('mahasiswa')->user()->load('hasil');
        $pengumumans = Pengumuman::where('status_aktif', true)->orderByDesc('tanggal')->get();

        return view('portal.mahasiswa.hasil', ['mahasiswa' => $mahasiswa, 'pengumumans' => $pengumumans]);
    }

    public function viewFile(string $field): BinaryFileResponse|RedirectResponse
    {
        abort_unless(isset($this->fileFields[$field]), 404);

        $mahasiswa = Auth::guard('mahasiswa')->user()->load('hasil');
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
