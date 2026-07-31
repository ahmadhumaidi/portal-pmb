<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PengumumanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $pengumumans = Pengumuman::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('judul', 'like', "%{$search}%")
                        ->orWhere('isi', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        return view('master.pengumuman.index', compact('pengumumans', 'search'));
    }

    public function create(): View
    {
        return view('master.pengumuman.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'tanggal' => ['nullable', 'date'],
            'status_aktif' => ['nullable', 'boolean'],
            'lampiran' => ['nullable', 'file', 'mimes:jpg,jpeg,pdf', 'max:5120'],
        ]);

        $validated['tanggal'] = $validated['tanggal'] ?? now()->toDateString();
        $validated['status_aktif'] = $request->boolean('status_aktif', true);
        $validated['input_by'] = auth()->id();

        if ($request->hasFile('lampiran')) {
            $validated['lampiran_path'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        unset($validated['lampiran']);

        Pengumuman::create($validated);

        return redirect()
            ->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Pengumuman $pengumuman): View
    {
        return view('master.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'tanggal' => ['nullable', 'date'],
            'status_aktif' => ['nullable', 'boolean'],
            'lampiran' => ['nullable', 'file', 'mimes:jpg,jpeg,pdf', 'max:5120'],
        ]);

        $validated['tanggal'] = $validated['tanggal'] ?? now()->toDateString();
        $validated['status_aktif'] = $request->boolean('status_aktif');

        if ($request->hasFile('lampiran')) {
            if ($pengumuman->lampiran_path) {
                Storage::disk('public')->delete($pengumuman->lampiran_path);
            }

            $validated['lampiran_path'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        unset($validated['lampiran']);

        $pengumuman->update($validated);

        return redirect()
            ->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman): RedirectResponse
    {
        if ($pengumuman->lampiran_path) {
            Storage::disk('public')->delete($pengumuman->lampiran_path);
        }

        $pengumuman->delete();

        return redirect()
            ->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function viewFile(Pengumuman $pengumuman): BinaryFileResponse
    {
        abort_if(blank($pengumuman->lampiran_path), 404, 'Lampiran belum tersedia.');
        abort_unless(Storage::disk('public')->exists($pengumuman->lampiran_path), 404);

        return response()->file(Storage::disk('public')->path($pengumuman->lampiran_path));
    }
}
