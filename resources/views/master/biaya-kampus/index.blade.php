@extends('layouts.app')

@section('title', 'Biaya Kampus')
@section('page-title', 'Biaya Kampus')
@section('page-description', 'Kelola biaya yang harus disetorkan ke masing-masing kampus mitra')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card dashboard-card border-0">

    <div class="card-header flex-wrap gap-3">
        <div>
            <h5>Daftar Biaya Kampus</h5>
            <small>Biaya yang harus disetor ke kampus, per kampus dan prodi</small>
        </div>

        <a href="{{ route('biaya-kampus.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Biaya Kampus
        </a>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('biaya-kampus.index') }}" class="row g-2 mb-4">
            <div class="col-md-5">
                <input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari kode, kampus, atau prodi...">
            </div>

            <div class="col-md-4">
                <select name="kampus_id" class="form-select">
                    <option value="">Semua kampus</option>
                    @foreach ($kampuses as $kampus)
                        <option value="{{ $kampus->id }}" @selected(($kampusId ?? '') == $kampus->id)>{{ $kampus->nama_kampus }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search me-1"></i>Cari</button>
                <a href="{{ route('biaya-kampus.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">No.</th>
                        <th style="width: 130px;">Kode</th>
                        <th>Kampus</th>
                        <th>Berlaku Untuk</th>
                        <th>Biaya Pendidikan</th>
                        <th>Biaya Wisuda</th>
                        <th>Biaya Almamater</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($biayaKampus as $item)
                        <tr>
                            <td>{{ $biayaKampus->firstItem() + $loop->index }}</td>
                            <td><span class="badge text-bg-light border text-dark">{{ $item->kode_biaya_kampus }}</span></td>
                            <td>{{ $item->kampus->nama_kampus ?? '-' }}</td>
                            <td>
                                @if ($item->jurusan)
                                    {{ $item->jurusan->nama_jurusan }}
                                @else
                                    <span class="badge text-bg-info">Semua Prodi</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($item->biaya, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->biaya_wisuda, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->biaya_almamater, 0, ',', '.') }}</td>
                            <td>
                                @if ($item->status_aktif)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('biaya-kampus.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('biaya-kampus.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus aturan biaya ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-cash-coin fs-1 d-block mb-2"></i>
                                Belum ada data biaya kampus.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            @include('partials.per-page-select')
            {{ $biayaKampus->links() }}
        </div>

    </div>
</div>

@endsection
