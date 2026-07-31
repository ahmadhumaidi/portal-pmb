@extends('layouts.app')

@section('title', 'Hasil')
@section('page-title', 'Hasil')
@section('page-description', 'Pantau hasil kelulusan dan pengiriman dokumen akhir')

@section('content')
<div class="card dashboard-card border-0"><div class="card-header flex-wrap gap-3"><div><h5>Daftar Hasil</h5><small>Status NIM, ijazah, dokumen, dan pengiriman hasil mahasiswa &mdash; klik Edit untuk ubah baris</small></div></div><div class="card-body">
<form method="GET" action="{{ route('hasil.index') }}" class="row g-2 mb-4"><div class="col-md-7"><input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari kode, nama, NIM, seri ijazah, status, atau link..."></div><div class="col-md-3"><select name="status" class="form-select"><option value="">Semua status kirim</option>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(($status ?? '') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div><div class="col-md-2 d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Cari</button><a href="{{ route('hasil.index') }}" class="btn btn-outline-secondary">Reset</a></div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>No.</th><th>Kode</th><th>Mahasiswa</th><th style="min-width:140px">Status</th><th style="min-width:120px">NIM</th><th style="min-width:120px">No. Seri</th><th style="min-width:150px">PISN</th><th style="min-width:150px">Link PDDIKTI</th><th style="min-width:150px">Ijazah</th><th style="min-width:150px">Transkrip</th><th style="min-width:150px">Status Kirim</th><th></th></tr></thead><tbody>
@forelse ($hasils as $hasil)
<tr class="js-hasil-row" data-row-id="{{ $hasil->id }}">
<td>{{ $hasils->firstItem() + $loop->index }}</td>
<td><span class="badge text-bg-light border text-dark">{{ $hasil->kode_hasil }}</span></td>
<td><div>{{ $hasil->mahasiswa->nama_mahasiswa ?? '-' }}</div><div class="small text-muted">{{ $hasil->mahasiswa->kode_pmb ?? '-' }}</div></td>

<td>
    <span class="js-view">{{ $hasil->status_kelulusan ?: '-' }}</span>
    <select form="hasil-form-{{ $hasil->id }}" name="status_kelulusan" class="form-select form-select-sm js-edit d-none">
        <option value="">-</option>
        @foreach ($kelulusanStatuses as $option)<option value="{{ $option }}" @selected($hasil->status_kelulusan === $option)>{{ $option }}</option>@endforeach
    </select>
</td>

<td>
    <span class="js-view">{{ $hasil->nim ?: '-' }}</span>
    <input form="hasil-form-{{ $hasil->id }}" type="text" name="nim" value="{{ $hasil->nim }}" class="form-control form-control-sm js-edit d-none">
</td>

<td>
    <span class="js-view">{{ $hasil->nomor_seri_ijazah ?: '-' }}</span>
    <input form="hasil-form-{{ $hasil->id }}" type="text" name="nomor_seri_ijazah" value="{{ $hasil->nomor_seri_ijazah }}" class="form-control form-control-sm js-edit d-none">
</td>

<td>
    <span class="js-view">@if ($hasil->screenshot_pisn_path)<i class="bi bi-check-circle-fill text-success"></i> Sudah ada@else<span class="text-muted">-</span>@endif</span>
    <input form="hasil-form-{{ $hasil->id }}" type="file" name="screenshot_pisn" class="form-control form-control-sm js-edit d-none">
</td>

<td>
    <span class="js-view">@if ($hasil->link_pddikti)<a href="{{ $hasil->link_pddikti }}" target="_blank" rel="noopener" class="small">Buka</a>@else<span class="text-muted">-</span>@endif</span>
    <input form="hasil-form-{{ $hasil->id }}" type="url" name="link_pddikti" value="{{ $hasil->link_pddikti }}" class="form-control form-control-sm js-edit d-none" placeholder="https://">
</td>

<td>
    <span class="js-view">@if ($hasil->scan_ijazah_path)<i class="bi bi-check-circle-fill text-success"></i> Sudah ada@else<span class="text-muted">-</span>@endif</span>
    <input form="hasil-form-{{ $hasil->id }}" type="file" name="scan_ijazah" class="form-control form-control-sm js-edit d-none">
</td>

<td>
    <span class="js-view">@if ($hasil->scan_transkrip_path)<i class="bi bi-check-circle-fill text-success"></i> Sudah ada@else<span class="text-muted">-</span>@endif</span>
    <input form="hasil-form-{{ $hasil->id }}" type="file" name="scan_transkrip" class="form-control form-control-sm js-edit d-none">
</td>

<td>
    <span class="js-view"><span class="badge text-bg-info">{{ ucfirst(str_replace('_', ' ', $hasil->status_kirim)) }}</span></span>
    <select form="hasil-form-{{ $hasil->id }}" name="status_kirim" class="form-select form-select-sm js-edit d-none" required>
        @foreach ($statuses as $option)<option value="{{ $option }}" @selected($hasil->status_kirim === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach
    </select>
</td>

<td class="text-nowrap">
    <button type="button" class="btn btn-sm btn-outline-primary js-hasil-edit">Edit</button>
    <button type="button" class="btn btn-sm btn-light border js-hasil-cancel d-none">Batal</button>
    <button form="hasil-form-{{ $hasil->id }}" type="submit" class="btn btn-sm btn-primary js-hasil-save d-none">Simpan</button>
</td>
</tr>
@empty<tr><td colspan="12" class="text-center py-5 text-muted">Belum ada data hasil.</td></tr>@endforelse
</tbody></table></div><div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">@include('partials.per-page-select'){{ $hasils->links() }}</div></div></div>

@foreach ($hasils as $hasil)
<form id="hasil-form-{{ $hasil->id }}" method="POST" action="{{ route('hasil.update', $hasil) }}" enctype="multipart/form-data" class="d-none">
    @csrf
    @method('PUT')
</form>
@endforeach

<script>
    function setHasilRowEditing(row, editing) {
        row.querySelectorAll('.js-view').forEach((el) => el.classList.toggle('d-none', editing));
        row.querySelectorAll('.js-edit').forEach((el) => el.classList.toggle('d-none', !editing));
        row.querySelector('.js-hasil-edit')?.classList.toggle('d-none', editing);
        row.querySelector('.js-hasil-cancel')?.classList.toggle('d-none', !editing);
        row.querySelector('.js-hasil-save')?.classList.toggle('d-none', !editing);
    }

    document.addEventListener('click', function (event) {
        const row = event.target.closest('.js-hasil-row');
        if (!row) {
            return;
        }

        if (event.target.matches('.js-hasil-edit')) {
            setHasilRowEditing(row, true);
        } else if (event.target.matches('.js-hasil-cancel')) {
            setHasilRowEditing(row, false);
        }
    });
</script>
@endsection
