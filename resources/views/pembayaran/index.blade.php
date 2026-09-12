@extends('layouts.app')

@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran')
@section('page-description', 'Ringkasan kewajiban dan pembayaran per mahasiswa')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format(max(0, (float) $value), 0, ',', '.');
@endphp

<div class="card dashboard-card border-0">
    <div class="card-header flex-wrap gap-3">
        <div>
            <h5>Daftar Pembayaran Mahasiswa</h5>
            <small>Ringkasan kewajiban, pembayaran masuk, dan setoran ke kampus</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('setoran-kampus.create') }}" class="btn btn-outline-primary"><i class="bi bi-cash-coin me-1"></i>Input Setoran</a>
            <a href="{{ route('pembayaran.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Input Pembayaran</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('pembayaran.index') }}" class="row g-2 mb-4">
            <div class="col-md-9">
                <input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari kode PMB, nama, NIK, WhatsApp, kampus, atau jurusan...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary" type="submit">Cari</button>
                <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Mahasiswa</th>
                        <th>Kewajiban</th>
                        <th>Sudah Terbayar</th>
                        <th>Kekurangan</th>
                        <th>Sudah Disetor Kampus</th>
                        <th>Kekurangan Setor</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mahasiswas as $mahasiswa)
                        @php
                            $kewajibanMahasiswa = (float) $mahasiswa->harga_kesepakatan;
                            $sudahTerbayar = $mahasiswa->totalDibayarMahasiswa();
                            $kekuranganMahasiswa = $mahasiswa->totalTagihan();
                            $sudahDisetorKampus = $mahasiswa->totalSetorKampus();
                            $kekuranganSetorKampus = $mahasiswa->kewajibanKampus();
                        @endphp
                        <tr>
                            <td>{{ $mahasiswas->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="fw-semibold">{{ $mahasiswa->nama_mahasiswa }}</div>
                                <div class="small text-muted">{{ $mahasiswa->kode_pmb }} &middot; {{ $mahasiswa->kampus->nama_kampus ?? '-' }}</div>
                                <div class="small text-muted">{{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</div>
                            </td>
                            <td class="text-nowrap">{{ $rupiah($kewajibanMahasiswa) }}</td>
                            <td class="text-nowrap">{{ $rupiah($sudahTerbayar) }}</td>
                            <td class="text-nowrap">
                                <span class="badge {{ $kekuranganMahasiswa > 0 ? 'text-bg-warning' : 'text-bg-success' }}">
                                    {{ $rupiah($kekuranganMahasiswa) }}
                                </span>
                            </td>
                            <td class="text-nowrap">{{ $rupiah($sudahDisetorKampus) }}</td>
                            <td class="text-nowrap">
                                @if ($kekuranganSetorKampus === null)
                                    <span class="text-muted">-</span>
                                @else
                                    <span class="badge {{ $kekuranganSetorKampus > 0 ? 'text-bg-warning' : 'text-bg-success' }}">
                                        {{ $rupiah($kekuranganSetorKampus) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-end flex-wrap">
                                    <a href="{{ route('pembayaran.create', ['mahasiswa_id' => $mahasiswa->id]) }}" class="btn btn-sm btn-outline-primary">Bayar</a>
                                    <a href="{{ route('setoran-kampus.create', ['mahasiswa_id' => $mahasiswa->id]) }}" class="btn btn-sm btn-outline-success">Setor</a>
                                    <a href="{{ route('mahasiswa.show', $mahasiswa) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada data mahasiswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            @include('partials.per-page-select')
            {{ $mahasiswas->links() }}
        </div>
    </div>
</div>
@endsection
