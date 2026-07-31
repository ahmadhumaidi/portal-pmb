<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KoordinatorHasilController extends Controller
{
    public function index(Request $request): View
    {
        $koordinator = Auth::guard('koordinator')->user();

        $search = trim((string) $request->query('search'));

        $mahasiswas = $koordinator->mahasiswas()
            ->with(['kampus', 'jurusan'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_mahasiswa', 'like', "%{$search}%")
                        ->orWhere('kode_pmb', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_mahasiswa')
            ->paginate(20)
            ->withQueryString();

        return view('portal.koordinator.hasil', [
            'koordinator' => $koordinator,
            'mahasiswas' => $mahasiswas,
            'search' => $search,
        ]);
    }
}
