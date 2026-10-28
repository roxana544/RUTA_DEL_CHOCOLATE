<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') · Admin Ruta del Chocolate</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cacao-950">
    <div class="flex min-h-screen flex-col md:flex-row">
        <aside class="border-b border-cream-100/10 bg-cacao-900 md:w-60 md:shrink-0 md:border-b-0 md:border-r">
            <div class="flex items-center gap-2 p-5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-gold font-display font-extrabold text-cacao-950">R</span>
                <span class="font-display font-semibold">Admin</span>
            </div>
            <nav class="flex flex-wrap gap-1 px-3 pb-4 text-sm md:flex-col">
                @php($u = auth()->user())
                <p class="px-3 pt-2 text-[10px] font-semibold uppercase tracking-wider text-cream-100/40">Eventos</p>
                @foreach ([
                    'manage.dashboard' => ['Resumen de eventos', 'manage.dashboard'],
                    'manage.events.index' => ['Eventos', 'manage.events.*'],
                    'manage.mine' => ['Mis eventos', 'manage.mine'],
                    'manage.events.create' => ['Crear evento', 'manage.events.create'],
                    'manage.activities.index' => ['Actividades', 'manage.activities.*'],
                ] as $route => [$label, $pattern])
                    <a href="{{ route($route) }}" class="rounded-lg px-3 py-2 {{ request()->routeIs($pattern) && ! ($route === 'manage.events.index' && request()->routeIs('manage.events.create')) ? 'bg-gold/15 text-gold' : 'text-cream-100/70 hover:bg-cream-100/5' }}">{{ $label }}</a>
                @endforeach
                @if ($u->isAdmin())
                <p class="px-3 pt-3 text-[10px] font-semibold uppercase tracking-wider text-cream-100/40">Administración</p>
                @foreach ([
                    'admin.dashboard' => 'Resumen',
                    'admin.bookings.index' => 'Reservas',
                    'admin.orders.index' => 'Pedidos',
                    'admin.tours.index' => 'Tours',
                    'admin.products.index' => 'Productos',
                    'admin.factories.index' => 'Fábricas',
                    'admin.stores.index' => 'Puntos de recojo',
                    'admin.checkpoints.index' => 'Checkpoints',
                    'admin.users.index' => 'Usuarios',
                ] as $route => $label)
                    @php($base = \Illuminate\Support\Str::beforeLast($route, '.'))
                    <a href="{{ route($route) }}" class="rounded-lg px-3 py-2 {{ request()->routeIs($route === 'admin.dashboard' ? $route : $base.'.*') ? 'bg-gold/15 text-gold' : 'text-cream-100/70 hover:bg-cream-100/5' }}">{{ $label }}</a>
                @endforeach
                @endif
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-cream-100/50 hover:bg-cream-100/5" target="_blank">Ver sitio ↗</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full rounded-lg px-3 py-2 text-left text-cream-100/50 hover:bg-cream-100/5">Cerrar sesión</button>
                </form>
            </nav>
        </aside>
        <main class="min-w-0 flex-1 p-5 md:p-10">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-gold/40 bg-gold/10 px-4 py-3 text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-xl border border-red-300/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>
            @endif
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <h1 class="font-display text-3xl font-semibold">@yield('title')</h1>
                @yield('actions')
            </div>
            @yield('content')
        </main>
    </div>
</body>
</html>
