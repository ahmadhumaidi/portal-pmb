<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MahasiswaAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('portal.mahasiswa.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'kode_pmb' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('mahasiswa')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('kode_pmb'))
                ->withErrors(['kode_pmb' => 'Nomor seleksi atau password salah.']);
        }

        $request->session()->regenerate();

        return redirect()->route('portal.mahasiswa.hasil');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('mahasiswa')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.mahasiswa.login');
    }
}
