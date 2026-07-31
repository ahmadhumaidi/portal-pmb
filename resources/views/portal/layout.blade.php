<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Mahasiswa') | Portal PMB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="bg-white shadow-sm">
        <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-4">
            <span class="text-lg font-black text-emerald-700">Portal PMB</span>
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
</body>
</html>
