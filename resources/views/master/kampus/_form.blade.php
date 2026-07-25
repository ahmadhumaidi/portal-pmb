@csrf

@if (isset($kampus))
    @method('PUT')
@endif

<div class="row g-3">

    @if (isset($kampus))
        <div class="col-12">
            <label class="form-label">Kode Kampus</label>

            <input
                type="text"
                class="form-control"
                value="{{ $kampus->kode_kampus }}"
                readonly
            >
        </div>
    @endif

    <div class="col-12">
        <label for="nama_kampus" class="form-label">
            Nama Kampus
        </label>

        <input
            type="text"
            id="nama_kampus"
            name="nama_kampus"
            class="form-control @error('nama_kampus') is-invalid @enderror"
            value="{{ old('nama_kampus', $kampus->nama_kampus ?? '') }}"
            placeholder="Contoh: Universitas Kristen Cipta Wacana"
            required
            autofocus
        >

        @error('nama_kampus')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="harga" class="form-label">
            Biaya per Mahasiswa
        </label>

        <input
            type="number"
            id="harga"
            name="harga"
            min="0"
            step="1"
            class="form-control @error('harga') is-invalid @enderror"
            value="{{ old('harga', $kampus->harga ?? 0) }}"
            required
        >

        @error('harga')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label d-block">
            Status
        </label>

        <div class="form-check form-switch mt-2">
            <input type="hidden" name="status_aktif" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                id="status_aktif"
                name="status_aktif"
                value="1"
                @checked(
                    old(
                        'status_aktif',
                        $kampus->status_aktif ?? true
                    )
                )
            >

            <label class="form-check-label" for="status_aktif">
                Kampus aktif
            </label>
        </div>
    </div>

    <div class="col-12">
        <label for="catatan" class="form-label">
            Catatan
        </label>

        <textarea
            id="catatan"
            name="catatan"
            rows="4"
            class="form-control @error('catatan') is-invalid @enderror"
            placeholder="Catatan tambahan mengenai kampus"
        >{{ old('catatan', $kampus->catatan ?? '') }}</textarea>

        @error('catatan')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a
        href="{{ route('kampus.index') }}"
        class="btn btn-light border"
    >
        Batal
    </a>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>
        Simpan
    </button>
</div>