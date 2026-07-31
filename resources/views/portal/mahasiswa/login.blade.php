@extends('portal.layout')

@section('title', 'Masuk')

@section('content')
    <div class="portal-orbit-wrap mx-auto max-w-md">
        <div class="portal-glow"></div>
        <div class="portal-orbit portal-orbit-one"></div>
        <div class="portal-orbit portal-orbit-two"></div>
        <div class="portal-card rounded-2xl bg-white p-8">
            <h1 class="text-2xl font-black text-slate-900">Cek Hasil Pendaftaran</h1>
            <p class="mt-2 text-sm text-slate-600">Masuk pakai nomor seleksi (kode PMB) dan password kamu.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('portal.mahasiswa.login.attempt') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="kode_pmb" class="block text-sm font-bold text-slate-700">Nomor Seleksi (Kode PMB)</label>
                    <input type="text" id="kode_pmb" name="kode_pmb" value="{{ old('kode_pmb') }}" required autofocus
                        class="mt-1 w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                    <input type="password" id="password" name="password" required
                        class="mt-1 w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-black text-white hover:bg-emerald-700">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-xs text-slate-500">
                Koordinator? <a href="{{ route('portal.koordinator.login') }}" class="font-bold text-emerald-700 hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
@endsection
