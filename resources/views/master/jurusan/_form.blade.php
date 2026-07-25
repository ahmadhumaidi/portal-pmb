@csrf

@if (isset($jurusan))
    @method('PUT')
@endif

<div class="row g-3">

    @if (isset($jurusan))
        <div class="col-md-6">
            <label class="form-label">Kode Jurusan</label>

            <input
                type="text"
                class="form-control"
                value="{{ $jurusan->kode_jurusan }}"
                readonly
            >
        </div>
    @endif

    <div class="{{ isset($jurusan) ? 'col-md-6' : 'col-12' }}">
        <label for="kampus_id" class="form-label">
            Kampus
        </label>

        <select
            id="kampus_id"
            class="form-select @error('kampus_id') is-invalid @enderror"
            name="kampus_id"
            required
        >
            <option value="">Pilih Kampus</option>

            @foreach ($kampuses as $kampus)
                <option
                    value="{{ $kampus->id }}"
                    @selected(old('kampus_id', $jurusan->kampus_id ?? '') == $kampus->id)
                >
                    {{ $kampus->nama_kampus }}
                </option>
            @endforeach
        </select>

        @error('kampus_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-8">
        <label for="nama_jurusan" class="form-label">
            Nama Jurusan
        </label>

        <input
            type="text"
            id="nama_jurusan"
            class="form-control @error('nama_jurusan') is-invalid @enderror"
            name="nama_jurusan"
            value="{{ old('nama_jurusan', $jurusan->nama_jurusan ?? '') }}"
            placeholder="Contoh: S1 Manajemen"
            required
            autofocus
        >

        @error('nama_jurusan')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="jenjang" class="form-label">
            Jenjang
        </label>

        <input
            type="text"
            id="jenjang"
            class="form-control @error('jenjang') is-invalid @enderror"
            name="jenjang"
            value="{{ old('jenjang', $jurusan->jenjang ?? '') }}"
            placeholder="S1 / D3 / S2"
        >

        @error('jenjang')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label d-block">Status</label>

        <div class="form-check form-switch">
            <input type="hidden" name="status_aktif" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                id="status_aktif"
                name="status_aktif"
                value="1"
                @checked(old('status_aktif', $jurusan->status_aktif ?? true))
            >

            <label class="form-check-label" for="status_aktif">
                Jurusan aktif
            </label>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a
        href="{{ route('jurusan.index') }}"
        class="btn btn-light border"
    >
        Batal
    </a>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>
        Simpan
    </button>
</div>
