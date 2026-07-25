@extends('layouts.app')

@section('title', 'Detail Hasil')
@section('page-title', 'Detail Hasil')
@section('page-description', $hasil->mahasiswa->nama_mahasiswa ?? 'Detail hasil')

@section('content')
@php
$data = [
'Kode Hasil' => $hasil->kode_hasil,
'Mahasiswa' => $hasil->mahasiswa->nama_mahasiswa ?? null,
'Kode PMB' => $hasil->mahasiswa->kode_pmb ?? null,
'Kampus' => $hasil->mahasiswa->kampus->nama_kampus ?? null,
'Jurusan' => $hasil->mahasiswa->jurusan->nama_jurusan ?? null,
'Status Kelulusan' => $hasil->status_kelulusan,
'NIM' => $hasil->nim,
'No. Seri Ijazah' => $hasil->nomor_seri_ijazah,
'Link PDDIKTI' => $hasil->link_pddikti,
'Status Kirim' => ucfirst(str_replace('_', ' ', $hasil->status_kirim)),
'Tanggal Kirim' => optional($hasil->tanggal_kirim)->format('d M Y H:i'),
'Metode Kirim' => $hasil->metode_kirim,
'Nomor Resi' => $hasil->nomor_resi,
'Keterangan' => $hasil->keterangan,
'Input By' => $hasil->inputBy->name ?? null,
'Dibuat Sistem' => optional($hasil->created_at)->format('d M Y H:i'),
'Diubah Sistem' => optional($hasil->updated_at)->format('d M Y H:i'),
];
@endphp
<div class="d-flex justify-content-between align-items-center mb-3"><a href="{{ route('hasil.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a><a href="{{ route('hasil.edit', $hasil) }}" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Hasil</a></div>
<div class="card dashboard-card border-0 mb-4"><div class="card-header"><div><h5>Dokumen Hasil</h5><small>Dokumen akhir yang sudah di-upload</small></div></div><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Dokumen</th><th class="text-end">Aksi</th></tr></thead><tbody>
@foreach ($fileFields as $field => $meta)
@php $path = $hasil->{$meta['column']}; @endphp
<tr><td>{{ $meta['label'] }}</td><td class="text-end">@if ($path)<a href="{{ route('hasil.view-file', [$hasil, $field]) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Lihat</a>@else<span class="text-muted small">Belum upload</span>@endif</td></tr>
@endforeach
</tbody></table></div></div></div>
<div class="card dashboard-card border-0"><div class="card-header"><div><h5>Data Lengkap Hasil</h5><small>Data hasil dan dokumen akhir yang dipakai sistem</small></div></div><div class="card-body"><div class="detail-list">
@foreach ($data as $label => $value)<div class="detail-label">{{ $label }}</div><div class="detail-value {{ str_contains($label, 'Link') ? 'file-path' : '' }}">{{ $value ?: '-' }}</div>@endforeach
</div></div></div>
@endsection
