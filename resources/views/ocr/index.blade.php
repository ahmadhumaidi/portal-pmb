@extends('layouts.app')

@section('title', 'AI OCR')
@section('page-title', 'AI OCR')
@section('page-description', 'Baca KTP, KK, dan Ijazah untuk mempercepat input biodata mahasiswa baru')

@section('content')
@unless ($ocrConfigured)
<div class="alert alert-warning">AI OCR belum dikonfigurasi. Pastikan service PaddleOCR (<code>paddleocr.service</code>) aktif dan <code>PADDLE_OCR_BASE_URL</code> di file <code>.env</code> sudah benar.</div>
@endunless

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small">Upload dokumen calon mahasiswa baru, biarkan AI membacanya, lalu terapkan hasilnya ke form pendaftaran.</div>
    @if (!empty($documents))
    <form method="POST" action="{{ route('ocr.reset') }}" onsubmit="return confirm('Reset sesi AI OCR ini? Dokumen yang sudah diupload akan dihapus.');">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-secondary">Mulai Ulang</button>
    </form>
    @endif
</div>

<div class="row g-4">
@foreach ($documentTypes as $jenis => $label)
    @php
        $result = $documents[$jenis] ?? null;
        $status = $result['status'] ?? null;
        $data = $result['data'] ?? [];
    @endphp
    <div class="col-lg-4">
        <div class="card dashboard-card border-0 h-100">
            <div class="card-header">
                <div>
                    <h5>{{ $label }}</h5>
                    <small>Status pembacaan dokumen</small>
                </div>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="mb-3">
                    @if ($status === 'berhasil')
                        <span class="badge text-bg-success">Berhasil dibaca</span>
                        <span class="badge text-bg-light border text-dark ms-1">Keyakinan: {{ $result['confidence'] }}%</span>
                    @elseif ($status === 'gagal')
                        <span class="badge text-bg-danger">Gagal dibaca</span>
                        @if (!empty($result['error']))
                            <div class="small text-danger mt-2">{{ $result['error'] }}</div>
                        @endif
                    @else
                        <span class="badge text-bg-light border text-dark">Belum diproses</span>
                    @endif
                </div>

                @if (!empty($data))
                <div class="detail-list mb-3">
                    @foreach ($data as $field => $value)
                        <div class="detail-label">{{ ucfirst(str_replace('_', ' ', $field)) }}</div>
                        <div class="detail-value">{{ $value ?: '-' }}</div>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('ocr.proses', $jenis) }}" enctype="multipart/form-data" class="mt-auto">
                    @csrf
                    <input type="file" name="dokumen" class="form-control form-control-sm mb-2" accept="image/*,.pdf" required>
                    <button type="submit" class="btn btn-outline-primary w-100" @disabled(!$ocrConfigured)>
                        <i class="bi bi-magic me-1"></i>{{ $status ? 'Proses Ulang OCR' : 'Proses OCR' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endforeach
</div>

@php
    $hasSuccessfulResult = collect($documents)->contains(fn ($result) => ($result['status'] ?? null) === 'berhasil');
@endphp
<div class="d-flex justify-content-end mt-4">
    <form method="POST" action="{{ route('ocr.terapkan') }}">
        @csrf
        <button type="submit" class="btn btn-primary" @disabled(!$hasSuccessfulResult)>
            <i class="bi bi-check2-circle me-1"></i>Terapkan ke Biodata
        </button>
    </form>
</div>
@endsection
