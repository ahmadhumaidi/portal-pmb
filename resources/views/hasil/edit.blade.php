@extends('layouts.app')

@section('title', 'Update Hasil')
@section('page-title', 'Update Hasil')
@section('page-description', $hasil->mahasiswa->nama_mahasiswa ?? 'Update hasil mahasiswa')

@section('content')
<div class="row justify-content-center"><div class="col-xl-9"><div class="card dashboard-card border-0"><div class="card-header"><div><h5>Form Update Hasil</h5><small>Perbarui data kelulusan, dokumen akhir, dan pengiriman</small></div></div><div class="card-body">
<form method="POST" action="{{ route('hasil.update', $hasil) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label for="status_kelulusan" class="form-label">Status</label><select id="status_kelulusan" name="status_kelulusan" class="form-select"><option value="">-</option>@foreach ($kelulusanStatuses as $option)<option value="{{ $option }}" @selected(old('status_kelulusan', $hasil->status_kelulusan) === $option)>{{ $option }}</option>@endforeach</select></div>
<div class="col-md-3"><label for="nim" class="form-label">NIM</label><input type="text" id="nim" name="nim" class="form-control" value="{{ old('nim', $hasil->nim) }}"></div>
<div class="col-md-3"><label for="nomor_seri_ijazah" class="form-label">No. Seri Ijazah</label><input type="text" id="nomor_seri_ijazah" name="nomor_seri_ijazah" class="form-control" value="{{ old('nomor_seri_ijazah', $hasil->nomor_seri_ijazah) }}"></div>
<div class="col-12"><label for="link_pddikti" class="form-label">Link PDDIKTI</label><input type="url" id="link_pddikti" name="link_pddikti" class="form-control" value="{{ old('link_pddikti', $hasil->link_pddikti) }}"></div>
@foreach ([
'screenshot_pisn' => ['label' => 'Screenshot PISN', 'path' => $hasil->screenshot_pisn_path],
'screenshot_satudikti' => ['label' => 'Screenshot Satudikti', 'path' => $hasil->screenshot_satudikti_path],
'scan_ijazah' => ['label' => 'Scan Ijazah', 'path' => $hasil->scan_ijazah_path],
'scan_transkrip' => ['label' => 'Scan Transkrip', 'path' => $hasil->scan_transkrip_path],
] as $field => $meta)
<div class="col-md-6"><label for="{{ $field }}" class="form-label">{{ $meta['label'] }}</label><input type="file" id="{{ $field }}" name="{{ $field }}" class="form-control">@if ($meta['path'])<div class="small text-muted mt-1 file-path">Saat ini: {{ $meta['path'] }}</div>@endif</div>
@endforeach
<div class="col-md-4"><label for="status_kirim" class="form-label">Status Kirim</label><select id="status_kirim" name="status_kirim" class="form-select" required>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(old('status_kirim', $hasil->status_kirim) === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div>
<div class="col-md-4"><label for="tanggal_kirim" class="form-label">Tanggal Kirim</label><input type="datetime-local" id="tanggal_kirim" name="tanggal_kirim" class="form-control" value="{{ old('tanggal_kirim', $hasil->tanggal_kirim ? $hasil->tanggal_kirim->format('Y-m-d\\TH:i') : '') }}"></div>
<div class="col-md-4"><label for="metode_kirim" class="form-label">Metode Kirim</label><input type="text" id="metode_kirim" name="metode_kirim" class="form-control" value="{{ old('metode_kirim', $hasil->metode_kirim) }}"></div>
<div class="col-md-6"><label for="nomor_resi" class="form-label">Nomor Resi</label><input type="text" id="nomor_resi" name="nomor_resi" class="form-control" value="{{ old('nomor_resi', $hasil->nomor_resi) }}"></div>
<div class="col-md-6"><label for="keterangan" class="form-label">Keterangan</label><textarea id="keterangan" name="keterangan" rows="3" class="form-control">{{ old('keterangan', $hasil->keterangan) }}</textarea></div>
</div>
<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('hasil.index') }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan Hasil</button></div>
</form>
</div></div></div></div>
@endsection
