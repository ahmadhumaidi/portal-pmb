@extends('layouts.app')

@section('title', 'Detail Koordinator')
@section('page-title', 'Detail Koordinator')
@section('page-description', $koordinator->nama_koordinator)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('koordinator.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <a href="{{ route('koordinator.edit', $koordinator) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit Koordinator</a>
</div>

<div class="card dashboard-card border-0 mb-4">
    <div class="card-header"><div><h5>{{ $koordinator->nama_koordinator }}</h5><small>{{ $koordinator->kode_koordinator }}</small></div></div>
    <div class="card-body detail-list">
        <div class="detail-label">Status</div>
        <div class="detail-value">
            @if ($koordinator->status_aktif)
                <span class="badge text-bg-success">Aktif</span>
            @else
                <span class="badge text-bg-secondary">Nonaktif</span>
            @endif
        </div>
        <div class="detail-label">Jumlah Perolehan Mahasiswa</div>
        <div class="detail-value fw-semibold">{{ $mahasiswas->total() }}</div>
    </div>
</div>

<div class="card dashboard-card border-0">
    <div class="card-header flex-wrap gap-3">
        <div><h5>Perolehan Mahasiswa</h5><small>Mahasiswa yang terdaftar melalui koordinator ini</small></div>
        @if ($showAll)
            <a href="{{ route('koordinator.show', $koordinator) }}" class="btn btn-sm btn-outline-secondary">Tampilkan per Halaman</a>
        @else
            <a href="{{ route('koordinator.show', ['koordinator' => $koordinator, 'semua' => 1]) }}" class="btn btn-sm btn-outline-secondary">Tampilkan Semua</a>
        @endif
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode PMB</th>
                        <th>Nama Mahasiswa</th>
                        <th>Kampus</th>
                        <th>Jurusan</th>
                        <th>Kontak</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mahasiswas as $mahasiswa)
                        <tr>
                            <td>{{ $mahasiswas->firstItem() + $loop->index }}</td>
                            <td><span class="badge text-bg-light border text-dark">{{ $mahasiswa->kode_pmb }}</span></td>
                            <td>{{ $mahasiswa->nama_mahasiswa }}</td>
                            <td>{{ $mahasiswa->kampus->nama_kampus ?? '-' }}</td>
                            <td>{{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</td>
                            <td>{{ $mahasiswa->nomor_whatsapp ?? '-' }}</td>
                            <td><span class="badge text-bg-info">{{ ucfirst(str_replace('_', ' ', $mahasiswa->status_pendaftaran)) }}</span></td>
                            <td><a href="{{ route('mahasiswa.show', $mahasiswa) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-person-vcard fs-1 d-block mb-2"></i>
                                Belum ada mahasiswa yang terdaftar melalui koordinator ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $mahasiswas->links() }}</div>
    </div>
</div>

@endsection
