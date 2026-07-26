@extends('layouts.app')

@section('title', 'Mahasiswa')
@section('page-title', 'Mahasiswa')
@section('page-description', 'Pantau mahasiswa yang mendaftar dan status pendaftarannya')

@section('content')
<div class="card dashboard-card border-0">
    <div class="card-header flex-wrap gap-3">
        <div>
            <h5>Daftar Mahasiswa</h5>
            <small>Ringkasan calon mahasiswa dan status proses</small>
        </div>
        <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Mahasiswa
        </a>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('mahasiswa.index') }}" class="row g-2 mb-4">
            <div class="col-md-7">
                <input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari nama, kode PMB, NIK, NISN, ibu, sekolah, email, atau whatsapp...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $option)
                        <option value="{{ $option }}" @selected(($status ?? '') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline-primary" type="submit">Cari</button>
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>NIK</th>
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
                            <td>
                                <span class="badge text-bg-light border text-dark">{{ $mahasiswa->kode_pmb }}</span>
                            </td>
                            <td>
                                <div>{{ $mahasiswa->nama_mahasiswa }}</div>
                                <div class="small text-muted">{{ $mahasiswa->asal_sekolah ?? '-' }}</div>
                            </td>
                            <td>
                                <div>{{ $mahasiswa->nik ?? '-' }}</div>
                                <div class="small text-muted">NISN: {{ $mahasiswa->nisn ?? '-' }}</div>
                            </td>
                            <td>{{ $mahasiswa->kampus->nama_kampus ?? '-' }}</td>
                            <td>{{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</td>
                            <td>
                                <div>{{ $mahasiswa->nomor_whatsapp ?? '-' }}</div>
                                <div class="small text-muted">{{ $mahasiswa->email ?? '-' }}</div>
                            </td>
                            <td><span class="badge text-bg-info">{{ ucfirst(str_replace('_', ' ', $mahasiswa->status_pendaftaran)) }}</span></td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('mahasiswa.show', $mahasiswa) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                <form action="{{ route('mahasiswa.destroy', $mahasiswa) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data mahasiswa ini? Berkas dan pembayaran yang terkait akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-5 text-muted">Belum ada data mahasiswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $mahasiswas->links() }}</div>
    </div>
</div>
@endsection

