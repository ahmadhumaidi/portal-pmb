@extends('layouts.app')

@section('title', 'Staff')
@section('page-title', 'Staff')
@section('page-description', 'Kelola data staff dan penanggung jawab operasional')

@section('content')
<div class="card dashboard-card border-0">
    <div class="card-header flex-wrap gap-3">
        <div>
            <h5>Daftar Staff</h5>
            <small>Informasi staff yang terhubung dengan sistem PMB</small>
        </div>
        <a href="{{ route('staff.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Staff
        </a>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('staff.index') }}" class="row g-2 mb-4">
            <div class="col-md-5">
                <input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari nama, kode, email, atau kampus...">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">Semua role</option>
                    @foreach ($roles as $option)
                        <option value="{{ $option }}" @selected(($role ?? '') === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua status</option>
                    <option value="1" @selected(($status ?? '') === '1')>Aktif</option>
                    <option value="0" @selected(($status ?? '') === '0')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline-primary" type="submit">Cari</button>
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Kampus</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staffs as $staff)
                        <tr>
                            <td>{{ $staffs->firstItem() + $loop->index }}</td>
                            <td><span class="badge text-bg-light border text-dark">{{ $staff->kode_staff }}</span></td>
                            <td>{{ $staff->nama_staff }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>{{ ucfirst($staff->role) }}</td>
                            <td>{{ $staff->kampus->nama_kampus ?? '-' }}</td>
                            <td>
                                @if ($staff->status_aktif)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data staff.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            @include('partials.per-page-select')
            {{ $staffs->links() }}
        </div>
    </div>
</div>
@endsection


