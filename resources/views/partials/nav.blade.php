@php($cartCount = app(\App\Support\Cart::class)->count())
<header class="glass fixed inset-x-0 top-0 z-40 border-b border-cream-100/10">
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="grid h-8 w-8 place-items-center rounded-lg bg-gold font-display text-lg font-extrabold text-cacao-950">R</span>
            <span class="font-display text-lg font-semibold tracking-tight">{{ __('Ruta del Chocolate') }}</span>
        </a>

        <div class="hidden items-center gap-8 text-sm text-cream-100/80 md:flex">
            <a href="{{ route('home') }}#ruta" class="hover:text-cream-50">{{ __('La ruta') }}</a>
            <a href="{{ route('factories.index') }}" class="hover:text-cream-50 {{ request()->routeIs('factories.*') ? 'text-gold' : '' }}">{{ __('Fábricas') }}</a>
            <a href="{{ route('tours.index') }}" class="hover:text-cream-50 {{ request()->routeIs('tours.*') ? 'text-gold' : '' }}">{{ __('Tours') }}</a>
            <a href="{{ route('events.index') }}" class="hover:text-cream-50 {{ request()->routeIs('events.*') ? 'text-gold' : '' }}">{{ __('Eventos') }}</a>
            <a href="{{ route('shop') }}" class="hover:text-cream-50 {{ request()->routeIs('shop') ? 'text-gold' : '' }}">{{ __('Tienda') }}</a>
            @auth
                <a href="{{ auth()->user()->canManageEvents() ? route('manage.dashboard') : route('panel') }}" class="hover:text-cream-50 {{ request()->routeIs('panel', 'passport.*', 'manage.*') ? 'text-gold' : '' }}">{{ auth()->user()->canManageEvents() ? __('Gestión') : __('Mi panel') }}</a>
            @endauth
        </div>

        <div class="flex items-center gap-3">
            <div class="flex overflow-hidden rounded-full border border-cream-100/20 text-xs font-semibold">
                @foreach (['es' => 'ES', 'en' => 'EN'] as $code => $label)
                    <a href="{{ route('locale', $code) }}" class="px-2.5 py-1 {{ app()->getLocale() === $code ? 'bg-cream-100 text-cacao-950' : 'text-cream-100/70 hover:text-cream-50' }}">{{ $label }}</a>
                @endforeach
            </div>
            @auth
                <form method="POST" action="{{ route('logout') }}" class="hidden md:block">@csrf<button class="text-xs text-cream-100/70 hover:text-cream-50">{{ __('Salir') }}</button></form>
            @else
                <a href="{{ route('login') }}" class="hidden text-xs font-semibold text-cream-100/80 hover:text-cream-50 md:block">{{ __('Entrar') }}</a>
            @endauth
            <a href="{{ route('cart.index') }}" class="relative grid h-9 w-9 place-items-center rounded-full hover:bg-cream-100/10" aria-label="{{ __('Carrito') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/></svg>
                @if ($cartCount)
                    <span class="absolute -right-0.5 -top-0.5 grid h-4.5 min-w-4.5 place-items-center rounded-full bg-gold px-1 text-[10px] font-bold text-cacao-950">{{ $cartCount }}</span>
                @endif
            </a>
            <button type="button" class="grid h-9 w-9 place-items-center rounded-full hover:bg-cream-100/10 md:hidden" data-menu-toggle aria-label="Menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </nav>
    <div class="hidden border-t border-cream-100/10 px-4 py-3 md:hidden" data-menu>
        <a href="{{ route('home') }}#ruta" class="block py-2">{{ __('La ruta') }}</a>
        <a href="{{ route('factories.index') }}" class="block py-2">{{ __('Fábricas') }}</a>
        <a href="{{ route('tours.index') }}" class="block py-2">{{ __('Tours') }}</a>
        <a href="{{ route('events.index') }}" class="block py-2">{{ __('Eventos') }}</a>
        <a href="{{ route('shop') }}" class="block py-2">{{ __('Tienda') }}</a>
        @auth<a href="{{ auth()->user()->canManageEvents() ? route('manage.dashboard') : route('panel') }}" class="block py-2">{{ auth()->user()->canManageEvents() ? __('Gestión') : __('Mi panel') }}</a>@endauth
        @guest<a href="{{ route('login') }}" class="block py-2">{{ __('Ingresar') }}</a>@endguest
    </div>
</header>
