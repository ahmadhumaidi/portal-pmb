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
            <div class="col-md-4">
                <input type="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari kode PMB, nama, NIK, WhatsApp...">
            </div>
            <div class="col-md-2">
                <select id="pembayaran-kampus-filter" name="kampus_id" class="form-select">
                    <option value="">Semua kampus</option>
                    @foreach ($kampuses as $kampus)
                        <option value="{{ $kampus->id }}" @selected((string) ($kampusId ?? '') === (string) $kampus->id)>{{ $kampus->nama_kampus }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="pembayaran-jurusan-filter" name="jurusan_id" class="form-select">
                    <option value="">Semua prodi</option>
                    @foreach ($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}" data-kampus-id="{{ $jurusan->kampus_id }}" data-prodi-label="{{ $jurusan->nama_jurusan }}" data-full-label="{{ $jurusan->nama_jurusan }} - {{ $jurusan->kampus->nama_kampus ?? '-' }}" @selected((string) ($jurusanId ?? '') === (string) $jurusan->id)>{{ $kampusId ? $jurusan->nama_jurusan : $jurusan->nama_jurusan . ' - ' . ($jurusan->kampus->nama_kampus ?? '-') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
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
<script>
    function syncPembayaranJurusanFilter() {
        const kampusSelect = document.getElementById('pembayaran-kampus-filter');
        const jurusanSelect = document.getElementById('pembayaran-jurusan-filter');

        if (!kampusSelect || !jurusanSelect) {
            return;
        }

        const selectedKampus = kampusSelect.value;

        Array.from(jurusanSelect.options).forEach((option) => {
            if (!option.value) {
                option.hidden = false;
                option.disabled = false;
                option.textContent = 'Semua prodi';
                return;
            }

            const matchesKampus = !selectedKampus || option.dataset.kampusId === selectedKampus;
            option.hidden = !matchesKampus;
            option.disabled = !matchesKampus;
            option.textContent = selectedKampus ? option.dataset.prodiLabel : option.dataset.fullLabel;
        });

        const selectedOption = jurusanSelect.selectedOptions[0];
        if (selectedOption?.hidden || selectedOption?.disabled) {
            jurusanSelect.value = '';
        }
    }

    document.getElementById('pembayaran-kampus-filter')?.addEventListener('change', syncPembayaranJurusanFilter);
    syncPembayaranJurusanFilter();
</script>
@endsection
