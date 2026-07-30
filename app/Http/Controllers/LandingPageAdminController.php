<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingPageAdminController extends Controller
{
    public function edit(): View
    {
        $landingPage = LandingPage::query()->first();
        $content = array_merge(LandingPage::defaults(), $landingPage?->toArray() ?? []);

        return view('landing.edit', compact('content'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'badge' => ['nullable', 'string', 'max:150'],
            'headline' => ['required', 'string', 'max:180'],
            'subheadline' => ['nullable', 'string', 'max:600'],
            'primary_button_text' => ['nullable', 'string', 'max:60'],
            'primary_button_url' => ['nullable', 'string', 'max:255'],
            'secondary_button_text' => ['nullable', 'string', 'max:60'],
            'secondary_button_url' => ['nullable', 'string', 'max:255'],
            'announcement' => ['nullable', 'string', 'max:300'],
            'registration_status' => ['nullable', 'string', 'max:80'],
            'registration_deadline' => ['nullable', 'date'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'features' => ['nullable', 'array', 'max:6'],
            'features.*.title' => ['required_with:features', 'string', 'max:80'],
            'features.*.description' => ['required_with:features', 'string', 'max:240'],
            'steps' => ['nullable', 'array', 'max:6'],
            'steps.*.title' => ['required_with:steps', 'string', 'max:80'],
            'steps.*.description' => ['required_with:steps', 'string', 'max:240'],
            'faqs' => ['nullable', 'array', 'max:8'],
            'faqs.*.question' => ['required_with:faqs', 'string', 'max:180'],
            'faqs.*.answer' => ['required_with:faqs', 'string', 'max:500'],
        ]);

        $defaults = LandingPage::defaults();
        $data['features'] = collect($data['features'] ?? [])->map(
            fn (array $feature, int $index) => $feature + ['icon' => $defaults['features'][$index]['icon'] ?? 'bi-stars']
        )->values()->all();

        LandingPage::query()->updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'Landing page berhasil diperbarui.');
    }
}
