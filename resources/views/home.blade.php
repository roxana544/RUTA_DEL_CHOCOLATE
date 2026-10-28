@extends('layouts.app')

@section('content')
    {{-- Fixed background map, driven by the scroll position (resources/js/scroll-map.js) --}}
    <div id="scroll-map" data-factories="{{ $mapData->toJson() }}" aria-hidden="true"></div>
    <div class="map-vignette"></div>

    <nav class="progress-rail hidden md:flex" style="opacity: 0" aria-label="{{ __('Progreso de la ruta') }}">
        <a href="#top" title="{{ __('Inicio') }}"></a>
        <a href="#ruta" title="Sucre"></a>
        @foreach ($factories as $factory)
            <a href="#fabrica-{{ $factory->slug }}" title="{{ $factory->name }}"></a>
        @endforeach
        <a href="#ruta-completa" title="{{ __('La ruta completa') }}"></a>
    </nav>

    <div class="story">
        {{-- Keyframe 0: Bolivia --}}
        <section id="top" class="flex min-h-screen items-center justify-center px-4 text-center"
                 data-map-step data-lat="-17.2" data-lng="-64.9" data-zoom="5" data-route="-1">
            <div data-hero class="will-change-transform">
                <p class="eyebrow">Sucre · Bolivia</p>
                <h1 class="mt-5 font-display text-6xl font-extrabold leading-[0.95] tracking-tight sm:text-8xl lg:text-9xl">
                    {{ __('Ruta del') }}<br><span class="bg-gradient-to-r from-gold via-cacao-300 to-gold bg-clip-text text-transparent">{{ __('Chocolate') }}</span>
                </h1>
                <p class="mx-auto mt-6 max-w-xl text-lg text-cream-100/80 sm:text-xl">
                    {{ __('Recorre las fábricas de chocolate de la capital dulce de Bolivia. Reserva tu tour y recoge tus chocolates en la visita.') }}
                </p>
                <div class="mt-9 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('events.index') }}" class="btn btn-primary">{{ __('Ver eventos e inscribirme') }}</a>
                    <a href="{{ route('tours.index') }}" class="btn btn-ghost">{{ __('Reservar un tour') }}</a>
                    <a href="#ruta" class="btn btn-ghost">{{ __('Descubrir la ruta') }} ↓</a>
                </div>
            </div>
        </section>

        {{-- Keyframe 1: Sucre --}}
        <section id="ruta" class="chapter px-4 sm:px-8 lg:px-16"
                 data-map-step data-lat="-19.047" data-lng="-65.26" data-zoom="13" data-route="-1">
            <div class="chapter-card reveal">
                <p class="eyebrow">{{ __('La ciudad blanca') }}</p>
                <h2 class="mt-3 font-display text-4xl font-semibold leading-tight">{{ __('Sucre, capital del chocolate') }}</h2>
                <p class="mt-4 text-cream-100/75">
                    {{ __('Entre casonas coloniales y calles empedradas, Sucre guarda una tradición chocolatera de más de un siglo. Sigue el mapa: te llevaremos fábrica por fábrica.') }}
                </p>
                <dl class="mt-8 grid grid-cols-3 gap-4 border-t border-cream-100/10 pt-6 text-center">
                    <div><dt class="text-xs text-cream-100/50">{{ __('Fábricas') }}</dt><dd class="font-display text-3xl text-gold">{{ $factories->count() }}</dd></div>
                    <div><dt class="text-xs text-cream-100/50">{{ __('Tours') }}</dt><dd class="font-display text-3xl text-gold">{{ $tours->count() }}</dd></div>
                    <div><dt class="text-xs text-cream-100/50">{{ __('Tiendas') }}</dt><dd class="font-display text-3xl text-gold">{{ $factories->sum(fn ($f) => $f->stores->count()) }}</dd></div>
                </dl>
            </div>
        </section>

        {{-- One keyframe per factory --}}
        @foreach ($factories as $i => $factory)
            <section id="fabrica-{{ $factory->slug }}" class="chapter px-4 sm:px-8 lg:px-16"
                     data-map-step data-lat="{{ $factory->lat }}" data-lng="{{ $factory->lng }}" data-zoom="17"
                     data-focus="{{ $factory->slug }}" data-route="{{ $i }}">
                <article class="chapter-card reveal">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-full font-bold text-white" style="background: {{ $factory->color }}">{{ $i + 1 }}</span>
                        <p class="eyebrow">{{ __('Parada :n', ['n' => $i + 1]) }}@if ($factory->founded_year) · {{ __('desde :year', ['year' => $factory->founded_year]) }}@endif</p>
                    </div>
                    <h2 class="mt-5 font-display text-3xl font-semibold leading-tight md:text-4xl">{{ $factory->name }}</h2>
                    @if ($factory->tr('tagline'))
                        <p class="mt-2 text-lg text-cacao-300">{{ $factory->tr('tagline') }}</p>
                    @endif
                    <p class="mt-4 line-clamp-3 text-cream-100/75 md:line-clamp-none">{{ $factory->tr('description') }}</p>

                    @if ($factory->stores->isNotEmpty())
                        <div class="mt-6 hidden space-y-2 border-t border-cream-100/10 pt-5 md:block">
                            <p class="text-xs font-semibold uppercase tracking-wider text-cream-100/50">{{ __('Recoge tus chocolates en') }}</p>
                            @foreach ($factory->stores as $store)
                                <p class="flex items-start gap-2 text-sm">
                                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-gold"></span>
                                    <span><strong class="font-semibold">{{ $store->name }}</strong><br><span class="text-cream-100/55">{{ $store->address }}@if ($store->hours) · {{ $store->hours }}@endif</span></span>
                                </p>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('shop', ['fabrica' => $factory->slug]) }}" class="btn btn-primary">{{ __('Ver sus chocolates') }}</a>
                        <a href="#tours" class="btn btn-ghost">{{ __('Tours') }}</a>
                    </div>
                </article>
            </section>
        @endforeach

        {{-- Final keyframe: the whole route --}}
        <section id="ruta-completa" class="chapter px-4 sm:px-8 lg:px-16" data-map-step="all">
            <div class="chapter-card reveal">
                <p class="eyebrow">{{ __('La ruta completa') }}</p>
                <h2 class="mt-3 font-display text-4xl font-semibold leading-tight">{{ __(':n paradas, un solo sabor', ['n' => $factories->count()]) }}</h2>
                <p class="mt-4 text-cream-100/75">{{ __('Elige un tour y nosotros nos encargamos del resto: guía, degustaciones y tus compras listas para recoger en el camino.') }}</p>
                <a href="#tours" class="btn btn-primary mt-7">{{ __('Ver tours') }}</a>
            </div>
        </section>
    </div>

    {{-- Solid sections after the map story --}}
    <section id="tours" class="relative z-10 bg-cacao-950 px-4 py-28 sm:px-6">
        <div class="mx-auto max-w-7xl">
            <div class="reveal max-w-2xl">
                <p class="eyebrow">{{ __('Tours') }}</p>
                <h2 class="mt-3 font-display text-5xl font-semibold tracking-tight">{{ __('Elige tu experiencia') }}</h2>
                <p class="mt-4 text-lg text-cream-100/65">{{ __('Reserva online en un minuto. Pagas al llegar a la primera parada.') }}</p>
            </div>
            <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($tours as $tour)
                    @include('partials.tour-card')
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative z-10 bg-cacao-900 px-4 py-28 sm:px-6">
        <div class="mx-auto max-w-7xl">
            <div class="reveal flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-2xl">
                    <p class="eyebrow">{{ __('Tienda') }}</p>
                    <h2 class="mt-3 font-display text-5xl font-semibold tracking-tight">{{ __('Compra hoy, recoge en tu visita') }}</h2>
                    <p class="mt-4 text-lg text-cream-100/65">{{ __('Aparta tus chocolates favoritos y recógelos en la fábrica o tienda que prefieras.') }}</p>
                </div>
                <a href="{{ route('shop') }}" class="btn btn-ghost">{{ __('Ver toda la tienda') }} →</a>
            </div>
            <div class="mt-14 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($products as $product)
                    @include('partials.product-card')
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative z-10 bg-cacao-950 px-4 py-28 sm:px-6">
        <div class="mx-auto max-w-5xl text-center">
            <p class="eyebrow reveal">{{ __('Cómo funciona') }}</p>
            <div class="mt-12 grid gap-10 md:grid-cols-3">
                @foreach ([
                    ['1', __('Elige'), __('Explora la ruta y escoge el tour o los chocolates que más te gusten.')],
                    ['2', __('Reserva'), __('Reserva tu cupo o aparta tu pedido. Recibirás un código de confirmación.')],
                    ['3', __('Disfruta'), __('Paga al llegar y recoge tus chocolates en la fábrica o tienda elegida.')],
                ] as [$n, $title, $text])
                    <div class="reveal">
                        <span class="font-display text-6xl font-extrabold text-gold/80">{{ $n }}</span>
                        <h3 class="mt-3 text-xl font-semibold">{{ $title }}</h3>
                        <p class="mt-2 text-cream-100/60">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
