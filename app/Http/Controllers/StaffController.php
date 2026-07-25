<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');
        $role = $request->query('role');

        $staffs = Staff::query()
            ->with('kampus')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('nama_staff', 'like', "%{$search}%")
                        ->orWhere('kode_staff', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('kampus', function ($kampusQuery) use ($search) {
                            $kampusQuery->where('nama_kampus', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status_aktif', (bool) $status);
            })
            ->when($role, function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = ['admin', 'operator', 'keuangan', 'pimpinan'];

        return view('staff.index', compact('staffs', 'search', 'status', 'role', 'roles'));
    }

    public function create(): View
    {
        $kampuses = Kampus::query()
            ->where('status_aktif', true)
            ->orderBy('nama_kampus')
            ->get();

        $roles = ['admin', 'operator', 'keuangan', 'pimpinan'];

        return view('staff.create', compact('kampuses', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kampus_id' => ['nullable', 'exists:kampuses,id'],
            'nama_staff' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:staff,email'],
            'role' => ['required', 'in:admin,operator,keuangan,pimpinan'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif');

        Staff::create($validated);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Data staff berhasil ditambahkan.');
    }
}
