@extends('layouts.app')

@section('title', 'Setoran ke Kampus')
@section('page-title', 'Setoran ke Kampus')
@section('page-description', 'Pantau uang yang sudah disetorkan ke masing-masing kampus mitra')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card dashboard-card border-0"><div class="card-header flex-wrap gap-3"><div><h5>Daftar Setoran ke Kampus</h5><small>Riwayat setoran ke kampus mitra, terpisah dari pembayaran mahasiswa</small></div><a href="{{ route('setoran-kampus.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Input Setoran</a></div><div class="card-body">
<form method="GET" action="{{ route('setoran-kampus.index') }}" class="row g-2 mb-4"><div class="col-md-9"><input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari kode, nama, atau kode PMB..."></div><div class="col-md-3 d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Cari</button><a href="{{ route('setoran-kampus.index') }}" class="btn btn-outline-secondary">Reset</a></div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>No.</th><th>Kode</th><th>Mahasiswa</th><th>Kampus</th><th>Tanggal</th><th>Nominal</th><th>Bukti</th><th></th></tr></thead><tbody>
@forelse ($setoranKampus as $item)<tr><td>{{ $setoranKampus->firstItem() + $loop->index }}</td><td><span class="badge text-bg-light border text-dark">{{ $item->kode_setoran_kampus }}</span></td><td><div>{{ $item->mahasiswa->nama_mahasiswa ?? '-' }}</div><div class="small text-muted">{{ $item->mahasiswa->kode_pmb ?? '-' }}</div></td><td>{{ $item->mahasiswa->kampus->nama_kampus ?? '-' }}</td><td>{{ optional($item->tanggal_setor)->format('d M Y') ?? '-' }}</td><td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td><td class="small">{{ $item->bukti_setor_path ? 'Ada' : '-' }}</td><td class="d-flex gap-1"><a href="{{ route('setoran-kampus.show', $item) }}" class="btn btn-sm btn-outline-primary">Detail</a><a href="{{ route('setoran-kampus.edit', $item) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a></td></tr>@empty<tr><td colspan="8" class="text-center py-5 text-muted">Belum ada data setoran ke kampus.</td></tr>@endforelse
</tbody></table></div><div class="mt-3">{{ $setoranKampus->links() }}</div></div></div>
@endsection
