@extends('layouts.app')

@section('title', 'Edit Landing Page')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Landing Page</h2>
        <p class="text-secondary mb-0">Atur informasi yang dilihat calon mahasiswa.</p>
    </div>
    <a href="{{ route('landing') }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-box-arrow-up-right me-2"></i>Lihat halaman</a>
</div>

<form method="POST" action="{{ route('landing.admin.update') }}">
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="alert alert-danger">Periksa kembali kolom yang ditandai.</div>
    @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
                <h5 class="fw-bold mb-4">Bagian utama</h5>
                <div class="mb-3">
                    <label class="form-label">Label kecil</label>
                    <input name="badge" value="{{ old('badge', $content['badge']) }}" class="form-control @error('badge') is-invalid @enderror">
                </div>
                <div class="mb-3">
                    <label class="form-label">Judul utama *</label>
                    <textarea name="headline" rows="2" class="form-control @error('headline') is-invalid @enderror" required>{{ old('headline', $content['headline']) }}</textarea>
                    @error('headline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-0">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="subheadline" rows="4" class="form-control">{{ old('subheadline', $content['subheadline']) }}</textarea>
                </div>
            </div></div>

            <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
                <h5 class="fw-bold mb-4">Keunggulan</h5>
                @foreach($content['features'] as $index => $feature)
                    <div class="p-3 rounded-3 bg-light mb-3">
                        <label class="form-label">Judul {{ $index + 1 }}</label>
                        <input name="features[{{ $index }}][title]" value="{{ old("features.$index.title", $feature['title']) }}" class="form-control mb-2">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="features[{{ $index }}][description]" rows="2" class="form-control">{{ old("features.$index.description", $feature['description']) }}</textarea>
                    </div>
                @endforeach
            </div></div>

            <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
                <h5 class="fw-bold mb-4">Alur pendaftaran</h5>
                <div class="row g-3">
                    @foreach($content['steps'] as $index => $step)
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <span class="badge text-bg-success mb-3">Langkah {{ $index + 1 }}</span>
                                <input name="steps[{{ $index }}][title]" value="{{ old("steps.$index.title", $step['title']) }}" class="form-control mb-2" aria-label="Judul langkah {{ $index + 1 }}">
                                <textarea name="steps[{{ $index }}][description]" rows="3" class="form-control" aria-label="Deskripsi langkah {{ $index + 1 }}">{{ old("steps.$index.description", $step['description']) }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div></div>

            <div class="card border-0 shadow-sm"><div class="card-body p-4">
                <h5 class="fw-bold mb-4">Pertanyaan umum</h5>
                @foreach($content['faqs'] as $index => $faq)
                    <div class="mb-4">
                        <label class="form-label">Pertanyaan {{ $index + 1 }}</label>
                        <input name="faqs[{{ $index }}][question]" value="{{ old("faqs.$index.question", $faq['question']) }}" class="form-control mb-2">
                        <textarea name="faqs[{{ $index }}][answer]" rows="2" class="form-control">{{ old("faqs.$index.answer", $faq['answer']) }}</textarea>
                    </div>
                @endforeach
            </div></div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
                <h5 class="fw-bold mb-4">Status pendaftaran</h5>
                <div class="mb-3"><label class="form-label">Status</label><input name="registration_status" value="{{ old('registration_status', $content['registration_status']) }}" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Batas pendaftaran</label><input type="date" name="registration_deadline" value="{{ old('registration_deadline', $content['registration_deadline'] instanceof \DateTimeInterface ? $content['registration_deadline']->format('Y-m-d') : $content['registration_deadline']) }}" class="form-control"></div>
                <div><label class="form-label">Pengumuman</label><textarea name="announcement" rows="3" class="form-control">{{ old('announcement', $content['announcement']) }}</textarea></div>
            </div></div>

            <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
                <h5 class="fw-bold mb-4">Tombol</h5>
                <label class="form-label">Tombol utama</label>
                <input name="primary_button_text" value="{{ old('primary_button_text', $content['primary_button_text']) }}" class="form-control mb-2">
                <input name="primary_button_url" value="{{ old('primary_button_url', $content['primary_button_url']) }}" class="form-control mb-3" placeholder="URL tujuan">
                <label class="form-label">Tombol kedua</label>
                <input name="secondary_button_text" value="{{ old('secondary_button_text', $content['secondary_button_text']) }}" class="form-control mb-2">
                <input name="secondary_button_url" value="{{ old('secondary_button_url', $content['secondary_button_url']) }}" class="form-control" placeholder="URL tujuan">
            </div></div>

            <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
                <h5 class="fw-bold mb-4">Kontak</h5>
                <div class="mb-3"><label class="form-label">WhatsApp</label><input name="contact_whatsapp" value="{{ old('contact_whatsapp', $content['contact_whatsapp']) }}" class="form-control"></div>
                <div><label class="form-label">Email</label><input type="email" name="contact_email" value="{{ old('contact_email', $content['contact_email']) }}" class="form-control"></div>
            </div></div>

            <button class="btn btn-primary btn-lg w-100"><i class="bi bi-check2-circle me-2"></i>Simpan perubahan</button>
        </div>
    </div>
</form>
@endsection
