@extends('layouts.app')

@section('title', 'Master Jurusan')
@section('page-title', 'Master Jurusan')
@section('page-description', 'Kelola jurusan pada setiap kampus')

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

<div class="card dashboard-card border-0">

    <div class="card-header flex-wrap gap-3">
        <div>
            <h5>Daftar Jurusan</h5>
            <small>Jurusan yang tersedia pada kampus mitra</small>
        </div>

        <a href="{{ route('jurusan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Jurusan
        </a>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('jurusan.index') }}" class="row g-2 mb-4">
            <div class="col-md-5">
                <input
                    type="search"
                    name="search"
                    class="form-control"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari kode, jurusan, atau kampus..."
                >
            </div>

            <div class="col-md-4">
                <select name="kampus_id" class="form-select">
                    <option value="">Semua kampus</option>

                    @foreach ($kampuses as $kampus)
                        <option
                            value="{{ $kampus->id }}"
                            @selected(($kampusId ?? '') == $kampus->id)
                        >
                            {{ $kampus->nama_kampus }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>

                <a href="{{ route('jurusan.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">No.</th>
                        <th style="width: 130px;">Kode</th>
                        <th>Jurusan</th>
                        <th>Jenjang</th>
                        <th>Kampus</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($jurusans as $jurusan)
                        <tr>
                            <td>
                                {{ $jurusans->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <span class="badge text-bg-light border text-dark">
                                    {{ $jurusan->kode_jurusan }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $jurusan->nama_jurusan }}
                                </div>
                            </td>

                            <td>
                                {{ $jurusan->jenjang ?: '-' }}
                            </td>

                            <td>
                                {{ $jurusan->kampus->nama_kampus }}
                            </td>

                            <td>
                                @if ($jurusan->status_aktif)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <a
                                    href="{{ route('jurusan.edit', $jurusan) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('jurusan.destroy', $jurusan) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Hapus jurusan ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-mortarboard fs-1 d-block mb-2"></i>
                                Belum ada data jurusan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $jurusans->links() }}
        </div>

    </div>
</div>

@endsection


