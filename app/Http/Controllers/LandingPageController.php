<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Models\LandingPage;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function show(): View
    {
        $landingPage = LandingPage::query()->first();
        $content = array_merge(LandingPage::defaults(), $landingPage?->toArray() ?? []);
        $kampuses = Kampus::query()
            ->where('status_aktif', true)
            ->with(['jurusans' => fn ($query) => $query->where('status_aktif', true)->orderBy('nama_jurusan')])
            ->orderBy('nama_kampus')
            ->get();

        return view('landing.show', compact('content', 'kampuses'));
    }
}
