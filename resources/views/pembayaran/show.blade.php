@extends('layouts.app')

@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')
@section('page-description', $pembayaran->mahasiswa->nama_mahasiswa ?? 'Detail pembayaran')

@section('content')
@php
$data = [
'Kode Pembayaran' => $pembayaran->kode_pembayaran,
'Mahasiswa' => $pembayaran->mahasiswa->nama_mahasiswa ?? null,
'Kode PMB' => $pembayaran->mahasiswa->kode_pmb ?? null,
'Kampus' => $pembayaran->mahasiswa->kampus->nama_kampus ?? null,
'Jurusan' => $pembayaran->mahasiswa->jurusan->nama_jurusan ?? null,
'Jenis Pembayaran' => $pembayaran->jenis_pembayaran,
'Angsuran Ke' => $pembayaran->angsuran_ke,
'Tanggal Bayar' => optional($pembayaran->tanggal_bayar)->format('d M Y'),
'Nominal' => 'Rp ' . number_format($pembayaran->nominal, 0, ',', '.'),
'Status Bayar' => ucfirst(str_replace('_', ' ', $pembayaran->status_bayar)),
'Catatan' => $pembayaran->catatan,
'Input By' => $pembayaran->inputBy->name ?? null,
'Verified By' => $pembayaran->verifiedBy->name ?? null,
'Verified At' => optional($pembayaran->verified_at)->format('d M Y H:i'),
'Dibuat Sistem' => optional($pembayaran->created_at)->format('d M Y H:i'),
'Diubah Sistem' => optional($pembayaran->updated_at)->format('d M Y H:i'),
];
@endphp
<a href="{{ route('pembayaran.index') }}" class="btn btn-light border mb-3"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
<div class="card dashboard-card border-0"><div class="card-header"><div><h5>Data Lengkap Pembayaran</h5><small>Data transaksi yang dipakai sistem</small></div></div><div class="card-body"><div class="detail-list">
@foreach ($data as $label => $value)<div class="detail-label">{{ $label }}</div><div class="detail-value">{{ $value ?: '-' }}</div>@endforeach
<div class="detail-label">Bukti Bayar</div>
<div class="detail-value">
@if ($pembayaran->bukti_bayar_path)
<a href="{{ asset('storage/' . $pembayaran->bukti_bayar_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Lihat Bukti</a>
@else
-
@endif
</div>
</div></div></div>
@endsection
