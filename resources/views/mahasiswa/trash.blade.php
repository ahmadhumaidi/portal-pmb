@extends('layouts.app')

@section('title', 'Sampah Mahasiswa')
@section('page-title', 'Sampah Mahasiswa')
@section('page-description', 'Data mahasiswa yang sudah dihapus, bisa dipulihkan atau dihapus permanen')

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
            <h5>Sampah Mahasiswa</h5>
            <small>Data yang sudah dihapus, masih tersimpan sampai dihapus permanen</small>
        </div>
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Mahasiswa</a>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('mahasiswa.trash') }}" class="row g-2 mb-4">
            <div class="col-md-9">
                <input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari nama atau kode PMB...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary" type="submit">Cari</button>
                <a href="{{ route('mahasiswa.trash') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode PMB</th>
                        <th>Nama</th>
                        <th>Kampus</th>
                        <th>Jurusan</th>
                        <th>Dihapus Pada</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mahasiswas as $mahasiswa)
                        <tr>
                            <td>{{ $mahasiswas->firstItem() + $loop->index }}</td>
                            <td><span class="badge text-bg-light border text-dark">{{ $mahasiswa->kode_pmb }}</span></td>
                            <td>{{ $mahasiswa->nama_mahasiswa }}</td>
                            <td>{{ $mahasiswa->kampus->nama_kampus ?? '-' }}</td>
                            <td>{{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</td>
                            <td>{{ optional($mahasiswa->deleted_at)->format('d M Y H:i') }}</td>
                            <td class="d-flex gap-1">
                                <form action="{{ route('mahasiswa.restore', $mahasiswa) }}" method="POST" class="d-inline" onsubmit="return confirm('Pulihkan data mahasiswa ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">Pulihkan</button>
                                </form>
                                <form action="{{ route('mahasiswa.force-delete', $mahasiswa) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus PERMANEN data mahasiswa ini? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus Permanen</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">Sampah kosong.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $mahasiswas->links() }}</div>
    </div>
</div>

@endsection
