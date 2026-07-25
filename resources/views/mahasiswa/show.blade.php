@extends('layouts.app')

@section('title', 'Detail Mahasiswa')
@section('page-title', 'Detail Mahasiswa')
@section('page-description', $mahasiswa->nama_mahasiswa)

@section('content')
@php
    $data = [
        'Kode PMB' => $mahasiswa->kode_pmb,
        'Nama Mahasiswa' => $mahasiswa->nama_mahasiswa,
        'Kampus' => $mahasiswa->kampus->nama_kampus ?? null,
        'Jurusan' => $mahasiswa->jurusan->nama_jurusan ?? null,
        'PIC Staff' => $mahasiswa->picStaff->nama_staff ?? null,
        'NIK' => $mahasiswa->nik,
        'NISN' => $mahasiswa->nisn,
        'Jenis Kelamin' => $mahasiswa->jenis_kelamin,
        'Tempat Lahir' => $mahasiswa->tempat_lahir,
        'Tanggal Lahir' => optional($mahasiswa->tanggal_lahir)->format('d M Y'),
        'Agama' => $mahasiswa->agama,
        'Kewarganegaraan' => $mahasiswa->kewarganegaraan,
        'No. WhatsApp' => $mahasiswa->nomor_whatsapp,
        'Email' => $mahasiswa->email,
        'Alamat KTP' => $mahasiswa->alamat,
        'Nama Ibu' => $mahasiswa->nama_ibu,
        'Asal Sekolah' => $mahasiswa->asal_sekolah,
        'Tahun Lulus' => $mahasiswa->tahun_lulus,
        'Harga Kesepakatan' => 'Rp ' . number_format($mahasiswa->harga_kesepakatan, 0, ',', '.'),
        'Status Pendaftaran' => ucfirst(str_replace('_', ' ', $mahasiswa->status_pendaftaran)),
        'Keterangan' => $mahasiswa->keterangan,
        'Google Drive Folder ID' => $mahasiswa->google_drive_folder_id,
        'Google Drive Folder URL' => $mahasiswa->google_drive_folder_url,
        'Dibuat Sistem' => optional($mahasiswa->created_at)->format('d M Y H:i'),
        'Diubah Sistem' => optional($mahasiswa->updated_at)->format('d M Y H:i'),
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('mahasiswa.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a><a href="{{ route('mahasiswa.edit', $mahasiswa) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit Data</a>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card dashboard-card border-0">
            <div class="card-header"><div><h5>Data Lengkap Mahasiswa</h5><small>Semua field yang tersedia dari database</small></div></div>
            <div class="card-body">
                <div class="detail-list">
                    @foreach ($data as $label => $value)
                        <div class="detail-label">{{ $label }}</div>
                        <div class="detail-value">{{ $value ?: '-' }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card dashboard-card border-0 mb-4">
            <div class="card-header"><div><h5>Ringkasan Modul</h5><small>Data turunan mahasiswa</small></div></div>
            <div class="card-body d-grid gap-2">
                @if ($mahasiswa->berkas)
                    <a href="{{ route('berkas.show', $mahasiswa->berkas) }}" class="btn btn-outline-primary text-start">Berkas: {{ $mahasiswa->berkas->kode_berkas }}</a><a href="{{ route('berkas.edit', $mahasiswa->berkas) }}" class="btn btn-outline-secondary text-start">Upload Berkas</a>
                @endif
                <a href="{{ route('pembayaran.create', ['mahasiswa_id' => $mahasiswa->id]) }}" class="btn btn-outline-success text-start">Input Pembayaran Manual</a>@foreach ($mahasiswa->pembayarans as $pembayaran)
                    <a href="{{ route('pembayaran.show', $pembayaran) }}" class="btn btn-outline-primary text-start">Pembayaran: {{ $pembayaran->kode_pembayaran }}</a>
                @endforeach
                @if ($mahasiswa->hasil)
                    <a href="{{ route('hasil.show', $mahasiswa->hasil) }}" class="btn btn-outline-primary text-start">Hasil: {{ $mahasiswa->hasil->kode_hasil }}</a><a href="{{ route('hasil.edit', $mahasiswa->hasil) }}" class="btn btn-outline-secondary text-start">Update Hasil</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


