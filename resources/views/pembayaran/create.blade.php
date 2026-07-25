@extends('layouts.app')

@section('title', 'Input Pembayaran')
@section('page-title', 'Input Pembayaran Manual')
@section('page-description', 'Catat pembayaran mahasiswa secara manual')

@section('content')
<div class="row justify-content-center"><div class="col-xl-8"><div class="card dashboard-card border-0"><div class="card-header"><div><h5>Form Pembayaran</h5><small>Isi transaksi pembayaran mahasiswa</small></div></div><div class="card-body">
<form method="POST" action="{{ route('pembayaran.store') }}" enctype="multipart/form-data">
@csrf
<div class="row g-3">
<div class="col-12"><label for="mahasiswa_id" class="form-label">Mahasiswa</label><select id="mahasiswa_id" name="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror" required><option value="">Pilih Mahasiswa</option>@foreach ($mahasiswas as $mahasiswa)<option value="{{ $mahasiswa->id }}" @selected(old('mahasiswa_id', $selectedMahasiswa) == $mahasiswa->id)>{{ $mahasiswa->kode_pmb }} - {{ $mahasiswa->nama_mahasiswa }} ({{ $mahasiswa->kampus->nama_kampus ?? '-' }})</option>@endforeach</select>@error('mahasiswa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-6"><label for="jenis_pembayaran" class="form-label">Jenis Pembayaran</label><input type="text" id="jenis_pembayaran" name="jenis_pembayaran" class="form-control" value="{{ old('jenis_pembayaran', 'Angsuran') }}" required></div>
<div class="col-md-6"><label for="angsuran_ke" class="form-label">Angsuran Ke</label><input type="number" id="angsuran_ke" name="angsuran_ke" min="1" class="form-control" value="{{ old('angsuran_ke') }}"></div>
<div class="col-md-6"><label for="tanggal_bayar" class="form-label">Tanggal Bayar</label><input type="date" id="tanggal_bayar" name="tanggal_bayar" class="form-control" value="{{ old('tanggal_bayar', now()->toDateString()) }}" required></div>
<div class="col-md-6"><label for="nominal" class="form-label">Nominal</label><input type="number" id="nominal" name="nominal" min="0" step="1" class="form-control" value="{{ old('nominal', 0) }}" required></div>
<div class="col-md-6"><label for="status_bayar" class="form-label">Status Bayar</label><select id="status_bayar" name="status_bayar" class="form-select" required>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(old('status_bayar', 'menunggu') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div>
<div class="col-md-6"><label for="bukti_bayar" class="form-label">Bukti Bayar</label><input type="file" id="bukti_bayar" name="bukti_bayar" class="form-control"></div>
<div class="col-12"><label for="catatan" class="form-label">Catatan</label><textarea id="catatan" name="catatan" rows="3" class="form-control">{{ old('catatan') }}</textarea></div>
</div>
<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('pembayaran.index') }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-wallet2 me-1"></i>Simpan Pembayaran</button></div>
</form>
</div></div></div></div>
@endsection
