@extends('layouts.app')

@section('title', 'Master Koordinator')
@section('page-title', 'Master Koordinator')
@section('page-description', 'Kelola daftar koordinator kelas')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>
        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>
    </div>
@endif

<div class="card dashboard-card border-0">

    <div class="card-header flex-wrap gap-3">
        <div>
            <h5>Daftar Koordinator</h5>
            <small>Data koordinator kelas yang tersedia di Portal PMB</small>
        </div>

        <a href="{{ route('koordinator.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Koordinator
        </a>
    </div>

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('koordinator.index') }}"
            class="mb-4"
        >
            <div class="input-group" style="max-width: 440px;">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>

                <input
                    type="search"
                    name="search"
                    class="form-control"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari kode atau nama koordinator..."
                >

                <button class="btn btn-outline-primary" type="submit">
                    Cari
                </button>

                @if (!empty($search))
                    <a
                        href="{{ route('koordinator.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">No.</th>
                        <th style="width: 130px;">Kode</th>
                        <th>Nama Koordinator</th>
                        <th>Mahasiswa</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 150px;">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($koordinators as $koordinator)
                        <tr>
                            <td>
                                {{ $koordinators->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <span class="badge text-bg-light border text-dark">
                                    {{ $koordinator->kode_koordinator }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $koordinator->nama_koordinator }}
                                </div>
                            </td>

                            <td>
                                {{ $koordinator->mahasiswas()->count() }}
                            </td>

                            <td>
                                @if ($koordinator->status_aktif)
                                    <span class="badge text-bg-success">
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge text-bg-secondary">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">
                                <a
                                    href="{{ route('koordinator.edit', $koordinator) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Edit"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    action="{{ route('koordinator.destroy', $koordinator) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Hapus data koordinator ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Hapus"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-person-badge fs-1 d-block mb-2"></i>
                                Belum ada data koordinator.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $koordinators->links() }}
        </div>

    </div>
</div>

@endsection
