@extends('layouts.app')

@section('title', 'Edit Setoran ke Kampus')
@section('page-title', 'Edit Setoran ke Kampus')
@section('page-description', $setoranKampus->mahasiswa->nama_mahasiswa ?? 'Edit setoran ke kampus')

@section('content')
<div class="row justify-content-center"><div class="col-xl-8">
<form method="POST" action="{{ route('setoran-kampus.update', $setoranKampus) }}" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="card dashboard-card border-0 mb-4">
    <div class="card-header"><div><h5>Setoran ke Kampus Mitra</h5><small>Uang yang sudah disetorkan ke kampus mitra</small></div></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12"><label class="form-label">Mahasiswa</label><input type="text" class="form-control" value="{{ $setoranKampus->mahasiswa->kode_pmb }} - {{ $setoranKampus->mahasiswa->nama_mahasiswa }} ({{ $setoranKampus->mahasiswa->kampus->nama_kampus ?? '-' }})" disabled></div>
            <div class="col-md-6"><label class="form-label">Jenis Setoran</label><input type="text" class="form-control" value="{{ $setoranKampus->jenis_setoran }}" disabled></div>
            <div class="col-md-6"><label for="nominal" class="form-label">Nominal Disetor ke Kampus</label><input type="number" id="nominal" name="nominal" min="0" step="1" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal', $setoranKampus->nominal) }}" required>@error('nominal')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="tanggal_setor" class="form-label">Tanggal Setor</label><input type="date" id="tanggal_setor" name="tanggal_setor" class="form-control @error('tanggal_setor') is-invalid @enderror" value="{{ old('tanggal_setor', optional($setoranKampus->tanggal_setor)->format('Y-m-d')) }}" required>@error('tanggal_setor')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6">
                <label for="bukti_setor" class="form-label">Bukti Setor ke Kampus</label>
                <input type="file" id="bukti_setor" name="bukti_setor" class="form-control @error('bukti_setor') is-invalid @enderror">
                @if ($setoranKampus->bukti_setor_path)
                    <div class="small text-muted mt-1 d-flex align-items-center justify-content-between gap-2">
                        <span class="text-truncate">Saat ini: {{ basename($setoranKampus->bukti_setor_path) }}</span>
                        <a href="{{ route('setoran-kampus.view-file', $setoranKampus) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Lihat</a>
                    </div>
                @endif
                @error('bukti_setor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12"><label for="catatan" class="form-label">Catatan</label><textarea id="catatan" name="catatan" rows="3" class="form-control">{{ old('catatan', $setoranKampus->catatan) }}</textarea></div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mb-4"><a href="{{ route('setoran-kampus.show', $setoranKampus) }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan Perubahan</button></div>
</form>
</div></div>
@endsection
