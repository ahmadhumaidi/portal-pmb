@csrf

@if (isset($pengumuman))
    @method('PUT')
@endif

<div class="row g-3">

    <div class="col-12">
        <label for="judul" class="form-label">Judul</label>

        <input
            type="text"
            id="judul"
            name="judul"
            class="form-control @error('judul') is-invalid @enderror"
            value="{{ old('judul', $pengumuman->judul ?? '') }}"
            placeholder="Judul pengumuman"
            required
            autofocus
        >

        @error('judul')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="isi" class="form-label">Isi Pengumuman</label>

        <textarea
            id="isi"
            name="isi"
            rows="5"
            class="form-control @error('isi') is-invalid @enderror"
            placeholder="Isi pengumuman yang akan tampil di portal mahasiswa dan koordinator"
            required
        >{{ old('isi', $pengumuman->isi ?? '') }}</textarea>

        @error('isi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="tanggal" class="form-label">Tanggal</label>

        <input
            type="date"
            id="tanggal"
            name="tanggal"
            class="form-control @error('tanggal') is-invalid @enderror"
            value="{{ old('tanggal', optional($pengumuman->tanggal ?? null)->format('Y-m-d') ?? now()->toDateString()) }}"
        >

        @error('tanggal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="lampiran" class="form-label">Lampiran (opsional)</label>

        <input
            type="file"
            id="lampiran"
            name="lampiran"
            class="form-control @error('lampiran') is-invalid @enderror"
            accept=".jpg,.jpeg,.pdf"
        >

        <div class="form-text">Format JPG atau PDF, maksimal 5 MB.</div>

        @if (isset($pengumuman) && $pengumuman->lampiran_path)
            <div class="small text-muted mt-1 file-path">
                Saat ini:
                <a href="{{ route('pengumuman.lampiran', $pengumuman) }}" target="_blank">{{ $pengumuman->lampiran_path }}</a>
            </div>
        @endif

        @error('lampiran')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label d-block">Status</label>

        <div class="form-check form-switch mt-2">
            <input type="hidden" name="status_aktif" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                id="status_aktif"
                name="status_aktif"
                value="1"
                @checked(old('status_aktif', $pengumuman->status_aktif ?? true))
            >

            <label class="form-check-label" for="status_aktif">
                Tampilkan di portal mahasiswa dan koordinator
            </label>
        </div>
    </div>

</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('pengumuman.index') }}" class="btn btn-light border">
        Batal
    </a>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>
        Simpan
    </button>
</div>
