@extends('layouts.app')

@section('title', 'Berkas')
@section('page-title', 'Berkas')
@section('page-description', 'Pantau kelengkapan dokumen pendaftaran')

@section('content')
<div class="card dashboard-card border-0">
    <div class="card-header flex-wrap gap-3"><div><h5>Daftar Berkas</h5><small>Status verifikasi dokumen mahasiswa</small></div></div>
    <div class="card-body">
        <form method="GET" action="{{ route('berkas.index') }}" class="row g-2 mb-4">
            <div class="col-md-7"><input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari kode, nama, NIK, atau dokumen..."></div>
            <div class="col-md-3"><select name="status" class="form-select"><option value="">Semua status</option>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(($status ?? '') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div>
            <div class="col-md-2 d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Cari</button><a href="{{ route('berkas.index') }}" class="btn btn-outline-secondary">Reset</a></div>
        </form>
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>No.</th><th>Kode</th><th>Mahasiswa</th><th class="text-center">Ijazah</th><th class="text-center">KTP</th><th class="text-center">KK</th><th class="text-center">Foto</th><th>Status</th><th>Keterangan</th><th></th></tr></thead><tbody>
            @forelse ($berkas as $item)
                <tr>
                    <td>{{ $berkas->firstItem() + $loop->index }}</td>
                    <td><span class="badge text-bg-light border text-dark">{{ $item->kode_berkas }}</span></td>
                    <td><div>{{ $item->mahasiswa->nama_mahasiswa ?? '-' }}</div><div class="small text-muted">{{ $item->mahasiswa->kode_pmb ?? '-' }}</div></td>
                    <td class="text-center">@if ($item->ijazah_path)<i class="bi bi-check-circle-fill text-success"></i>@else<span class="text-muted">-</span>@endif</td>
                    <td class="text-center">@if ($item->ktp_path)<i class="bi bi-check-circle-fill text-success"></i>@else<span class="text-muted">-</span>@endif</td>
                    <td class="text-center">@if ($item->kk_path)<i class="bi bi-check-circle-fill text-success"></i>@else<span class="text-muted">-</span>@endif</td>
                    <td class="text-center">@if ($item->pas_foto_path)<i class="bi bi-check-circle-fill text-success"></i>@else<span class="text-muted">-</span>@endif</td>
                    <td><span class="badge text-bg-warning">{{ ucfirst(str_replace('_', ' ', $item->status_verifikasi)) }}</span></td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('berkas.show', $item) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        <form action="{{ route('berkas.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data berkas ini beserta semua file yang sudah diupload?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center py-5 text-muted">Belum ada data berkas.</td></tr>
            @endforelse
        </tbody></table></div><div class="mt-3">{{ $berkas->links() }}</div>
    </div>
</div>
@endsection
