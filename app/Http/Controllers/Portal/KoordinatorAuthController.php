<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KoordinatorAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('portal.koordinator.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'kode_koordinator' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('koordinator')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('kode_koordinator'))
                ->withErrors(['kode_koordinator' => 'Kode koordinator atau password salah.']);
        }

        $request->session()->regenerate();

        return redirect()->route('portal.koordinator.hasil');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('koordinator')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.koordinator.login');
    }
}
