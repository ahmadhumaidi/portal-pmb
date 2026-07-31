@extends('portal.layout')

@section('title', 'Mahasiswa Saya')

@section('logout-route', route('portal.koordinator.logout'))

@php
    $statusLabels = [
        'baru' => ['Baru', 'bg-slate-100 text-slate-700'],
        'proses' => ['Diproses', 'bg-sky-100 text-sky-700'],
        'berkas_kurang' => ['Berkas Kurang', 'bg-amber-100 text-amber-700'],
        'siap_registrasi' => ['Siap Registrasi', 'bg-teal-100 text-teal-700'],
        'terdaftar' => ['Terdaftar', 'bg-emerald-100 text-emerald-700'],
        'selesai' => ['Selesai', 'bg-emerald-100 text-emerald-800'],
        'dibatalkan' => ['Dibatalkan', 'bg-red-100 text-red-700'],
    ];
@endphp

@section('content')
    <div class="rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">{{ $koordinator->kode_koordinator }}</p>
        <h1 class="mt-2 text-2xl font-black text-slate-900">{{ $koordinator->nama_koordinator }}</h1>
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
                        <th class="px-4 py-3">Kampus / Jurusan</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($mahasiswas as $mahasiswa)
                        @php
                            [$statusLabel, $statusClass] = $statusLabels[$mahasiswa->status_pendaftaran] ?? [$mahasiswa->status_pendaftaran, 'bg-slate-100 text-slate-700'];
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-bold">{{ $mahasiswa->nama_mahasiswa }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $mahasiswa->kode_pmb }}</td>
                            <td class="px-4 py-3">{{ $mahasiswa->kampus?->nama_kampus }} / {{ $mahasiswa->jurusan?->nama_jurusan }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center font-semibold text-slate-500">Belum ada mahasiswa.</td>
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
