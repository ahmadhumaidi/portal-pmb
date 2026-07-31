@extends('portal.layout')

@section('title', 'Masuk Koordinator')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-2xl bg-white p-8 shadow-sm">
            <h1 class="text-2xl font-black text-slate-900">Portal Koordinator</h1>
            <p class="mt-2 text-sm text-slate-600">Masuk pakai kode koordinator dan password kamu.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('portal.koordinator.login.attempt') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="kode_koordinator" class="block text-sm font-bold text-slate-700">Kode Koordinator</label>
                    <input type="text" id="kode_koordinator" name="kode_koordinator" value="{{ old('kode_koordinator') }}" required autofocus
                        class="mt-1 w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        placeholder="KRD-0001">
                </div>
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                    <input type="password" id="password" name="password" required
                        class="mt-1 w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        placeholder="Default: sukses1">
                </div>
                <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-black text-white hover:bg-emerald-700">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-xs text-slate-500">
                Mahasiswa? <a href="{{ route('portal.mahasiswa.login') }}" class="font-bold text-emerald-700 hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
@endsection
