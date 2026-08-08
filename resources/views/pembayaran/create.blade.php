@extends('layouts.app')

@section('title', 'Input Pembayaran')
@section('page-title', 'Input Pembayaran Manual')
@section('page-description', 'Catat pembayaran mahasiswa secara manual')

@section('content')
<div class="row justify-content-center"><div class="col-xl-8">
<form method="POST" action="{{ route('pembayaran.store') }}" enctype="multipart/form-data">
@csrf
@php $selectedMahasiswaModel = $mahasiswas->firstWhere('id', (int) old('mahasiswa_id', $selectedMahasiswa)); @endphp

<div class="card dashboard-card border-0 mb-4">
    <div class="card-header"><div><h5>Pembayaran dari Mahasiswa</h5><small>Isi nominal untuk jenis yang dibayar sekarang, kosongkan yang tidak dibayar &mdash; bisa langsung beberapa jenis sekaligus dalam satu simpan</small></div></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <label for="mahasiswa_display" class="form-label">Mahasiswa</label>
                <div class="position-relative">
                    <input type="text" id="mahasiswa_display" class="form-control @error('mahasiswa_id') is-invalid @enderror" autocomplete="off" placeholder="Ketik nama atau kode PMB mahasiswa" value="{{ $selectedMahasiswaModel ? $selectedMahasiswaModel->kode_pmb . ' - ' . $selectedMahasiswaModel->nama_mahasiswa . ' (' . ($selectedMahasiswaModel->kampus->nama_kampus ?? '-') . ')' : '' }}" required>
                    <div id="mahasiswa_dropdown" class="list-group position-absolute w-100 d-none shadow-sm" style="top: 100%; z-index: 1055; max-height: 280px; overflow-y: auto;"></div>
                </div>
                <input type="hidden" id="mahasiswa_id" name="mahasiswa_id" value="{{ $selectedMahasiswaModel->id ?? '' }}">
                @error('mahasiswa_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            @error('nominal')<div class="col-12"><div class="alert alert-danger mb-0">{{ $message }}</div></div>@enderror
            @error('angsuran_ke')<div class="col-12"><div class="alert alert-danger mb-0">{{ $message }}</div></div>@enderror

            <div class="col-12">
                <div class="border rounded p-3">
                    <label for="nominal_angsuran" class="form-label fw-bold">Biaya Pendidikan (Angsuran)</label>
                    <div id="info_angsuran" class="mb-2"></div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="number" id="nominal_angsuran" name="nominal[Angsuran]" min="0" step="1" class="form-control" placeholder="Kosongkan jika tidak bayar Angsuran sekarang" value="{{ old('nominal.Angsuran') }}">
                        </div>
                        <div class="col-md-6">
                            <select id="angsuran_ke" name="angsuran_ke" class="form-select">
                                @for ($i = 1; $i <= 12; $i++)<option value="{{ $i }}" @selected((string) old('angsuran_ke') === (string) $i)>Angsuran ke-{{ $i }}</option>@endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="border rounded p-3">
                    <label for="nominal_wisuda" class="form-label fw-bold">Wisuda</label>
                    <div id="info_wisuda" class="mb-2"></div>
                    <input type="number" id="nominal_wisuda" name="nominal[Wisuda]" min="0" step="1" class="form-control" placeholder="Kosongkan jika tidak bayar Wisuda sekarang" value="{{ old('nominal.Wisuda') }}">
                </div>
            </div>

            <div class="col-12">
                <div class="border rounded p-3">
                    <label for="nominal_almamater" class="form-label fw-bold">Almamater</label>
                    <div id="info_almamater" class="mb-2"></div>
                    <input type="number" id="nominal_almamater" name="nominal[Almamater]" min="0" step="1" class="form-control" placeholder="Kosongkan jika tidak bayar Almamater sekarang" value="{{ old('nominal.Almamater') }}">
                </div>
            </div>

            <div class="col-md-6"><label for="tanggal_bayar" class="form-label">Tanggal Bayar</label><input type="date" id="tanggal_bayar" name="tanggal_bayar" class="form-control" value="{{ old('tanggal_bayar', now()->toDateString()) }}" required></div>
            <div class="col-md-6"><label for="status_bayar" class="form-label">Status Bayar</label><select id="status_bayar" name="status_bayar" class="form-select" required>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(old('status_bayar', 'menunggu') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select></div>
            <div class="col-md-6"><label for="bukti_bayar" class="form-label">Bukti Bayar dari Mahasiswa</label><input type="file" id="bukti_bayar" name="bukti_bayar" class="form-control"></div>
            <div class="col-12"><label for="catatan" class="form-label">Catatan</label><textarea id="catatan" name="catatan" rows="3" class="form-control">{{ old('catatan') }}</textarea></div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mb-4"><a href="{{ route('pembayaran.index') }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-wallet2 me-1"></i>Simpan Pembayaran</button></div>
</form>
</div></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const display = document.getElementById('mahasiswa_display');
    const hidden = document.getElementById('mahasiswa_id');
    const dropdown = document.getElementById('mahasiswa_dropdown');
    const mahasiswaOptions = @json($mahasiswas->map(fn ($mahasiswa) => [
        'id' => $mahasiswa->id,
        'label' => $mahasiswa->kode_pmb . ' - ' . $mahasiswa->nama_mahasiswa . ' (' . ($mahasiswa->kampus->nama_kampus ?? '-') . ')',
    ])->values());
    const usedAngsuranByMahasiswa = @json($usedAngsuran);
    const infoByMahasiswa = @json($infoByMahasiswa);

    const angsuranSelect = document.getElementById('angsuran_ke');
    const angsuranOptions = Array.from(angsuranSelect.options).filter(function (option) {
        return option.value !== '';
    });

    function formatRupiah(value) {
        return 'Rp ' + Math.round(value).toLocaleString('id-ID');
    }

    function refreshAngsuranOptions() {
        const used = usedAngsuranByMahasiswa[hidden.value] || [];

        angsuranOptions.forEach(function (option) {
            option.disabled = used.includes(parseInt(option.value, 10));
        });

        const nextAvailable = angsuranOptions.find(function (option) {
            return !option.disabled;
        });

        angsuranSelect.value = nextAvailable ? nextAvailable.value : '';
    }

    function refreshInfo() {
        const info = infoByMahasiswa[hidden.value];
        const angsuranContainer = document.getElementById('info_angsuran');
        const wisudaContainer = document.getElementById('info_wisuda');
        const almamaterContainer = document.getElementById('info_almamater');

        if (!info) {
            angsuranContainer.innerHTML = '';
            wisudaContainer.innerHTML = '';
            almamaterContainer.innerHTML = '';
            return;
        }

        const angsuran = info.Angsuran;
        const lunas = angsuran.sudah_lunas;
        const badgeClass = lunas ? 'text-bg-success' : 'text-bg-danger';
        const badgeLabel = lunas ? 'LUNAS' : 'Ada Tunggakan';

        angsuranContainer.innerHTML = '<div class="alert alert-light border d-flex flex-wrap align-items-center justify-content-between gap-2 mb-0 py-2">'
            + '<div><span class="badge ' + badgeClass + ' me-2">' + badgeLabel + '</span>'
            + '<span class="text-muted small">Harga Kesepakatan: ' + formatRupiah(angsuran.harga_kesepakatan) + ' &middot; Sudah Dibayar: ' + formatRupiah(angsuran.sudah_dibayar) + '</span></div>'
            + '<div class="fw-bold small ' + (lunas ? 'text-success' : 'text-danger') + '">Tunggakan: ' + formatRupiah(angsuran.tunggakan) + '</div>'
            + '</div>';

        [['Wisuda', wisudaContainer], ['Almamater', almamaterContainer]].forEach(function (pair) {
            const jenis = pair[0];
            const container = pair[1];
            const data = info[jenis];

            const statusBadgeClass = {
                'Lunas': 'text-bg-success',
                'Menunggu Verifikasi': 'text-bg-warning',
                'Belum Bayar': 'text-bg-secondary',
            }[data.status] || 'text-bg-secondary';

            const referensi = data.referensi ? ' &middot; Referensi harga: ' + formatRupiah(data.referensi) : '';

            container.innerHTML = '<div class="alert alert-light border mb-0 py-2">'
                + '<span class="badge ' + statusBadgeClass + ' me-2">' + data.status + '</span>'
                + '<span class="text-muted small">' + jenis + referensi + '</span>'
                + '</div>';
        });
    }

    let currentMatches = [];
    let activeIndex = -1;

    function closeDropdown() {
        dropdown.classList.add('d-none');
        dropdown.innerHTML = '';
        currentMatches = [];
        activeIndex = -1;
    }

    function highlightActive() {
        Array.from(dropdown.children).forEach(function (el, index) {
            el.classList.toggle('active', index === activeIndex);
        });
    }

    function renderDropdown(matches) {
        currentMatches = matches;
        activeIndex = -1;

        if (matches.length === 0) {
            dropdown.innerHTML = '<div class="list-group-item text-muted small">Tidak ada mahasiswa yang cocok.</div>';
            dropdown.classList.remove('d-none');
            return;
        }

        dropdown.innerHTML = matches.map(function (item, index) {
            const span = document.createElement('span');
            span.textContent = item.label;
            return '<button type="button" class="list-group-item list-group-item-action py-2 px-3 text-start" data-index="' + index + '">' + span.innerHTML + '</button>';
        }).join('');

        dropdown.classList.remove('d-none');
    }

    function selectMahasiswa(item) {
        display.value = item.label;
        hidden.value = item.id;
        closeDropdown();
        refreshAngsuranOptions();
        refreshInfo();
    }

    display.addEventListener('input', function () {
        hidden.value = '';
        refreshAngsuranOptions();
        refreshInfo();

        const term = display.value.trim().toLowerCase();

        if (term.length === 0) {
            closeDropdown();
            return;
        }

        const matches = mahasiswaOptions.filter(function (item) {
            return item.label.toLowerCase().includes(term);
        }).slice(0, 30);

        renderDropdown(matches);
    });

    display.addEventListener('focus', function () {
        if (display.value.trim().length > 0 && !hidden.value) {
            display.dispatchEvent(new Event('input'));
        }
    });

    display.addEventListener('keydown', function (event) {
        if (dropdown.classList.contains('d-none') || currentMatches.length === 0) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            activeIndex = Math.min(activeIndex + 1, currentMatches.length - 1);
            highlightActive();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            highlightActive();
        } else if (event.key === 'Enter') {
            if (activeIndex >= 0) {
                event.preventDefault();
                selectMahasiswa(currentMatches[activeIndex]);
            }
        } else if (event.key === 'Escape') {
            closeDropdown();
        }
    });

    dropdown.addEventListener('mousedown', function (event) {
        const button = event.target.closest('[data-index]');

        if (!button) {
            return;
        }

        event.preventDefault();
        selectMahasiswa(currentMatches[parseInt(button.dataset.index, 10)]);
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('#mahasiswa_display') && !event.target.closest('#mahasiswa_dropdown')) {
            closeDropdown();
        }
    });

    refreshAngsuranOptions();
    refreshInfo();
});
</script>
@endsection
