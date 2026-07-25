@extends('layouts.app')

@section('title', 'Detail Berkas')
@section('page-title', 'Detail Berkas')
@section('page-description', $berkas->mahasiswa->nama_mahasiswa ?? 'Detail dokumen')

@section('content')
@php
    $info = [
        'Kode Berkas' => $berkas->kode_berkas,
        'Mahasiswa' => $berkas->mahasiswa->nama_mahasiswa ?? null,
        'Kode PMB' => $berkas->mahasiswa->kode_pmb ?? null,
        'Kampus' => $berkas->mahasiswa->kampus->nama_kampus ?? null,
        'Jurusan' => $berkas->mahasiswa->jurusan->nama_jurusan ?? null,
        'Folder Google Drive' => $berkas->mahasiswa->google_drive_folder_url ?? null,
        'Status Verifikasi' => ucfirst(str_replace('_', ' ', $berkas->status_verifikasi)),
        'Keterangan' => $berkas->keterangan,
        'Dibuat Sistem' => optional($berkas->created_at)->format('d M Y H:i'),
        'Diubah Sistem' => optional($berkas->updated_at)->format('d M Y H:i'),
    ];
@endphp
<div class="d-flex justify-content-between align-items-center mb-3"><a href="{{ route('berkas.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a><a href="{{ route('berkas.edit', $berkas) }}" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Upload / Edit Berkas</a></div>
<div class="card dashboard-card border-0 mb-4"><div class="card-header"><div><h5>File Berkas</h5><small>Dokumen mahasiswa yang sudah di-upload</small></div></div><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Dokumen</th><th class="text-end">Aksi</th></tr></thead><tbody>
@foreach ($fileFields as $field => $meta)
@php $path = $berkas->{$meta['column']}; @endphp
<tr><td>{{ $meta['label'] }}</td><td class="text-end">@if ($path)<a href="{{ route('berkas.view-file', [$berkas, $field]) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Lihat</a>@else<span class="text-muted small">Belum upload</span>@endif</td></tr>
@endforeach
</tbody></table></div></div></div>
<div class="card dashboard-card border-0"><div class="card-header"><div><h5>Data Berkas</h5><small>Informasi dokumen yang dipakai sistem</small></div></div><div class="card-body"><div class="detail-list">
@foreach ($info as $label => $value)<div class="detail-label">{{ $label }}</div><div class="detail-value">{{ $value ?: '-' }}</div>@endforeach
</div></div></div>
@endsection

