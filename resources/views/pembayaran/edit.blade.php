@extends('layouts.app')

@section('title', 'Edit Pembayaran')
@section('page-title', 'Edit Pembayaran')
@section('page-description', $pembayaran->mahasiswa->nama_mahasiswa ?? 'Edit transaksi pembayaran')

@section('content')
<div class="row justify-content-center"><div class="col-xl-8">
<form method="POST" action="{{ route('pembayaran.update', $pembayaran) }}" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="card dashboard-card border-0 mb-4">
    <div class="card-header"><div><h5>Pembayaran dari Mahasiswa</h5><small>Uang yang diterima dari mahasiswa</small></div></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12"><label class="form-label">Mahasiswa</label><input type="text" class="form-control" value="{{ $pembayaran->mahasiswa->kode_pmb }} - {{ $pembayaran->mahasiswa->nama_mahasiswa }} ({{ $pembayaran->mahasiswa->kampus->nama_kampus ?? '-' }})" disabled></div>
            <div class="col-md-4"><label class="form-label">Jenis Pembayaran</label><input type="text" class="form-control" value="{{ $pembayaran->jenis_pembayaran }}" disabled><div class="form-text">Untuk mencatat angsuran baru, gunakan "Input Pembayaran Manual" dari halaman mahasiswa &mdash; bukan mengedit pembayaran yang sudah ada.</div></div>
            <div class="col-md-4"><label class="form-label">Angsuran Ke</label><input type="text" class="form-control" value="{{ $pembayaran->angsuran_ke ? 'Angsuran ke-' . $pembayaran->angsuran_ke : '- Tidak Berlaku -' }}" disabled></div>
            <div class="col-md-4"><label class="form-label">Tanggal Bayar</label><input type="text" class="form-control" value="{{ optional($pembayaran->tanggal_bayar)->format('d M Y') }}" disabled></div>
            <div class="col-md-6"><label for="nominal" class="form-label">Nominal</label><input type="number" id="nominal" name="nominal" min="0" step="1" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal', $pembayaran->nominal) }}" required>@error('nominal')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="status_bayar" class="form-label">Status Bayar</label><select id="status_bayar" name="status_bayar" class="form-select" required>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(old('status_bayar', $pembayaran->status_bayar) === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div>
            <div class="col-md-6">
                <label for="bukti_bayar" class="form-label">Bukti Bayar dari Mahasiswa</label>
                <input type="file" id="bukti_bayar" name="bukti_bayar" class="form-control @error('bukti_bayar') is-invalid @enderror">
                @if ($pembayaran->bukti_bayar_path)
                    <div class="small text-muted mt-1 d-flex align-items-center justify-content-between gap-2">
                        <span class="text-truncate">Saat ini: {{ basename($pembayaran->bukti_bayar_path) }}</span>
                        <a href="{{ route('pembayaran.view-file', [$pembayaran, 'bukti_bayar']) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Lihat</a>
                    </div>
                @endif
                @error('bukti_bayar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12"><label for="catatan" class="form-label">Catatan</label><textarea id="catatan" name="catatan" rows="3" class="form-control">{{ old('catatan', $pembayaran->catatan) }}</textarea></div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mb-4"><a href="{{ route('pembayaran.show', $pembayaran) }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan Perubahan</button></div>
</form>
</div></div>
@endsection
