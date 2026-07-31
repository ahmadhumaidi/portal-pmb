<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HasilController extends Controller
{
    public function index(): View
    {
        $mahasiswa = Auth::guard('mahasiswa')->user()->load(['kampus', 'jurusan', 'koordinator']);

        return view('portal.mahasiswa.hasil', ['mahasiswa' => $mahasiswa]);
    }
}
