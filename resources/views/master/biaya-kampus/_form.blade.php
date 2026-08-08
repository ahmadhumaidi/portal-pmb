@csrf

@if (isset($biayaKampus))
    @method('PUT')
@endif

<div class="row g-3">

    @if (isset($biayaKampus))
        <div class="col-md-6">
            <label class="form-label">Kode Biaya</label>
            <input type="text" class="form-control" value="{{ $biayaKampus->kode_biaya_kampus }}" readonly>
        </div>
    @endif

    <div class="{{ isset($biayaKampus) ? 'col-md-6' : 'col-12' }}">
        <label for="kampus_id" class="form-label">Kampus</label>
        <select id="kampus_id" name="kampus_id" class="form-select @error('kampus_id') is-invalid @enderror" required>
            <option value="">Pilih Kampus</option>
            @foreach ($kampuses as $kampus)
                <option value="{{ $kampus->id }}" @selected(old('kampus_id', $biayaKampus->kampus_id ?? '') == $kampus->id)>{{ $kampus->nama_kampus }}</option>
            @endforeach
        </select>
        @error('kampus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label d-block">
            Berlaku Untuk
            @unless (isset($biayaKampus))
                <small class="text-muted">(bisa pilih lebih dari satu prodi sekaligus)</small>
            @endunless
        </label>

        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="semua_prodi" name="semua_prodi" value="1" @checked(old('semua_prodi', isset($biayaKampus) && is_null($biayaKampus->jurusan_id)))>
            <label class="form-check-label" for="semua_prodi">Semua Prodi</label>
        </div>

        <div id="prodiCheckboxes" class="row row-cols-1 row-cols-md-2 g-2 border rounded p-2 @error('jurusan_ids') is-invalid @enderror">
            <div class="col text-muted small">Pilih kampus terlebih dahulu.</div>
        </div>
        @error('jurusan_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="biaya" class="form-label">Biaya Pendidikan Disetor ke Kampus</label>
        <input type="number" id="biaya" name="biaya" min="0" step="1" class="form-control @error('biaya') is-invalid @enderror" value="{{ old('biaya', $biayaKampus->biaya ?? 0) }}" required>
        @error('biaya')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="biaya_wisuda" class="form-label">Biaya Wisuda Disetor ke Kampus</label>
        <input type="number" id="biaya_wisuda" name="biaya_wisuda" min="0" step="1" class="form-control @error('biaya_wisuda') is-invalid @enderror" value="{{ old('biaya_wisuda', $biayaKampus->biaya_wisuda ?? 0) }}">
        @error('biaya_wisuda')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="biaya_almamater" class="form-label">Biaya Almamater Disetor ke Kampus</label>
        <input type="number" id="biaya_almamater" name="biaya_almamater" min="0" step="1" class="form-control @error('biaya_almamater') is-invalid @enderror" value="{{ old('biaya_almamater', $biayaKampus->biaya_almamater ?? 0) }}">
        @error('biaya_almamater')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 d-flex align-items-end">
        <div class="form-check form-switch">
            <input type="hidden" name="status_aktif" value="0">
            <input class="form-check-input" type="checkbox" id="status_aktif" name="status_aktif" value="1" @checked(old('status_aktif', $biayaKampus->status_aktif ?? true))>
            <label class="form-check-label" for="status_aktif">Aturan biaya aktif</label>
        </div>
    </div>

    <div class="col-12">
        <label for="keterangan" class="form-label">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Catatan tambahan mengenai aturan biaya ini">{{ old('keterangan', $biayaKampus->keterangan ?? '') }}</textarea>
        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('biaya-kampus.index') }}" class="btn btn-light border">Batal</a>
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const allowMultiple = {{ isset($biayaKampus) ? 'false' : 'true' }};
    const kampusSelect = document.getElementById('kampus_id');
    const semuaProdiCheckbox = document.getElementById('semua_prodi');
    const prodiContainer = document.getElementById('prodiCheckboxes');
    const selectedJurusanIds = @json(array_map('strval', (array) old('jurusan_ids', isset($biayaKampus) && $biayaKampus->jurusan_id ? [$biayaKampus->jurusan_id] : [])));

    const jurusanByKampus = @json($jurusans->groupBy('kampus_id')->map(fn ($items) => $items->map(fn ($jurusan) => [
        'id' => $jurusan->id,
        'nama' => trim($jurusan->nama_jurusan . ($jurusan->jenjang ? ' - ' . $jurusan->jenjang : '')),
    ])->values()));

    function renderProdiCheckboxes() {
        const kampusId = kampusSelect.value;
        const jurusans = jurusanByKampus[kampusId] || [];

        prodiContainer.innerHTML = '';

        if (!kampusId) {
            prodiContainer.innerHTML = '<div class="col text-muted small">Pilih kampus terlebih dahulu.</div>';
            return;
        }

        if (jurusans.length === 0) {
            prodiContainer.innerHTML = '<div class="col text-muted small">Kampus ini belum memiliki prodi.</div>';
            return;
        }

        jurusans.forEach(function (jurusan) {
            const wrapper = document.createElement('div');
            wrapper.className = 'col';

            const checkId = 'jurusan_' + jurusan.id;
            const checked = selectedJurusanIds.includes(String(jurusan.id));

            wrapper.innerHTML = '<div class="form-check"><input class="form-check-input prodi-checkbox" type="' + (allowMultiple ? 'checkbox' : 'radio') + '" name="jurusan_ids[]" id="' + checkId + '" value="' + jurusan.id + '"><label class="form-check-label" for="' + checkId + '">' + jurusan.nama + '</label></div>';

            const input = wrapper.querySelector('input');
            input.checked = checked;
            prodiContainer.appendChild(wrapper);
        });

        updateProdiState();
        bindProdiCheckboxes();
    }

    function bindProdiCheckboxes() {
        prodiContainer.querySelectorAll('.prodi-checkbox').forEach(function (input) {
            input.addEventListener('change', function () {
                if (input.checked) {
                    semuaProdiCheckbox.checked = false;
                }
            });
        });
    }

    function updateProdiState() {
        const disable = semuaProdiCheckbox.checked;
        prodiContainer.querySelectorAll('.prodi-checkbox').forEach(function (input) {
            input.disabled = disable;
            if (disable) {
                input.checked = false;
            }
        });
    }

    semuaProdiCheckbox.addEventListener('change', updateProdiState);

    kampusSelect.addEventListener('change', function () {
        selectedJurusanIds.length = 0;
        renderProdiCheckboxes();
    });

    renderProdiCheckboxes();
});
</script>
