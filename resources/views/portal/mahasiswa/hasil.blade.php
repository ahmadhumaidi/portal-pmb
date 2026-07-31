@extends('portal.layout')

@section('title', 'Hasil Pendaftaran')

@section('logout-route', route('portal.mahasiswa.logout'))

@php
    $statusLabels = [
        'baru' => ['Pendaftaran Diterima', 'bg-slate-100 text-slate-700'],
        'proses' => ['Sedang Diproses', 'bg-sky-100 text-sky-700'],
        'berkas_kurang' => ['Berkas Belum Lengkap', 'bg-amber-100 text-amber-700'],
        'siap_registrasi' => ['Siap Registrasi', 'bg-teal-100 text-teal-700'],
        'terdaftar' => ['Terdaftar', 'bg-emerald-100 text-emerald-700'],
        'selesai' => ['Selesai', 'bg-emerald-100 text-emerald-800'],
        'dibatalkan' => ['Dibatalkan', 'bg-red-100 text-red-700'],
    ];
    [$statusLabel, $statusClass] = $statusLabels[$mahasiswa->status_pendaftaran] ?? [$mahasiswa->status_pendaftaran, 'bg-slate-100 text-slate-700'];
@endphp

@section('content')
    <div class="rounded-2xl bg-white p-8 shadow-sm">
        <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">Nomor Seleksi: {{ $mahasiswa->kode_pmb }}</p>
        <h1 class="mt-2 text-2xl font-black text-slate-900">{{ $mahasiswa->nama_mahasiswa }}</h1>

        <div class="mt-6 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-black {{ $statusClass }}">
            {{ $statusLabel }}
        </div>

        <dl class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-slate-50 p-4">
                <dt class="text-xs font-bold uppercase text-slate-500">Kampus</dt>
                <dd class="mt-1 font-bold text-slate-900">{{ $mahasiswa->kampus?->nama_kampus ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 p-4">
                <dt class="text-xs font-bold uppercase text-slate-500">Program Studi</dt>
                <dd class="mt-1 font-bold text-slate-900">{{ $mahasiswa->jurusan?->nama_jurusan ?? '-' }}</dd>
            </div>
            @if($mahasiswa->koordinator)
                <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-500">Koordinator</dt>
                    <dd class="mt-1 font-bold text-slate-900">{{ $mahasiswa->koordinator->nama_koordinator }}</dd>
                </div>
            @endif
        </dl>

        @if($mahasiswa->keterangan)
            <div class="mt-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-900">
                <p class="font-bold">Keterangan</p>
                <p class="mt-1">{{ $mahasiswa->keterangan }}</p>
            </div>
        @endif
    </div>
@endsection
