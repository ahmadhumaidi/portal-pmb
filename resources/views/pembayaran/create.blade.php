@extends('layouts.app')

@section('title', 'Input Pembayaran')
@section('page-title', 'Input Pembayaran Manual')
@section('page-description', 'Catat pembayaran mahasiswa secara manual')

@section('content')
<div class="row justify-content-center"><div class="col-xl-8">
<form method="POST" action="{{ route('pembayaran.store') }}" enctype="multipart/form-data">
@csrf
@php $selectedMahasiswaModel = $mahasiswas->firstWhere('id', (int) old('mahasiswa_id', $selectedMahasiswa)); @endphp

<div class="card dashboard-card border-0 mb-4">
    <div class="card-header"><div><h5>Pembayaran dari Mahasiswa</h5><small>Uang yang diterima dari mahasiswa</small></div></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12"><label for="mahasiswa_display" class="form-label">Mahasiswa</label><input type="text" id="mahasiswa_display" class="form-control @error('mahasiswa_id') is-invalid @enderror" list="mahasiswa_list" autocomplete="off" placeholder="Ketik nama atau kode PMB mahasiswa" value="{{ $selectedMahasiswaModel ? $selectedMahasiswaModel->kode_pmb . ' - ' . $selectedMahasiswaModel->nama_mahasiswa . ' (' . ($selectedMahasiswaModel->kampus->nama_kampus ?? '-') . ')' : '' }}" required><datalist id="mahasiswa_list">@foreach ($mahasiswas as $mahasiswa)<option data-id="{{ $mahasiswa->id }}" value="{{ $mahasiswa->kode_pmb }} - {{ $mahasiswa->nama_mahasiswa }} ({{ $mahasiswa->kampus->nama_kampus ?? '-' }})"></option>@endforeach</datalist><input type="hidden" id="mahasiswa_id" name="mahasiswa_id" value="{{ $selectedMahasiswaModel->id ?? '' }}">@error('mahasiswa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="angsuran_ke" class="form-label">Angsuran Ke</label><select id="angsuran_ke" name="angsuran_ke" class="form-select @error('angsuran_ke') is-invalid @enderror" required>@for ($i = 1; $i <= 12; $i++)<option value="{{ $i }}" @selected((string) old('angsuran_ke') === (string) $i)>Angsuran ke-{{ $i }}</option>@endfor</select>@error('angsuran_ke')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="tanggal_bayar" class="form-label">Tanggal Bayar</label><input type="date" id="tanggal_bayar" name="tanggal_bayar" class="form-control" value="{{ old('tanggal_bayar', now()->toDateString()) }}" required></div>
            <div class="col-md-6"><label for="nominal" class="form-label">Nominal</label><input type="number" id="nominal" name="nominal" min="0" step="1" class="form-control" placeholder="Masukkan nominal" value="{{ old('nominal') }}" required></div>
            <div class="col-md-6"><label for="status_bayar" class="form-label">Status Bayar</label><select id="status_bayar" name="status_bayar" class="form-select" required>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(old('status_bayar', 'menunggu') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div>
            <div class="col-md-6"><label for="bukti_bayar" class="form-label">Bukti Bayar dari Mahasiswa</label><input type="file" id="bukti_bayar" name="bukti_bayar" class="form-control"></div>
            <div class="col-12"><label for="catatan" class="form-label">Catatan</label><textarea id="catatan" name="catatan" rows="3" class="form-control">{{ old('catatan') }}</textarea></div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mb-4"><a href="{{ route('pembayaran.index') }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-wallet2 me-1"></i>Simpan Pembayaran</button></div>
</form>
</div></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const display = document.getElementById('mahasiswa_display');
    const hidden = document.getElementById('mahasiswa_id');
    const options = Array.from(document.querySelectorAll('#mahasiswa_list option'));
    const angsuranSelect = document.getElementById('angsuran_ke');
    const angsuranOptions = Array.from(angsuranSelect.options).filter(function (option) {
        return option.value !== '';
    });
    const usedAngsuranByMahasiswa = @json($usedAngsuran);

    function refreshAngsuranOptions() {
        const used = usedAngsuranByMahasiswa[hidden.value] || [];

        angsuranOptions.forEach(function (option) {
            option.disabled = used.includes(parseInt(option.value, 10));
        });

        const nextAvailable = angsuranOptions.find(function (option) {
            return !option.disabled;
        });

        angsuranSelect.value = nextAvailable ? nextAvailable.value : '';
    }

    display.addEventListener('input', function () {
        const match = options.find(function (option) {
            return option.value === display.value;
        });

        hidden.value = match ? match.dataset.id : '';
        refreshAngsuranOptions();
    });

    refreshAngsuranOptions();
});
</script>
@endsection
