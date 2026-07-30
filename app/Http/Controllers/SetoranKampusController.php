<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\SetoranKampus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SetoranKampusController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $setoranKampus = SetoranKampus::query()
            ->with('mahasiswa.kampus')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('kode_setoran_kampus', 'like', "%{$search}%")
                        ->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($search) {
                            $mahasiswaQuery
                                ->where('nama_mahasiswa', 'like', "%{$search}%")
                                ->orWhere('kode_pmb', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('tanggal_setor')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        return view('setoran-kampus.index', compact('setoranKampus', 'search'));
    }

    public function create(Request $request): View
    {
        $mahasiswas = Mahasiswa::query()->with('kampus')->orderBy('nama_mahasiswa')->get();
        $selectedMahasiswa = $request->query('mahasiswa_id');

        return view('setoran-kampus.create', compact('mahasiswas', 'selectedMahasiswa'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswas,id'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'tanggal_setor' => ['required', 'date'],
            'bukti_setor' => ['nullable', 'file', 'max:5120'],
            'catatan' => ['nullable', 'string'],
        ]);

        $mahasiswa = Mahasiswa::findOrFail($validated['mahasiswa_id']);

        if ($request->hasFile('bukti_setor')) {
            $validated['bukti_setor_path'] = $request->file('bukti_setor')->store('setoran-kampus/' . $mahasiswa->kode_pmb, 'public');
        }

        unset($validated['bukti_setor']);
        $validated['input_by'] = auth()->id();

        $setoranKampus = SetoranKampus::create($validated);

        return redirect()->route('setoran-kampus.show', $setoranKampus)->with('success', 'Setoran ke kampus berhasil disimpan.');
    }

    public function show(SetoranKampus $setoranKampus): View
    {
        $setoranKampus->load(['mahasiswa.kampus', 'inputBy']);

        return view('setoran-kampus.show', compact('setoranKampus'));
    }

    public function edit(SetoranKampus $setoranKampus): View
    {
        $setoranKampus->load('mahasiswa.kampus');

        return view('setoran-kampus.edit', compact('setoranKampus'));
    }

    public function update(Request $request, SetoranKampus $setoranKampus): RedirectResponse
    {
        $validated = $request->validate([
            'nominal' => ['required', 'numeric', 'min:0'],
            'tanggal_setor' => ['required', 'date'],
            'bukti_setor' => ['nullable', 'file', 'max:5120'],
            'catatan' => ['nullable', 'string'],
        ]);

        $setoranKampus->loadMissing('mahasiswa');

        if ($request->hasFile('bukti_setor')) {
            if ($setoranKampus->bukti_setor_path) {
                Storage::disk('public')->delete($setoranKampus->bukti_setor_path);
            }

            $validated['bukti_setor_path'] = $request->file('bukti_setor')->store('setoran-kampus/' . $setoranKampus->mahasiswa->kode_pmb, 'public');
        }

        unset($validated['bukti_setor']);

        $setoranKampus->update($validated);

        return redirect()->route('setoran-kampus.show', $setoranKampus)->with('success', 'Setoran ke kampus berhasil diperbarui.');
    }

    public function destroy(SetoranKampus $setoranKampus): RedirectResponse
    {
        if ($setoranKampus->bukti_setor_path) {
            Storage::disk('public')->delete($setoranKampus->bukti_setor_path);
        }

        $setoranKampus->delete();

        return redirect()->route('setoran-kampus.index')->with('success', 'Setoran ke kampus berhasil dihapus.');
    }

    public function viewFile(SetoranKampus $setoranKampus): BinaryFileResponse
    {
        abort_if(blank($setoranKampus->bukti_setor_path), 404, 'File belum tersedia.');

        abort_unless(Storage::disk('public')->exists($setoranKampus->bukti_setor_path), 404, 'File tidak ditemukan.');

        return response()->file(Storage::disk('public')->path($setoranKampus->bukti_setor_path));
    }
}
