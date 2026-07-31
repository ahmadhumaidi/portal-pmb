<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Mahasiswa') | Portal PMB UKCW</title>
    <link rel="icon" href="{{ asset('images/logo-ukcw.png') }}">
    @vite(['resources/css/portal.css'])
</head>
<body class="portal-bg min-h-screen text-slate-900">
    <header class="portal-header sticky top-0 z-10 border-b border-slate-200 bg-white/90">
        <div class="mx-auto flex max-w-4xl items-center justify-between gap-4 px-4 py-3">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo-ukcw.png') }}" alt="Logo Universitas Kristen Cipta Wacana" class="portal-logo">
                <span class="hidden text-sm font-black leading-tight text-slate-800 sm:block">
                    Portal PMB<br>
                    <span class="text-xs font-semibold text-slate-500">Universitas Kristen Cipta Wacana</span>
                </span>
            </a>
            @hasSection('logout-route')
                <form method="POST" action="@yield('logout-route')">
                    @csrf
                    <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-200">
                        Keluar
                    </button>
                </form>
            @endif
        </div>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-8">
        @yield('content')
    </main>

    <footer class="portal-footer mt-12 py-6 text-center text-xs text-white/70">
        &copy; {{ date('Y') }} Universitas Kristen Cipta Wacana &mdash; Portal Penerimaan Mahasiswa Baru
    </footer>
</body>
</html>
