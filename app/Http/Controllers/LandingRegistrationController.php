<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\LandingRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LandingRegistrationController extends Controller
{
    public function index(): View
    {
        $registrations = LandingRegistration::query()
            ->with(['kampus', 'jurusan'])
            ->latest()
            ->paginate(20);

        return view('landing.registrations', compact('registrations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'whatsapp' => ['required', 'regex:/^[0-9+\\-\\s]{9,20}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'school' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'education_level' => ['required', Rule::in(['SMA', 'SMK', 'MA', 'Paket C', 'Lainnya'])],
            'kampus_id' => ['required', 'exists:kampuses,id'],
            'jurusan_id' => [
                'required',
                Rule::exists('jurusans', 'id')->where(
                    fn ($query) => $query->where('kampus_id', $request->integer('kampus_id'))->where('status_aktif', true)
                ),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
            'consent' => ['accepted'],
        ], [
            'jurusan_id.exists' => 'Program studi tidak sesuai dengan kampus yang dipilih.',
            'consent.accepted' => 'Persetujuan pemrosesan data wajib dicentang.',
        ]);

        unset($data['consent']);
        LandingRegistration::create($data);

        return redirect()->route('landing', ['registered' => 1])
            ->with('registration_success', 'Pendaftaran awal berhasil dikirim. Tim admisi akan menghubungi kamu melalui WhatsApp.');
    }
}
