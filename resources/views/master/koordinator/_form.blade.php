@csrf

@if (isset($koordinator))
    @method('PUT')
@endif

<div class="row g-3">

    @if (isset($koordinator))
        <div class="col-12">
            <label class="form-label">Kode Koordinator</label>

            <input
                type="text"
                class="form-control"
                value="{{ $koordinator->kode_koordinator }}"
                readonly
            >
        </div>
    @endif

    <div class="col-12">
        <label for="nama_koordinator" class="form-label">
            Nama Koordinator
        </label>

        <input
            type="text"
            id="nama_koordinator"
            name="nama_koordinator"
            class="form-control @error('nama_koordinator') is-invalid @enderror"
            value="{{ old('nama_koordinator', $koordinator->nama_koordinator ?? '') }}"
            placeholder="Nama koordinator kelas"
            required
            autofocus
        >

        @error('nama_koordinator')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-12">
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
                        $koordinator->status_aktif ?? true
                    )
                )
            >

            <label class="form-check-label" for="status_aktif">
                Koordinator aktif
            </label>
        </div>
    </div>

</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a
        href="{{ route('koordinator.index') }}"
        class="btn btn-light border"
    >
        Batal
    </a>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>
        Simpan
    </button>
</div>
