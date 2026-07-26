@csrf

<div class="row g-3">
    <div class="col-md-6"><label for="nama_mahasiswa" class="form-label">Nama Mahasiswa</label><input type="text" id="nama_mahasiswa" name="nama_mahasiswa" class="form-control @error('nama_mahasiswa') is-invalid @enderror" value="{{ old('nama_mahasiswa', $mahasiswa->nama_mahasiswa ?? '') }}" required autofocus>@error('nama_mahasiswa')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-3"><label for="nik" class="form-label">NIK</label><input type="text" id="nik" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $mahasiswa->nik ?? '') }}">@error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-3"><label for="nisn" class="form-label">NISN</label><input type="text" id="nisn" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn', $mahasiswa->nisn ?? '') }}">@error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

    <div class="col-md-6"><label for="kampus_id" class="form-label">Kampus</label><select id="kampus_id" name="kampus_id" class="form-select @error('kampus_id') is-invalid @enderror" required><option value="">Pilih Kampus</option>@foreach ($kampuses as $kampus)<option value="{{ $kampus->id }}" @selected(old('kampus_id', $mahasiswa->kampus_id ?? '') == $kampus->id)>{{ $kampus->nama_kampus }}</option>@endforeach</select>@error('kampus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label for="jurusan_id" class="form-label">Jurusan</label><select id="jurusan_id" name="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror" data-selected-jurusan="{{ old('jurusan_id', $mahasiswa->jurusan_id ?? '') }}" required><option value="">Pilih kampus dulu</option></select>@error('jurusan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

    <div class="col-md-4"><label for="pic_staff_id" class="form-label">PIC Staff</label><select id="pic_staff_id" name="pic_staff_id" class="form-select @error('pic_staff_id') is-invalid @enderror"><option value="">Belum ditentukan</option>@foreach ($staffs as $staff)<option value="{{ $staff->id }}" @selected(old('pic_staff_id', $mahasiswa->pic_staff_id ?? '') == $staff->id)>{{ $staff->nama_staff }}</option>@endforeach</select>@error('pic_staff_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label for="status_pendaftaran" class="form-label">Status</label><select id="status_pendaftaran" name="status_pendaftaran" class="form-select @error('status_pendaftaran') is-invalid @enderror" required>@foreach ($statuses as $option)<option value="{{ $option }}" @selected(old('status_pendaftaran', $mahasiswa->status_pendaftaran ?? 'baru') === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>@endforeach</select>@error('status_pendaftaran')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label for="harga_kesepakatan" class="form-label">Harga Kesepakatan</label><input type="number" id="harga_kesepakatan" name="harga_kesepakatan" min="0" step="1" class="form-control @error('harga_kesepakatan') is-invalid @enderror" value="{{ old('harga_kesepakatan', $mahasiswa->harga_kesepakatan ?? 0) }}">@error('harga_kesepakatan')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

    <div class="col-md-4"><label for="tempat_lahir" class="form-label">Tempat Lahir</label><input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir ?? '') }}">@error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label for="tanggal_lahir" class="form-label">Tanggal Lahir</label><input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', isset($mahasiswa) && $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('Y-m-d') : '') }}">@error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label for="jenis_kelamin" class="form-label">Jenis Kelamin</label><select id="jenis_kelamin" name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror"><option value="">Pilih</option><option value="L" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin ?? '') === 'L')>Laki-laki</option><option value="P" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin ?? '') === 'P')>Perempuan</option></select>@error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

    <div class="col-md-4"><label for="agama" class="form-label">Agama</label><input type="text" id="agama" name="agama" class="form-control @error('agama') is-invalid @enderror" value="{{ old('agama', $mahasiswa->agama ?? '') }}">@error('agama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label for="kewarganegaraan" class="form-label">Kewarganegaraan</label><input type="text" id="kewarganegaraan" name="kewarganegaraan" class="form-control @error('kewarganegaraan') is-invalid @enderror" value="{{ old('kewarganegaraan', $mahasiswa->kewarganegaraan ?? '') }}">@error('kewarganegaraan')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label for="tahun_lulus" class="form-label">Tahun Lulus</label><input type="number" id="tahun_lulus" name="tahun_lulus" min="1900" max="2100" class="form-control @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus', $mahasiswa->tahun_lulus ?? '') }}">@error('tahun_lulus')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

    <div class="col-md-4"><label for="nomor_whatsapp" class="form-label">WhatsApp</label><input type="text" id="nomor_whatsapp" name="nomor_whatsapp" class="form-control @error('nomor_whatsapp') is-invalid @enderror" value="{{ old('nomor_whatsapp', $mahasiswa->nomor_whatsapp ?? '') }}">@error('nomor_whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label for="email" class="form-label">Email</label><input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $mahasiswa->email ?? '') }}">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label for="nama_ibu" class="form-label">Nama Ibu</label><input type="text" id="nama_ibu" name="nama_ibu" class="form-control @error('nama_ibu') is-invalid @enderror" value="{{ old('nama_ibu', $mahasiswa->nama_ibu ?? '') }}">@error('nama_ibu')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

    <div class="col-md-6"><label for="asal_sekolah" class="form-label">Asal Sekolah</label><input type="text" id="asal_sekolah" name="asal_sekolah" class="form-control @error('asal_sekolah') is-invalid @enderror" value="{{ old('asal_sekolah', $mahasiswa->asal_sekolah ?? '') }}">@error('asal_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6">
        <label for="koordinator_id" class="form-label">Koordinator Kelas</label>
        <div class="input-group">
            <select id="koordinator_id" name="koordinator_id" class="form-select @error('koordinator_id') is-invalid @enderror">
                <option value="">Belum ditentukan</option>
                @foreach ($koordinators as $koordinator)
                    <option value="{{ $koordinator->id }}" @selected(old('koordinator_id', $mahasiswa->koordinator_id ?? '') == $koordinator->id)>{{ $koordinator->nama_koordinator }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#tambahKoordinatorModal" title="Tambah koordinator baru">
                <i class="bi bi-plus-lg"></i>
            </button>
            @error('koordinator_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6"><label for="alamat" class="form-label">Alamat KTP</label><textarea id="alamat" name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $mahasiswa->alamat ?? '') }}</textarea>@error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-12"><label for="keterangan" class="form-label">Keterangan</label><textarea id="keterangan" name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $mahasiswa->keterangan ?? '') }}</textarea>@error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('mahasiswa.index') }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button></div>

<div class="modal fade" id="tambahKoordinatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="formTambahKoordinator">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Koordinator Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="nama_koordinator_baru" class="form-label">Nama Koordinator</label>
                    <input type="text" id="nama_koordinator_baru" class="form-control" placeholder="Nama koordinator kelas" autofocus>
                    <div id="koordinatorBaruError" class="text-danger small mt-1"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnSimpanKoordinator" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const kampusSelect = document.getElementById('kampus_id');
    const jurusanSelect = document.getElementById('jurusan_id');
    let selectedJurusan = jurusanSelect.dataset.selectedJurusan || '';
    const jurusanByKampus = @json($jurusans->groupBy('kampus_id')->map(fn ($items) => $items->map(fn ($jurusan) => [
        'id' => $jurusan->id,
        'nama' => trim($jurusan->nama_jurusan . ($jurusan->jenjang ? ' - ' . $jurusan->jenjang : '')),
    ])->values()));

    function renderJurusan() {
        const kampusId = kampusSelect.value;
        const jurusans = jurusanByKampus[kampusId] || [];

        jurusanSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = kampusId ? 'Pilih Jurusan' : 'Pilih kampus dulu';
        jurusanSelect.appendChild(placeholder);

        jurusans.forEach(function (jurusan) {
            const option = document.createElement('option');
            option.value = jurusan.id;
            option.textContent = jurusan.nama;
            option.selected = String(jurusan.id) === String(selectedJurusan);
            jurusanSelect.appendChild(option);
        });
    }

    kampusSelect.addEventListener('change', function () {
        selectedJurusan = '';
        renderJurusan();
    });

    renderJurusan();

    const koordinatorSelect = document.getElementById('koordinator_id');
    const koordinatorModalEl = document.getElementById('tambahKoordinatorModal');
    const koordinatorInput = document.getElementById('nama_koordinator_baru');
    const koordinatorError = document.getElementById('koordinatorBaruError');
    const koordinatorSubmitButton = document.getElementById('btnSimpanKoordinator');
    const koordinatorModal = window.bootstrap ? new window.bootstrap.Modal(koordinatorModalEl) : null;

    function showKoordinatorToast(message) {
        let container = document.getElementById('koordinatorToastContainer');

        if (!container) {
            container = document.createElement('div');
            container.id = 'koordinatorToastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = 1080;
            document.body.appendChild(container);
        }

        const toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center text-bg-success border-0';
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = '<div class="d-flex"><div class="toast-body"><i class="bi bi-check-circle me-1"></i>' + message + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(toastEl);

        if (window.bootstrap) {
            const toast = new window.bootstrap.Toast(toastEl, { delay: 4000 });
            toastEl.addEventListener('hidden.bs.toast', function () {
                toastEl.remove();
            });
            toast.show();
        } else {
            toastEl.classList.add('show');
            setTimeout(function () {
                toastEl.remove();
            }, 4000);
        }
    }

    koordinatorModalEl.addEventListener('shown.bs.modal', function () {
        koordinatorInput.focus();
    });

    koordinatorModalEl.addEventListener('hidden.bs.modal', function () {
        koordinatorInput.value = '';
        koordinatorError.textContent = '';
        koordinatorInput.classList.remove('is-invalid');
    });

    function simpanKoordinatorBaru() {
        const nama = koordinatorInput.value.trim();
        koordinatorError.textContent = '';
        koordinatorInput.classList.remove('is-invalid');

        if (!nama) {
            koordinatorError.textContent = 'Nama koordinator wajib diisi.';
            koordinatorInput.classList.add('is-invalid');
            return;
        }

        koordinatorSubmitButton.disabled = true;

        fetch('{{ route('koordinator.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ nama_koordinator: nama, status_aktif: 1 }),
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (!result.ok) {
                    const message = result.data.errors && result.data.errors.nama_koordinator
                        ? result.data.errors.nama_koordinator[0]
                        : (result.data.message || 'Gagal menambahkan koordinator.');
                    koordinatorError.textContent = message;
                    koordinatorInput.classList.add('is-invalid');
                    return;
                }

                const option = document.createElement('option');
                option.value = result.data.id;
                option.textContent = result.data.nama_koordinator;
                option.selected = true;
                koordinatorSelect.appendChild(option);

                if (koordinatorModal) {
                    koordinatorModal.hide();
                } else {
                    koordinatorModalEl.classList.remove('show');
                }

                showKoordinatorToast('Koordinator "' + result.data.nama_koordinator + '" berhasil ditambahkan.');
            })
            .catch(function () {
                koordinatorError.textContent = 'Terjadi kesalahan, silakan coba lagi.';
            })
            .finally(function () {
                koordinatorSubmitButton.disabled = false;
            });
    }

    koordinatorSubmitButton.addEventListener('click', simpanKoordinatorBaru);

    koordinatorInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            simpanKoordinatorBaru();
        }
    });
});
</script>
