@extends('layouts.app')

@section('title', 'Hasil')
@section('page-title', 'Hasil')
@section('page-description', 'Pantau hasil kelulusan dan pengiriman dokumen akhir')

@section('content')
<div class="card dashboard-card border-0"><div class="card-header flex-wrap gap-3"><div><h5>Daftar Hasil</h5><small>Status NIM, ijazah, dokumen, dan pengiriman hasil mahasiswa &mdash; bisa diedit langsung di tabel</small></div></div><div class="card-body">
<form method="GET" action="{{ route('hasil.index') }}" class="row g-2 mb-4"><div class="col-md-7"><input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari kode, nama, NIM, seri ijazah, status, atau link..."></div><div class="col-md-3"><select name="status" class="form-select"><option value="">Semua status kirim</option>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(($status ?? '') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div><div class="col-md-2 d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Cari</button><a href="{{ route('hasil.index') }}" class="btn btn-outline-secondary">Reset</a></div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>No.</th><th>Kode</th><th>Mahasiswa</th><th style="min-width:140px">Status</th><th style="min-width:120px">NIM</th><th style="min-width:120px">No. Seri</th><th style="min-width:150px">PISN</th><th style="min-width:150px">Link PDDIKTI</th><th style="min-width:150px">Ijazah</th><th style="min-width:150px">Transkrip</th><th style="min-width:150px">Status Kirim</th><th></th></tr></thead><tbody>
@forelse ($hasils as $hasil)
<tr>
<td>{{ $hasils->firstItem() + $loop->index }}</td>
<td><span class="badge text-bg-light border text-dark">{{ $hasil->kode_hasil }}</span></td>
<td><div>{{ $hasil->mahasiswa->nama_mahasiswa ?? '-' }}</div><div class="small text-muted">{{ $hasil->mahasiswa->kode_pmb ?? '-' }}</div></td>
<td>
    <select form="hasil-form-{{ $hasil->id }}" name="status_kelulusan" class="form-select form-select-sm">
        <option value="">-</option>
        @foreach ($kelulusanStatuses as $option)<option value="{{ $option }}" @selected($hasil->status_kelulusan === $option)>{{ $option }}</option>@endforeach
    </select>
</td>
<td><input form="hasil-form-{{ $hasil->id }}" type="text" name="nim" value="{{ $hasil->nim }}" class="form-control form-control-sm"></td>
<td><input form="hasil-form-{{ $hasil->id }}" type="text" name="nomor_seri_ijazah" value="{{ $hasil->nomor_seri_ijazah }}" class="form-control form-control-sm"></td>
<td>
    <input form="hasil-form-{{ $hasil->id }}" type="file" name="screenshot_pisn" class="form-control form-control-sm">
    @if ($hasil->screenshot_pisn_path)<div class="small text-success mt-1"><i class="bi bi-check-circle-fill"></i> Sudah ada</div>@endif
</td>
<td><input form="hasil-form-{{ $hasil->id }}" type="url" name="link_pddikti" value="{{ $hasil->link_pddikti }}" class="form-control form-control-sm" placeholder="https://"></td>
<td>
    <input form="hasil-form-{{ $hasil->id }}" type="file" name="scan_ijazah" class="form-control form-control-sm">
    @if ($hasil->scan_ijazah_path)<div class="small text-success mt-1"><i class="bi bi-check-circle-fill"></i> Sudah ada</div>@endif
</td>
<td>
    <input form="hasil-form-{{ $hasil->id }}" type="file" name="scan_transkrip" class="form-control form-control-sm">
    @if ($hasil->scan_transkrip_path)<div class="small text-success mt-1"><i class="bi bi-check-circle-fill"></i> Sudah ada</div>@endif
</td>
<td>
    <select form="hasil-form-{{ $hasil->id }}" name="status_kirim" class="form-select form-select-sm" required>
        @foreach ($statuses as $option)<option value="{{ $option }}" @selected($hasil->status_kirim === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach
    </select>
</td>
<td><button form="hasil-form-{{ $hasil->id }}" type="submit" class="btn btn-sm btn-primary">Simpan</button></td>
</tr>
@empty<tr><td colspan="12" class="text-center py-5 text-muted">Belum ada data hasil.</td></tr>@endforelse
</tbody></table></div><div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">@include('partials.per-page-select'){{ $hasils->links() }}</div></div></div>

@foreach ($hasils as $hasil)
<form id="hasil-form-{{ $hasil->id }}" method="POST" action="{{ route('hasil.update', $hasil) }}" enctype="multipart/form-data" class="d-none">
    @csrf
    @method('PUT')
</form>
@endforeach
@endsection
