@extends('layouts.app')

@section('title', 'Pendaftar Landing Page')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Pendaftar Landing Page</h2>
        <p class="text-secondary mb-0">Data calon mahasiswa yang mengirim formulir dari halaman publik.</p>
    </div>
    <span class="badge rounded-pill text-bg-primary fs-6 px-3 py-2">{{ $registrations->total() }} pendaftar</span>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="px-4 py-3">Calon mahasiswa</th>
                    <th class="py-3">Asal</th>
                    <th class="py-3">Pilihan</th>
                    <th class="py-3">Kontak</th>
                    <th class="py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $registration)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-semibold">{{ $registration->name }}</div>
                            <small class="text-secondary">{{ $registration->education_level }}</small>
                        </td>
                        <td><div>{{ $registration->school }}</div><small class="text-secondary">{{ $registration->city }}</small></td>
                        <td><div>{{ $registration->kampus?->nama_kampus ?? '—' }}</div><small class="text-secondary">{{ $registration->jurusan?->nama_jurusan ?? '—' }}</small></td>
                        <td>
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $registration->whatsapp) }}" target="_blank" class="text-decoration-none"><i class="bi bi-whatsapp me-1"></i>{{ $registration->whatsapp }}</a>
                            @if($registration->email)<div><small>{{ $registration->email }}</small></div>@endif
                        </td>
                        <td class="text-nowrap">{{ $registration->created_at->translatedFormat('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-5 text-center text-secondary">Belum ada pendaftaran yang masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registrations->hasPages())
        <div class="card-footer bg-white border-0 px-4 py-3">{{ $registrations->links() }}</div>
    @endif
</div>
@endsection
