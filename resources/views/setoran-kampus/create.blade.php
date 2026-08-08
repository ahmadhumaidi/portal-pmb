@extends('layouts.app')

@section('title', 'Input Setoran ke Kampus')
@section('page-title', 'Input Setoran ke Kampus')
@section('page-description', 'Catat uang yang sudah disetorkan ke kampus mitra')

@section('content')
<div class="row justify-content-center"><div class="col-xl-8">
<form method="POST" action="{{ route('setoran-kampus.store') }}" enctype="multipart/form-data">
@csrf
@php $selectedMahasiswaModel = $mahasiswas->firstWhere('id', (int) old('mahasiswa_id', $selectedMahasiswa)); @endphp

<div class="card dashboard-card border-0 mb-4">
    <div class="card-header"><div><h5>Setoran ke Kampus Mitra</h5><small>Isi nominal untuk jenis yang disetor sekarang, kosongkan yang tidak disetor &mdash; bisa langsung beberapa jenis sekaligus dalam satu simpan</small></div></div>
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

            @foreach ($jenisSetorans as $jenis)
                @php $slug = \Illuminate\Support\Str::slug($jenis); @endphp
                <div class="col-12">
                    <div class="border rounded p-3">
                        <label for="nominal_{{ $slug }}" class="form-label fw-bold">{{ $jenis }}</label>
                        <div id="tunggakan_kampus_info_{{ $slug }}" class="mb-2"></div>
                        <input type="number" id="nominal_{{ $slug }}" name="nominal[{{ $jenis }}]" min="0" step="1" class="form-control @error('nominal.'.$jenis) is-invalid @enderror" placeholder="Kosongkan jika tidak menyetor {{ $jenis }} sekarang" value="{{ old('nominal.'.$jenis) }}">
                        @error('nominal.'.$jenis)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            @endforeach

            <div class="col-md-6"><label for="tanggal_setor" class="form-label">Tanggal Setor</label><input type="date" id="tanggal_setor" name="tanggal_setor" class="form-control @error('tanggal_setor') is-invalid @enderror" value="{{ old('tanggal_setor', now()->toDateString()) }}" required>@error('tanggal_setor')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="bukti_setor" class="form-label">Bukti Setor ke Kampus</label><input type="file" id="bukti_setor" name="bukti_setor" class="form-control @error('bukti_setor') is-invalid @enderror">@error('bukti_setor')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label for="catatan" class="form-label">Catatan</label><textarea id="catatan" name="catatan" rows="3" class="form-control">{{ old('catatan') }}</textarea></div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mb-4"><a href="{{ route('setoran-kampus.index') }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-cash-coin me-1"></i>Simpan Setoran</button></div>
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
    const kewajibanByMahasiswa = @json($kewajibanByMahasiswa);
    const jenisSetorans = @json($jenisSetorans);

    function slugify(value) {
        return value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }

    function formatRupiah(value) {
        return 'Rp ' + Math.round(value).toLocaleString('id-ID');
    }

    function refreshTunggakanInfo() {
        const perJenis = kewajibanByMahasiswa[hidden.value];

        jenisSetorans.forEach(function (jenis) {
            const container = document.getElementById('tunggakan_kampus_info_' + slugify(jenis));
            const data = perJenis ? perJenis[jenis] : null;

            if (!data) {
                container.innerHTML = '';
                return;
            }

            if (data.target === null) {
                container.innerHTML = '<div class="alert alert-warning mb-0 py-2 small">Biaya ' + jenis + ' untuk mahasiswa ini belum diatur.</div>';
                return;
            }

            if (data.opsional_belum_opt_in) {
                container.innerHTML = '<div class="alert alert-secondary mb-0 py-2 small">Almamater bersifat opsional (add-on) &mdash; mahasiswa ini belum memesan/membayar almamater, jadi belum terhitung tunggakan ke kampus. Target harga: ' + formatRupiah(data.target) + '.</div>';
                return;
            }

            const lunas = data.tunggakan <= 0;
            const badgeClass = lunas ? 'text-bg-success' : 'text-bg-danger';
            const badgeLabel = lunas ? 'LUNAS' : 'Ada Tunggakan';

            container.innerHTML = '<div class="alert alert-light border d-flex flex-wrap align-items-center justify-content-between gap-2 mb-0 py-2">'
                + '<div><span class="badge ' + badgeClass + ' me-2">' + badgeLabel + '</span>'
                + '<span class="text-muted small">Target: ' + formatRupiah(data.target) + ' &middot; Sudah Disetor: ' + formatRupiah(data.sudah_disetor) + '</span></div>'
                + '<div class="fw-bold small ' + (lunas ? 'text-success' : 'text-danger') + '">Tunggakan: ' + formatRupiah(data.tunggakan) + '</div>'
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
        refreshTunggakanInfo();
    }

    display.addEventListener('input', function () {
        hidden.value = '';
        refreshTunggakanInfo();

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

    refreshTunggakanInfo();
});
</script>
@endsection
