@extends('portal.layout')

@section('title', 'Hasil Mahasiswa')

@section('logout-route', route('portal.koordinator.logout'))

@section('content')
    @if(($pengumumans ?? collect())->isNotEmpty())
        <div class="portal-card rounded-2xl bg-white p-6 mb-6">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Pengumuman</p>
            <div class="mt-3 space-y-3">
                @foreach($pengumumans as $pengumuman)
                    <div class="rounded-xl bg-amber-50 p-4">
                        <p class="font-black text-slate-900">{{ $pengumuman->judul }}</p>
                        <p class="mt-1 text-sm text-slate-700 whitespace-pre-line">{{ $pengumuman->isi }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ $pengumuman->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="portal-card rounded-2xl bg-white p-6 sm:p-8">
        <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">{{ $koordinator->kode_koordinator }}</p>
        <h1 class="mt-2 text-2xl font-black text-slate-900">Hasil Mahasiswa</h1>
        <p class="mt-1 text-sm text-slate-600">{{ $mahasiswas->total() }} mahasiswa yang kamu bawa.</p>

        <form method="GET" class="mt-6">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode PMB..."
                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-black uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kode PMB</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">NIM</th>
                        <th class="px-4 py-3">No. Seri Ijazah</th>
                        <th class="px-4 py-3">PDDIKTI</th>
                        <th class="px-4 py-3">Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($mahasiswas as $mahasiswa)
                        @php
                            $hasil = $mahasiswa->hasil;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-bold">{{ $mahasiswa->nama_mahasiswa }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $mahasiswa->kode_pmb }}</td>
                            <td class="px-4 py-3">{{ $hasil?->status_kelulusan ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $hasil?->nim ?: 'Dalam Proses' }}</td>
                            <td class="px-4 py-3">{{ $hasil?->nomor_seri_ijazah ?: 'Pengajuan PIN' }}</td>
                            <td class="px-4 py-3">
                                @if($hasil?->link_pddikti)
                                    <a href="{{ $hasil->link_pddikti }}" target="_blank" class="font-bold text-emerald-700 hover:underline">Buka</a>
                                @else
                                    Menunggu sinkronisasi NeoFeeder
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(['scan_ijazah' => 'Ijazah', 'scan_transkrip' => 'Transkrip', 'screenshot_pisn' => 'PISN'] as $field => $label)
                                        @php $path = match($field) { 'scan_ijazah' => $hasil?->scan_ijazah_path, 'scan_transkrip' => $hasil?->scan_transkrip_path, 'screenshot_pisn' => $hasil?->screenshot_pisn_path }; @endphp
                                        @if($path)
                                            <a href="{{ route('portal.koordinator.hasil.file', [$mahasiswa, $field]) }}" target="_blank"
                                                class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-black text-white hover:bg-emerald-700">
                                                {{ $label }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center font-semibold text-slate-500">Belum ada mahasiswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $mahasiswas->links() }}
        </div>
    </div>
@endsection
