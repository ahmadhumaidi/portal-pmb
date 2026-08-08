@extends('portal.layout')

@section('title', 'Hasil')

@section('logout-route', route('portal.mahasiswa.logout'))

@php
    $hasil = $mahasiswa->hasil;
    $statusKelulusan = $hasil?->status_kelulusan;

    $itemPembayaran = [
        'Biaya Pendidikan' => $mahasiswa->statusBiayaPendidikan(),
        'Wisuda' => $mahasiswa->statusPembayaranJenis('Wisuda'),
        'Almamater' => $mahasiswa->statusPembayaranJenis('Almamater'),
    ];

    $statusBadgeClass = [
        'Lunas' => 'bg-emerald-100 text-emerald-700',
        'Menunggu Verifikasi' => 'bg-amber-100 text-amber-700',
        'Belum Bayar' => 'bg-slate-100 text-slate-600',
        'Belum Lunas' => 'bg-slate-100 text-slate-600',
    ];

    $fileFields = [
        'scan_ijazah' => ['label' => 'Ijazah', 'path' => $hasil?->scan_ijazah_path],
        'scan_transkrip' => ['label' => 'Transkrip', 'path' => $hasil?->scan_transkrip_path],
        'screenshot_pisn' => ['label' => 'PISN', 'path' => $hasil?->screenshot_pisn_path],
    ];
@endphp

@section('content')
    @if(($pengumumans ?? collect())->isNotEmpty())
        <div class="portal-card rounded-2xl bg-white p-6 mb-6">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Pengumuman</p>
            <div class="mt-3 space-y-3">
                @foreach($pengumumans as $pengumuman)
                    <div class="rounded-xl bg-amber-50 p-4">
                        <p class="font-black text-slate-900">{{ $pengumuman->judul }}</p>
                        <p class="mt-1 text-sm text-slate-700 whitespace-pre-line">{{ $pengumuman->isi }}</p>
                        @if($pengumuman->lampiran_path)
                            <a href="{{ route('portal.pengumuman.lampiran', $pengumuman) }}" target="_blank"
                                class="mt-2 inline-flex items-center gap-1 text-sm font-bold text-emerald-700 hover:underline">
                                📎 Lihat Lampiran
                            </a>
                        @endif
                        <p class="mt-2 text-xs text-slate-400">{{ ($pengumuman->tanggal ?? $pengumuman->created_at)->translatedFormat('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="portal-card rounded-2xl bg-white p-8">
        <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">Nomor Seleksi: {{ $mahasiswa->kode_pmb }}</p>
        <h1 class="mt-2 text-2xl font-black text-slate-900">{{ $mahasiswa->nama_mahasiswa }}</h1>

        <div class="mt-6 flex flex-wrap gap-3">
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-black text-slate-700">
                Status: {{ $statusKelulusan ?: 'Belum Tersedia' }}
            </div>
        </div>

        <dl class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-slate-50 p-4">
                <dt class="text-xs font-bold uppercase text-slate-500">NIM</dt>
                <dd class="mt-1 font-bold text-slate-900">{{ $hasil?->nim ?: 'Dalam Proses' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 p-4">
                <dt class="text-xs font-bold uppercase text-slate-500">No. Seri Ijazah</dt>
                <dd class="mt-1 font-bold text-slate-900">{{ $hasil?->nomor_seri_ijazah ?: 'Pengajuan PIN' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2">
                <dt class="text-xs font-bold uppercase text-slate-500">Link PDDIKTI</dt>
                <dd class="mt-1 font-bold text-slate-900">
                    @if($hasil?->link_pddikti)
                        <a href="{{ $hasil->link_pddikti }}" target="_blank" class="text-emerald-700 hover:underline break-all">{{ $hasil->link_pddikti }}</a>
                    @else
                        Menunggu sinkronisasi NeoFeeder
                    @endif
                </dd>
            </div>
        </dl>

        <div class="mt-8">
            <p class="text-xs font-bold uppercase text-slate-500">Status Pembayaran</p>
            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                @foreach($itemPembayaran as $jenis => $statusItem)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4">
                        <span class="font-bold text-slate-900">{{ $jenis }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusBadgeClass[$statusItem] }}">{{ $statusItem }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            <p class="text-xs font-bold uppercase text-slate-500">Dokumen Hasil S1</p>
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
    </div>
@endsection
