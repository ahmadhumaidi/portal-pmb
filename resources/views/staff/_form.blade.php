@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label for="nama_staff" class="form-label">Nama Staff</label>
        <input type="text" id="nama_staff" name="nama_staff" class="form-control @error('nama_staff') is-invalid @enderror" value="{{ old('nama_staff') }}" placeholder="Nama lengkap staff" required autofocus>
        @error('nama_staff')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@kampus.ac.id" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="kampus_id" class="form-label">Kampus</label>
        <select id="kampus_id" name="kampus_id" class="form-select @error('kampus_id') is-invalid @enderror">
            <option value="">Tanpa kampus</option>
            @foreach ($kampuses as $kampus)
                <option value="{{ $kampus->id }}" @selected(old('kampus_id') == $kampus->id)>{{ $kampus->nama_kampus }}</option>
            @endforeach
        </select>
        @error('kampus_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
            @foreach ($roles as $option)
                <option value="{{ $option }}" @selected(old('role', 'operator') === $option)>{{ ucfirst($option) }}</option>
            @endforeach
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch">
            <input type="hidden" name="status_aktif" value="0">
            <input class="form-check-input" type="checkbox" id="status_aktif" name="status_aktif" value="1" @checked(old('status_aktif', true))>
            <label class="form-check-label" for="status_aktif">Staff aktif</label>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('staff.index') }}" class="btn btn-light border">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>
        Simpan
    </button>
</div>

