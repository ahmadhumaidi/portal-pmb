<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KampusController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $kampuses = Kampus::query()
            ->withCount('mahasiswas')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('nama_kampus', 'like', "%{$search}%")
                        ->orWhere('kode_kampus', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        return view('master.kampus.index', compact('kampuses', 'search'));
    }

    public function create(): View
    {
        return view('master.kampus.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kampus' => [
                'required',
                'string',
                'max:255',
                'unique:kampuses,nama_kampus',
            ],
            'harga' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif');

        Kampus::create($validated);

        return redirect()
            ->route('kampus.index')
            ->with('success', 'Data kampus berhasil ditambahkan.');
    }

    public function edit(Kampus $kampus): View
    {
        return view('master.kampus.edit', compact('kampus'));
    }

    public function update(
        Request $request,
        Kampus $kampus
    ): RedirectResponse {
        $validated = $request->validate([
            'nama_kampus' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kampuses', 'nama_kampus')
                    ->ignore($kampus->id),
            ],
            'harga' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif');

        $kampus->update($validated);

        return redirect()
            ->route('kampus.index')
            ->with('success', 'Data kampus berhasil diperbarui.');
    }

    public function destroy(Kampus $kampus): RedirectResponse
    {
        if ($kampus->jurusans()->exists()) {
            return redirect()
                ->route('kampus.index')
                ->with('error', 'Kampus tidak dapat dihapus karena masih memiliki jurusan.');
        }

        $kampus->delete();

        return redirect()
            ->route('kampus.index')
            ->with('success', 'Data kampus berhasil dihapus.');
    }
}