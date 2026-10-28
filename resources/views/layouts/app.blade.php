<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') · @endif{{ __('Ruta del Chocolate') }} — Sucre</title>
    <meta name="description" content="{{ __('Descubre y reserva tours por las fábricas de chocolate de Sucre, Bolivia, y compra sus chocolates para recogerlos en tu visita.') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    @include('partials.nav')

    @if (session('status'))
        <div class="fixed inset-x-0 top-20 z-50 flex justify-center px-4">
            <div class="glass rounded-full border border-gold/40 px-5 py-2.5 text-sm shadow-xl">{{ session('status') }}</div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
