@extends('layouts.app')

@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran')
@section('page-description', 'Pantau pembayaran mahasiswa dan status verifikasinya')

@section('content')
<div class="card dashboard-card border-0"><div class="card-header flex-wrap gap-3"><div><h5>Daftar Pembayaran</h5><small>Riwayat transaksi pembayaran mahasiswa</small></div><a href="{{ route('pembayaran.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Input Pembayaran</a></div><div class="card-body">
<form method="GET" action="{{ route('pembayaran.index') }}" class="row g-2 mb-4"><div class="col-md-7"><input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari kode, nama, NIK, bukti bayar, atau catatan..."></div><div class="col-md-3"><select name="status" class="form-select"><option value="">Semua status</option>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(($status ?? '') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div><div class="col-md-2 d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Cari</button><a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary">Reset</a></div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>No.</th><th>Kode</th><th>Mahasiswa</th><th>Angsuran</th><th>Tanggal</th><th>Nominal</th><th>Status</th><th>Bukti</th><th></th></tr></thead><tbody>
@forelse ($pembayarans as $pembayaran)<tr><td>{{ $pembayarans->firstItem() + $loop->index }}</td><td><span class="badge text-bg-light border text-dark">{{ $pembayaran->kode_pembayaran }}</span></td><td><div>{{ $pembayaran->mahasiswa->nama_mahasiswa ?? '-' }}</div><div class="small text-muted">{{ $pembayaran->mahasiswa->kode_pmb ?? '-' }}</div></td><td>{{ $pembayaran->angsuran_ke ?? $pembayaran->jenis_pembayaran }}</td><td>{{ optional($pembayaran->tanggal_bayar)->format('d M Y') ?? '-' }}</td><td>Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td><td><span class="badge text-bg-info">{{ ucfirst(str_replace('_', ' ', $pembayaran->status_bayar)) }}</span></td><td class="small">{{ $pembayaran->bukti_bayar_path ? 'Ada' : '-' }}</td><td class="d-flex gap-1"><a href="{{ route('pembayaran.show', $pembayaran) }}" class="btn btn-sm btn-outline-primary">Detail</a><a href="{{ route('pembayaran.edit', $pembayaran) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a><form action="{{ route('pembayaran.destroy', $pembayaran) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data pembayaran ini?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>@empty<tr><td colspan="9" class="text-center py-5 text-muted">Belum ada data pembayaran.</td></tr>@endforelse
</tbody></table></div><div class="mt-3">{{ $pembayarans->links() }}</div></div></div>
@endsection


