@extends('layouts.app')

@section('title', 'Detail Mahasiswa')
@section('page-title', 'Detail Mahasiswa')
@section('page-description', $mahasiswa->nama_mahasiswa)

@section('content')
@php
    $backQuery = request('back');
    $indexUrl = route('mahasiswa.index') . ($backQuery ? '?' . $backQuery : '');
    $editUrl = route('mahasiswa.edit', $mahasiswa) . ($backQuery ? '?back=' . urlencode($backQuery) : '');

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
        'Koordinator Kelas' => $mahasiswa->koordinator->nama_koordinator ?? null,
        'Tahun Lulus' => $mahasiswa->tahun_lulus,
        'Harga Kesepakatan' => 'Rp ' . number_format($mahasiswa->harga_kesepakatan, 0, ',', '.'),
        'Status Pendaftaran' => ucfirst(str_replace('_', ' ', $mahasiswa->status_pendaftaran)),
        'Keterangan' => $mahasiswa->keterangan,
        'Dibuat Sistem' => optional($mahasiswa->created_at)->format('d M Y H:i'),
        'Diubah Sistem' => optional($mahasiswa->updated_at)->format('d M Y H:i'),
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ $indexUrl }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <div class="d-flex gap-2">
        <a href="{{ $editUrl }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit Data</a>
        <form action="{{ route('mahasiswa.destroy', $mahasiswa) }}" method="POST" onsubmit="return confirm('Hapus data mahasiswa ini? Berkas dan pembayaran yang terkait akan ikut terhapus permanen.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-8">
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

    <div class="col-12 col-xl-4">
        @php
            $biayaKampus = $mahasiswa->resolvedBiayaKampus();
            $keuntungan = $mahasiswa->keuntungan();
            $totalTagihan = $mahasiswa->totalTagihan();
            $totalDibayarMahasiswa = $mahasiswa->totalDibayarMahasiswa();
            $totalSetorKampus = $mahasiswa->totalSetorKampus();
            $kewajibanKampus = $mahasiswa->kewajibanKampus();
        @endphp
        <div class="card dashboard-card border-0 mb-4">
            <div class="card-header"><div><h5>Tagihan Mahasiswa</h5><small>Harga kesepakatan dikurangi pembayaran yang sudah masuk</small></div></div>
            <div class="card-body detail-list">
                <div class="detail-label">Status Pembayaran</div>
                <div class="detail-value">
                    @if ($mahasiswa->sudahLunas())
                        <span class="badge text-bg-success">LUNAS</span>
                    @else
                        <span class="badge text-bg-warning">Belum Lunas</span>
                    @endif
                </div>
                <div class="detail-label">Harga Kesepakatan</div>
                <div class="detail-value">Rp {{ number_format($mahasiswa->harga_kesepakatan, 0, ',', '.') }}</div>
                <div class="detail-label">Sudah Dibayar Mahasiswa</div>
                <div class="detail-value">Rp {{ number_format($totalDibayarMahasiswa, 0, ',', '.') }}</div>
                <div class="detail-label">Total Tagihan</div>
                <div class="detail-value fw-semibold {{ $totalTagihan > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="card dashboard-card border-0 mb-4">
            <div class="card-header"><div><h5>Tagihan ke Kampus</h5><small>Biaya kampus dikurangi setoran yang sudah dibayar</small></div></div>
            <div class="card-body detail-list">
                <div class="detail-label">Biaya Kampus</div>
                <div class="detail-value">{{ $biayaKampus !== null ? 'Rp ' . number_format($biayaKampus, 0, ',', '.') : 'Belum diatur' }}</div>
                <div class="detail-label">Sudah Disetor ke Kampus</div>
                <div class="detail-value">Rp {{ number_format($totalSetorKampus, 0, ',', '.') }}</div>
                <div class="detail-label">Kewajiban ke Kampus</div>
                <div class="detail-value fw-semibold {{ $kewajibanKampus !== null && $kewajibanKampus > 0 ? 'text-danger' : 'text-success' }}">{{ $kewajibanKampus !== null ? 'Rp ' . number_format($kewajibanKampus, 0, ',', '.') : '-' }}</div>
                <div class="detail-label">Keuntungan</div>
                <div class="detail-value fw-semibold {{ $keuntungan !== null && $keuntungan < 0 ? 'text-danger' : 'text-success' }}">{{ $keuntungan !== null ? 'Rp ' . number_format($keuntungan, 0, ',', '.') : '-' }}</div>
            </div>
        </div>
        <div class="card dashboard-card border-0 mb-4">
            <div class="card-header"><div><h5>Ringkasan Modul</h5><small>Data turunan mahasiswa</small></div></div>
            <div class="card-body d-grid gap-2">
                @if ($mahasiswa->berkas)
                    <a href="{{ route('berkas.show', $mahasiswa->berkas) }}" class="btn btn-outline-primary text-start">Berkas: {{ $mahasiswa->berkas->kode_berkas }}</a><a href="{{ route('berkas.edit', $mahasiswa->berkas) }}" class="btn btn-outline-secondary text-start">Upload Berkas</a>
                @endif
                <a href="{{ route('pembayaran.create', ['mahasiswa_id' => $mahasiswa->id]) }}" class="btn btn-outline-success text-start">Input Pembayaran Manual</a>
                <a href="{{ route('setoran-kampus.create', ['mahasiswa_id' => $mahasiswa->id]) }}" class="btn btn-outline-success text-start">Input Setoran ke Kampus</a>@foreach ($mahasiswa->setoranKampus as $setoran)
                    <a href="{{ route('setoran-kampus.show', $setoran) }}" class="btn btn-outline-primary text-start">Setoran Kampus: {{ $setoran->kode_setoran_kampus }}</a>
                @endforeach
                @if ($mahasiswa->hasil)
                    <a href="{{ route('hasil.show', $mahasiswa->hasil) }}" class="btn btn-outline-primary text-start">Hasil: {{ $mahasiswa->hasil->kode_hasil }}</a><a href="{{ route('hasil.edit', $mahasiswa->hasil) }}" class="btn btn-outline-secondary text-start">Update Hasil</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-0">
    <div class="col-12">
        <div class="card dashboard-card border-0">
            <div class="card-header"><div><h5>Riwayat Pembayaran</h5><small>Progres angsuran sampai pelunasan</small></div></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Jenis</th>
                                <th>Angsuran</th>
                                <th>Tanggal Bayar</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Bukti</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mahasiswa->pembayarans->sortBy(['angsuran_ke', 'tanggal_bayar']) as $pembayaran)
                                <tr>
                                    <td><span class="badge text-bg-light border text-dark">{{ $pembayaran->kode_pembayaran }}</span></td>
                                    <td>{{ $pembayaran->jenis_pembayaran }}</td>
                                    <td>{{ $pembayaran->angsuran_ke ?? '-' }}</td>
                                    <td>{{ optional($pembayaran->tanggal_bayar)->format('d M Y') ?? '-' }}</td>
                                    <td>Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
                                    <td><span class="badge text-bg-info">{{ ucfirst(str_replace('_', ' ', $pembayaran->status_bayar)) }}</span></td>
                                    <td class="small">{{ $pembayaran->bukti_bayar_path ? 'Ada' : '-' }}</td>
                                    <td><a href="{{ route('pembayaran.show', $pembayaran) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada pembayaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


