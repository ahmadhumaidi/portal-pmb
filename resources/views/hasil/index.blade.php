@extends('layouts.app')

@section('title', 'Hasil')
@section('page-title', 'Hasil')
@section('page-description', 'Pantau hasil kelulusan dan pengiriman dokumen akhir')

@section('content')
<div class="card dashboard-card border-0"><div class="card-header flex-wrap gap-3"><div><h5>Daftar Hasil</h5><small>Status NIM, ijazah, dokumen, dan pengiriman hasil mahasiswa</small></div></div><div class="card-body">
<form method="GET" action="{{ route('hasil.index') }}" class="row g-2 mb-4"><div class="col-md-7"><input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari kode, nama, NIM, seri ijazah, status, atau link..."></div><div class="col-md-3"><select name="status" class="form-select"><option value="">Semua status kirim</option>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(($status ?? '') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div><div class="col-md-2 d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Cari</button><a href="{{ route('hasil.index') }}" class="btn btn-outline-secondary">Reset</a></div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>No.</th><th>Kode</th><th>Mahasiswa</th><th>Status</th><th>NIM</th><th>No. Seri</th><th class="text-center">PISN</th><th class="text-center">Satudikti</th><th class="text-center">Ijazah</th><th class="text-center">Transkrip</th><th>Status Kirim</th><th></th></tr></thead><tbody>
@forelse ($hasils as $hasil)
<tr>
<td>{{ $hasils->firstItem() + $loop->index }}</td>
<td><span class="badge text-bg-light border text-dark">{{ $hasil->kode_hasil }}</span></td>
<td><div>{{ $hasil->mahasiswa->nama_mahasiswa ?? '-' }}</div><div class="small text-muted">{{ $hasil->mahasiswa->kode_pmb ?? '-' }}</div></td>
<td>{{ $hasil->status_kelulusan ?? '-' }}</td>
<td>{{ $hasil->nim ?? '-' }}</td>
<td>{{ $hasil->nomor_seri_ijazah ?? '-' }}</td>
<td class="text-center">@if ($hasil->screenshot_pisn_path)<i class="bi bi-check-circle-fill text-success"></i>@else<span class="text-muted">-</span>@endif</td>
<td class="text-center">@if ($hasil->screenshot_satudikti_path)<i class="bi bi-check-circle-fill text-success"></i>@else<span class="text-muted">-</span>@endif</td>
<td class="text-center">@if ($hasil->scan_ijazah_path)<i class="bi bi-check-circle-fill text-success"></i>@else<span class="text-muted">-</span>@endif</td>
<td class="text-center">@if ($hasil->scan_transkrip_path)<i class="bi bi-check-circle-fill text-success"></i>@else<span class="text-muted">-</span>@endif</td>
<td><span class="badge text-bg-info">{{ ucfirst(str_replace('_', ' ', $hasil->status_kirim)) }}</span></td>
<td><a href="{{ route('hasil.show', $hasil) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
</tr>
@empty<tr><td colspan="12" class="text-center py-5 text-muted">Belum ada data hasil.</td></tr>@endforelse
</tbody></table></div><div class="mt-3">{{ $hasils->links() }}</div></div></div>
@endsection
