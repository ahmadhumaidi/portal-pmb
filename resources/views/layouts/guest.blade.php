<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh; padding: 24px;">
            <div class="mb-3">
                <a href="/">
                    <x-application-logo style="width: 64px; height: 64px; fill: var(--primary);" />
                </a>
            </div>

            <div class="card dashboard-card border-0 w-100" style="max-width: 420px;">
                <div class="card-body p-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
