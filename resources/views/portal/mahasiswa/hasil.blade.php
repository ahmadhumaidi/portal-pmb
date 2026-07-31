@extends('portal.layout')

@section('title', 'Hasil')

@section('logout-route', route('portal.mahasiswa.logout'))

@php
    $hasil = $mahasiswa->hasil;
    $statusLabels = [
        'belum_siap' => ['Belum Siap', 'bg-slate-100 text-slate-700'],
        'siap_dikirim' => ['Siap Dikirim', 'bg-sky-100 text-sky-700'],
        'sudah_dikirim' => ['Sudah Dikirim', 'bg-teal-100 text-teal-700'],
        'sudah_diterima' => ['Sudah Diterima', 'bg-emerald-100 text-emerald-700'],
        'perlu_revisi' => ['Perlu Revisi', 'bg-amber-100 text-amber-700'],
    ];
    $status = $hasil?->status_kirim;
    [$statusLabel, $statusClass] = $statusLabels[$status] ?? ['Belum Tersedia', 'bg-slate-100 text-slate-700'];

    $fileFields = [
        'scan_ijazah' => ['label' => 'Ijazah', 'path' => $hasil?->scan_ijazah_path],
        'scan_transkrip' => ['label' => 'Transkrip', 'path' => $hasil?->scan_transkrip_path],
        'screenshot_pisn' => ['label' => 'PISN', 'path' => $hasil?->screenshot_pisn_path],
    ];
@endphp

@section('content')
    <div class="rounded-2xl bg-white p-8 shadow-sm">
        <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">Nomor Seleksi: {{ $mahasiswa->kode_pmb }}</p>
        <h1 class="mt-2 text-2xl font-black text-slate-900">{{ $mahasiswa->nama_mahasiswa }}</h1>

        <div class="mt-6 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-black {{ $statusClass }}">
            Status: {{ $statusLabel }}
        </div>

        <dl class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-slate-50 p-4">
                <dt class="text-xs font-bold uppercase text-slate-500">NIM</dt>
                <dd class="mt-1 font-bold text-slate-900">{{ $hasil?->nim ?: '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 p-4">
                <dt class="text-xs font-bold uppercase text-slate-500">No. Seri Ijazah</dt>
                <dd class="mt-1 font-bold text-slate-900">{{ $hasil?->nomor_seri_ijazah ?: '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2">
                <dt class="text-xs font-bold uppercase text-slate-500">Link PDDIKTI</dt>
                <dd class="mt-1 font-bold text-slate-900">
                    @if($hasil?->link_pddikti)
                        <a href="{{ $hasil->link_pddikti }}" target="_blank" class="text-emerald-700 hover:underline break-all">{{ $hasil->link_pddikti }}</a>
                    @else
                        -
                    @endif
                </dd>
            </div>
        </dl>

        <div class="mt-8">
            <p class="text-xs font-bold uppercase text-slate-500">Dokumen</p>
            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                @foreach($fileFields as $field => $meta)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4">
                        <span class="font-bold text-slate-900">{{ $meta['label'] }}</span>
                        @if($meta['path'])
                            <a href="{{ route('portal.mahasiswa.hasil.file', $field) }}" target="_blank"
                                class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-black text-white hover:bg-emerald-700">
                                Lihat
                            </a>
                        @else
                            <span class="text-xs font-semibold text-slate-400">Belum ada</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        @if($mahasiswa->keterangan)
            <div class="mt-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-900">
                <p class="font-bold">Keterangan</p>
                <p class="mt-1">{{ $mahasiswa->keterangan }}</p>
            </div>
        @endif
    </div>
@endsection
