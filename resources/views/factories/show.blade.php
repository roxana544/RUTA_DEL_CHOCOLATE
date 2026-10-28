@extends('layouts.app')
@section('title', $factory->name)

@section('content')
    <section class="px-4 pb-28 pt-28 sm:px-6">
        <div class="mx-auto max-w-7xl">
            <a href="{{ route('factories.index') }}" class="text-sm text-cream-100/60 hover:text-cream-50">← {{ __('Todas las fábricas') }}</a>
            <div class="mt-6 grid gap-10 lg:grid-cols-[1fr_28rem]">
                <div>
                    <p class="eyebrow">@if ($factory->founded_year){{ __('desde :year', ['year' => $factory->founded_year]) }}@else{{ __('Fábrica') }}@endif</p>
                    <h1 class="mt-3 font-display text-5xl font-semibold tracking-tight">{{ $factory->name }}</h1>
                    <p class="mt-2 text-xl text-cacao-300">{{ $factory->tr('tagline') }}</p>
                    <p class="mt-6 max-w-2xl text-lg text-cream-100/75">{{ $factory->tr('description') }}</p>

                    <dl class="card mt-8 grid gap-4 p-6 text-sm sm:grid-cols-2">
                        <div><dt class="text-cream-100/50">{{ __('Dirección') }}</dt><dd class="font-semibold">{{ $factory->address }}</dd></div>
                        <div><dt class="text-cream-100/50">{{ __('Horario de visita') }}</dt><dd class="font-semibold">{{ $factory->tr('visit_hours') ?: __('Consultar') }}</dd></div>
                        @if ($factory->phone)<div><dt class="text-cream-100/50">{{ __('Teléfono') }}</dt><dd class="font-semibold">{{ $factory->phone }}</dd></div>@endif
                        @if ($factory->has_museum)<div><dt class="text-cream-100/50">{{ __('Museo') }}</dt><dd class="font-semibold">{{ __('Abierto al público') }}</dd></div>@endif
                        @if ($factory->website || $factory->social_url)
                            <div class="sm:col-span-2 flex flex-wrap gap-4">
                                @if ($factory->website)<a class="text-gold hover:underline" href="{{ $factory->website }}" target="_blank" rel="noopener">{{ __('Sitio web') }} ↗</a>@endif
                                @if ($factory->social_url)<a class="text-gold hover:underline" href="{{ $factory->social_url }}" target="_blank" rel="noopener">{{ __('Redes sociales') }} ↗</a>@endif
                            </div>
                        @endif
                    </dl>

                    <div class="mt-8 h-80 overflow-hidden rounded-3xl border border-cream-100/10" data-route-map data-stops="{{ json_encode($mapData) }}"></div>

                    @if ($factory->stores->isNotEmpty())
                        <h2 class="mt-12 font-display text-3xl font-semibold">{{ __('Puntos de recojo') }}</h2>
                        <ul class="mt-4 space-y-2 text-sm">
                            @foreach ($factory->stores as $store)
                                <li class="card p-4"><strong>{{ $store->name }}</strong><br><span class="text-cream-100/60">{{ $store->address }}@if ($store->hours) · {{ $store->hours }}@endif</span></li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <aside class="space-y-6">
                    @if ($factory->tours->isNotEmpty())
                        <div class="card p-6">
                            <h2 class="font-semibold">{{ __('Tours que la visitan') }}</h2>
                            <ul class="mt-3 space-y-2 text-sm">
                                @foreach ($factory->tours as $tour)
                                    <li><a class="text-gold hover:underline" href="{{ route('tours.show', $tour) }}">{{ $tour->tr('name') }}</a> <span class="text-cream-100/50">· Bs {{ number_format($tour->price, 0) }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card p-6">
                        <h2 class="font-semibold">{{ __('Productos y precios') }}</h2>
                        <ul class="mt-3 divide-y divide-cream-100/10 text-sm">
                            @forelse ($factory->products as $product)
                                <li class="flex items-start justify-between gap-3 py-2.5">
                                    <span>{{ $product->tr('name') }}<br><span class="text-xs text-cream-100/50">{{ collect([$product->variety, $product->weight, $product->cacao ? $product->cacao.' cacao' : null])->filter()->implode(' · ') }}</span></span>
                                    <span class="font-semibold text-gold">Bs {{ number_format($product->price, 2) }}</span>
                                </li>
                            @empty
                                <li class="py-2 text-cream-100/50">{{ __('No hay productos disponibles.') }}</li>
                            @endforelse
                        </ul>
                        <a href="{{ route('shop', ['fabrica' => $factory->slug]) }}" class="btn btn-primary mt-5 w-full">{{ __('Ver en la tienda') }}</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
