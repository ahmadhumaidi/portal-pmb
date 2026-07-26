@extends('layouts.app')

@section('title', 'Input Setoran ke Kampus')
@section('page-title', 'Input Setoran ke Kampus')
@section('page-description', 'Catat uang yang sudah disetorkan ke kampus mitra')

@section('content')
<div class="row justify-content-center"><div class="col-xl-8">
<form method="POST" action="{{ route('setoran-kampus.store') }}" enctype="multipart/form-data">
@csrf
@php $selectedMahasiswaModel = $mahasiswas->firstWhere('id', (int) old('mahasiswa_id', $selectedMahasiswa)); @endphp

<div class="card dashboard-card border-0 mb-4">
    <div class="card-header"><div><h5>Setoran ke Kampus Mitra</h5><small>Uang yang sudah disetorkan ke kampus mitra, terpisah dari catatan pembayaran mahasiswa</small></div></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12"><label for="mahasiswa_display" class="form-label">Mahasiswa</label><input type="text" id="mahasiswa_display" class="form-control @error('mahasiswa_id') is-invalid @enderror" list="mahasiswa_list" autocomplete="off" placeholder="Ketik nama atau kode PMB mahasiswa" value="{{ $selectedMahasiswaModel ? $selectedMahasiswaModel->kode_pmb . ' - ' . $selectedMahasiswaModel->nama_mahasiswa . ' (' . ($selectedMahasiswaModel->kampus->nama_kampus ?? '-') . ')' : '' }}" required><datalist id="mahasiswa_list">@foreach ($mahasiswas as $mahasiswa)<option data-id="{{ $mahasiswa->id }}" value="{{ $mahasiswa->kode_pmb }} - {{ $mahasiswa->nama_mahasiswa }} ({{ $mahasiswa->kampus->nama_kampus ?? '-' }})"></option>@endforeach</datalist><input type="hidden" id="mahasiswa_id" name="mahasiswa_id" value="{{ $selectedMahasiswaModel->id ?? '' }}">@error('mahasiswa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="nominal" class="form-label">Nominal Disetor ke Kampus</label><input type="number" id="nominal" name="nominal" min="0" step="1" class="form-control @error('nominal') is-invalid @enderror" placeholder="Masukkan nominal" value="{{ old('nominal') }}" required>@error('nominal')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="tanggal_setor" class="form-label">Tanggal Setor</label><input type="date" id="tanggal_setor" name="tanggal_setor" class="form-control @error('tanggal_setor') is-invalid @enderror" value="{{ old('tanggal_setor', now()->toDateString()) }}" required>@error('tanggal_setor')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="bukti_setor" class="form-label">Bukti Setor ke Kampus</label><input type="file" id="bukti_setor" name="bukti_setor" class="form-control @error('bukti_setor') is-invalid @enderror">@error('bukti_setor')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label for="catatan" class="form-label">Catatan</label><textarea id="catatan" name="catatan" rows="3" class="form-control">{{ old('catatan') }}</textarea></div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mb-4"><a href="{{ route('setoran-kampus.index') }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-cash-coin me-1"></i>Simpan Setoran</button></div>
</form>
</div></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const display = document.getElementById('mahasiswa_display');
    const hidden = document.getElementById('mahasiswa_id');
    const options = Array.from(document.querySelectorAll('#mahasiswa_list option'));

    display.addEventListener('input', function () {
        const match = options.find(function (option) {
            return option.value === display.value;
        });

        hidden.value = match ? match.dataset.id : '';
    });
});
</script>
@endsection
