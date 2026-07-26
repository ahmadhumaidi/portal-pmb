<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Kampus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JurusanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $kampusId = $request->query('kampus_id');

        $jurusans = Jurusan::query()
            ->with('kampus')
            ->withCount('mahasiswas')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('nama_jurusan', 'like', "%{$search}%")
                        ->orWhere('kode_jurusan', 'like', "%{$search}%")
                        ->orWhereHas('kampus', function ($kampusQuery) use ($search) {
                            $kampusQuery->where(
                                'nama_kampus',
                                'like',
                                "%{$search}%"
                            );
                        });
                });
            })
            ->when($kampusId, function ($query) use ($kampusId) {
                $query->where('kampus_id', $kampusId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kampuses = Kampus::query()
            ->orderBy('nama_kampus')
            ->get();

        return view(
            'master.jurusan.index',
            compact('jurusans', 'kampuses', 'search', 'kampusId')
        );
    }

    public function create(): View
    {
        $kampuses = Kampus::query()
            ->where('status_aktif', true)
            ->orderBy('nama_kampus')
            ->get();

        return view('master.jurusan.create', compact('kampuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kampus_id' => [
                'required',
                'integer',
                'exists:kampuses,id',
            ],
            'nama_jurusan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jurusans', 'nama_jurusan')
                    ->where(fn ($query) => $query->where(
                        'kampus_id',
                        $request->kampus_id
                    )),
            ],
            'jenjang' => [
                'nullable',
                'string',
                'max:20',
            ],
            'status_aktif' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif');

        Jurusan::create($validated);

        return redirect()
            ->route('jurusan.index')
            ->with('success', 'Data jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan): View
    {
        $kampuses = Kampus::query()
            ->orderBy('nama_kampus')
            ->get();

        return view(
            'master.jurusan.edit',
            compact('jurusan', 'kampuses')
        );
    }

    public function update(
        Request $request,
        Jurusan $jurusan
    ): RedirectResponse {
        $validated = $request->validate([
            'kampus_id' => [
                'required',
                'integer',
                'exists:kampuses,id',
            ],
            'nama_jurusan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jurusans', 'nama_jurusan')
                    ->ignore($jurusan->id)
                    ->where(fn ($query) => $query->where(
                        'kampus_id',
                        $request->kampus_id
                    )),
            ],
            'jenjang' => [
                'nullable',
                'string',
                'max:20',
            ],
            'status_aktif' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif');

        $jurusan->update($validated);

        return redirect()
            ->route('jurusan.index')
            ->with('success', 'Data jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        $jurusan->delete();

        return redirect()
            ->route('jurusan.index')
            ->with('success', 'Data jurusan berhasil dihapus.');
    }
}
