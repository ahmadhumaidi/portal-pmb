<?php

namespace App\Http\Controllers;

use App\Models\Koordinator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KoordinatorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $koordinators = Koordinator::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('nama_koordinator', 'like', "%{$search}%")
                        ->orWhere('kode_koordinator', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        return view('master.koordinator.index', compact('koordinators', 'search'));
    }

    public function create(): View
    {
        return view('master.koordinator.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama_koordinator' => [
                'required',
                'string',
                'max:255',
                'unique:koordinators,nama_koordinator',
            ],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif', true);

        $koordinator = Koordinator::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $koordinator->id,
                'nama_koordinator' => $koordinator->nama_koordinator,
            ], 201);
        }

        return redirect()
            ->route('koordinator.index')
            ->with('success', 'Data koordinator berhasil ditambahkan.');
    }

    public function show(Request $request, Koordinator $koordinator): View
    {
        $showAll = $request->boolean('semua');

        $mahasiswas = $koordinator->mahasiswas()
            ->with(['kampus', 'jurusan'])
            ->orderBy('nama_mahasiswa')
            ->paginate($showAll ? $koordinator->mahasiswas()->count() ?: 1 : 10)
            ->withQueryString();

        return view('master.koordinator.show', compact('koordinator', 'mahasiswas', 'showAll'));
    }

    public function edit(Koordinator $koordinator): View
    {
        return view('master.koordinator.edit', compact('koordinator'));
    }

    public function update(Request $request, Koordinator $koordinator): RedirectResponse
    {
        $validated = $request->validate([
            'nama_koordinator' => [
                'required',
                'string',
                'max:255',
                Rule::unique('koordinators', 'nama_koordinator')
                    ->ignore($koordinator->id),
            ],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif');

        $koordinator->update($validated);

        return redirect()
            ->route('koordinator.index')
            ->with('success', 'Data koordinator berhasil diperbarui.');
    }

    public function destroy(Koordinator $koordinator): RedirectResponse
    {
        if ($koordinator->mahasiswas()->exists()) {
            return redirect()
                ->route('koordinator.index')
                ->with('error', 'Koordinator tidak dapat dihapus karena masih terkait dengan data mahasiswa.');
        }

        $koordinator->delete();

        return redirect()
            ->route('koordinator.index')
            ->with('success', 'Data koordinator berhasil dihapus.');
    }
}
