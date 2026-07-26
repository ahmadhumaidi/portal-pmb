@extends('layouts.app')

@section('title', 'Detail Setoran ke Kampus')
@section('page-title', 'Detail Setoran ke Kampus')
@section('page-description', $setoranKampus->mahasiswa->nama_mahasiswa ?? 'Detail setoran ke kampus')

@section('content')
@php
$data = [
'Kode Setoran' => $setoranKampus->kode_setoran_kampus,
'Mahasiswa' => $setoranKampus->mahasiswa->nama_mahasiswa ?? null,
'Kode PMB' => $setoranKampus->mahasiswa->kode_pmb ?? null,
'Kampus' => $setoranKampus->mahasiswa->kampus->nama_kampus ?? null,
'Nominal Disetor' => 'Rp ' . number_format($setoranKampus->nominal, 0, ',', '.'),
'Tanggal Setor' => optional($setoranKampus->tanggal_setor)->format('d M Y'),
'Catatan' => $setoranKampus->catatan,
'Input By' => $setoranKampus->inputBy->name ?? null,
'Dibuat Sistem' => optional($setoranKampus->created_at)->format('d M Y H:i'),
'Diubah Sistem' => optional($setoranKampus->updated_at)->format('d M Y H:i'),
];
@endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('setoran-kampus.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <a href="{{ route('setoran-kampus.edit', $setoranKampus) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit Setoran</a>
</div>

<div class="card dashboard-card border-0">
    <div class="card-header"><div><h5>Setoran ke Kampus Mitra</h5><small>Uang yang sudah disetorkan ke kampus mitra</small></div></div>
    <div class="card-body"><div class="detail-list">
    @foreach ($data as $label => $value)<div class="detail-label">{{ $label }}</div><div class="detail-value">{{ $value ?: '-' }}</div>@endforeach
    <div class="detail-label">Bukti Setor</div>
    <div class="detail-value">
    @if ($setoranKampus->bukti_setor_path)
    <a href="{{ route('setoran-kampus.view-file', $setoranKampus) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Lihat Bukti</a>
    @else
    -
    @endif
    </div>
    </div></div>
</div>
@endsection
