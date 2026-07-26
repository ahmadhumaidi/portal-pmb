@extends('layouts.app')

@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')
@section('page-description', $pembayaran->mahasiswa->nama_mahasiswa ?? 'Detail pembayaran')

@section('content')
@php
$mahasiswaData = [
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
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('pembayaran.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <div class="d-flex gap-2">
        <a href="{{ route('pembayaran.edit', $pembayaran) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit Pembayaran</a>
        <form action="{{ route('pembayaran.destroy', $pembayaran) }}" method="POST" onsubmit="return confirm('Hapus data pembayaran ini beserta bukti bayarnya?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
        </form>
    </div>
</div>

<div class="card dashboard-card border-0 mb-4">
    <div class="card-header"><div><h5>Pembayaran dari Mahasiswa</h5><small>Uang yang diterima dari mahasiswa</small></div></div>
    <div class="card-body"><div class="detail-list">
    @foreach ($mahasiswaData as $label => $value)<div class="detail-label">{{ $label }}</div><div class="detail-value">{{ $value ?: '-' }}</div>@endforeach
    <div class="detail-label">Bukti Bayar</div>
    <div class="detail-value">
    @if ($pembayaran->bukti_bayar_path)
    <a href="{{ route('pembayaran.view-file', [$pembayaran, 'bukti_bayar']) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Lihat Bukti</a>
    @else
    -
    @endif
    </div>
    </div></div>
</div>
@endsection
