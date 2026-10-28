<footer class="relative z-10 border-t border-cream-100/10 bg-cacao-950">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-3">
        <div>
            <p class="font-display text-2xl font-semibold">{{ __('Ruta del Chocolate') }}</p>
            <p class="mt-3 max-w-xs text-sm text-cream-100/60">{{ __('Sucre, la capital del chocolate de Bolivia. Visita sus fábricas, conoce a sus maestros chocolateros y llévate un pedacito de la ciudad blanca.') }}</p>
        </div>
        <div class="text-sm text-cream-100/70">
            <p class="eyebrow mb-3">{{ __('Explora') }}</p>
            <a href="{{ route('factories.index') }}" class="block py-1 hover:text-cream-50">{{ __('Fábricas') }}</a>
            <a href="{{ route('passport.index') }}" class="block py-1 hover:text-cream-50">{{ __('Mi ruta') }}</a>
            <a href="{{ route('tours.index') }}" class="block py-1 hover:text-cream-50">{{ __('Tours') }}</a>
            <a href="{{ route('shop') }}" class="block py-1 hover:text-cream-50">{{ __('Tienda') }}</a>
            <a href="{{ route('cart.index') }}" class="block py-1 hover:text-cream-50">{{ __('Carrito') }}</a>
        </div>
        <div class="text-sm text-cream-100/70">
            <p class="eyebrow mb-3">{{ __('Cómo funciona') }}</p>
            <p>{{ __('Reserva online y paga al llegar. Tus chocolates te esperan en la fábrica o tienda que elijas.') }}</p>
        </div>
    </div>
    <p class="pb-8 text-center text-xs text-cream-100/40">© {{ date('Y') }} {{ __('Ruta del Chocolate') }} · Sucre, Bolivia</p>
</footer>
