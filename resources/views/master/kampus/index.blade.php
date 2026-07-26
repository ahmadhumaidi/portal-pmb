@extends('layouts.app')

@section('title', 'Master Kampus')
@section('page-title', 'Master Kampus')
@section('page-description', 'Kelola daftar kampus mitra')

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
            <h5>Daftar Kampus</h5>
            <small>Data kampus yang tersedia di Portal PMB</small>
        </div>

        <a href="{{ route('kampus.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Kampus
        </a>
    </div>

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('kampus.index') }}"
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
                    placeholder="Cari kode atau nama kampus..."
                >

                <button class="btn btn-outline-primary" type="submit">
                    Cari
                </button>

                @if (!empty($search))
                    <a
                        href="{{ route('kampus.index') }}"
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
                        <th>Nama Kampus</th>
                        <th>Biaya</th>
                        <th>Jurusan</th>
                        <th>Jumlah Pendaftar</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th class="text-end" style="width: 150px;">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($kampuses as $kampus)
                        <tr>
                            <td>
                                {{ $kampuses->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <span class="badge text-bg-light border text-dark">
                                    {{ $kampus->kode_kampus }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $kampus->nama_kampus }}
                                </div>
                            </td>

                            <td>
                                Rp{{ number_format((float) $kampus->harga, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $kampus->jurusans()->count() }}
                            </td>

                            <td>
                                <span class="badge text-bg-light border text-dark">{{ $kampus->mahasiswas_count }}</span>
                            </td>

                            <td>
                                @if ($kampus->status_aktif)
                                    <span class="badge text-bg-success">
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge text-bg-secondary">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ \Illuminate\Support\Str::limit($kampus->catatan, 45) ?: '-' }}
                            </td>

                            <td class="text-end">
                                <a
                                    href="{{ route('kampus.edit', $kampus) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Edit"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    action="{{ route('kampus.destroy', $kampus) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Hapus data kampus ini?')"
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
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-buildings fs-1 d-block mb-2"></i>
                                Belum ada data kampus.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $kampuses->links() }}
        </div>

    </div>
</div>

@endsection

