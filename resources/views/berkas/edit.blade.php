@extends('layouts.app')

@section('title', 'Upload Berkas')
@section('page-title', 'Upload Berkas')
@section('page-description', $berkas->mahasiswa->nama_mahasiswa ?? 'Upload dokumen mahasiswa')

@section('content')
<div class="row justify-content-center"><div class="col-xl-8"><div class="card dashboard-card border-0"><div class="card-header"><div><h5>Upload Berkas Mahasiswa</h5><small>Unggah atau perbarui dokumen mahasiswa</small></div></div><div class="card-body">
<form method="POST" action="{{ route('berkas.update', $berkas) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-3">
@foreach ($fileFields as $field => $meta)
@php $path = $berkas->{$meta['column']}; @endphp
<div class="col-md-6"><label class="form-label" for="{{ $field }}">{{ $meta['label'] }}</label><input type="file" id="{{ $field }}" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror">@if ($path)<div class="small text-muted mt-1 d-flex align-items-center justify-content-between gap-2"><span class="text-truncate">Saat ini: {{ basename($path) }}</span><a href="{{ route('berkas.view-file', [$berkas, $field]) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Lihat</a></div>@endif @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endforeach
<div class="col-md-6"><label for="status_verifikasi" class="form-label">Status Verifikasi</label><select id="status_verifikasi" name="status_verifikasi" class="form-select" required>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(old('status_verifikasi', $berkas->status_verifikasi) === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div>
<div class="col-12"><label for="keterangan" class="form-label">Keterangan</label><textarea id="keterangan" name="keterangan" rows="3" class="form-control">{{ old('keterangan', $berkas->keterangan) }}</textarea></div>
</div>
<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('berkas.show', $berkas) }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Simpan Berkas</button></div>
</form>
</div></div></div></div>
@endsection
