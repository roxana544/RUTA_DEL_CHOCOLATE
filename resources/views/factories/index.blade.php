@extends('layouts.app')
@section('title', __('Fábricas'))

@section('content')
    @include('partials.page-header', [
        'eyebrow' => __('La ruta'),
        'title' => __('Fábricas de chocolate'),
        'subtitle' => __('Toda la información en un solo lugar: ubicación, horarios de visita, productos y precios.'),
    ])
    <section class="px-4 pb-28 sm:px-6">
        <div class="mx-auto grid max-w-7xl gap-6 md:grid-cols-2">
            @foreach ($factories as $i => $factory)
                <a href="{{ route('factories.show', $factory) }}" class="reveal card flex flex-col gap-4 p-7 hover:border-gold/40">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-full font-bold text-white" style="background: {{ $factory->color }}">{{ $i + 1 }}</span>
                        <div>
                            <h2 class="font-display text-2xl font-semibold">{{ $factory->name }}</h2>
                            <p class="text-sm text-cacao-300">{{ $factory->tr('tagline') }}</p>
                        </div>
                    </div>
                    <p class="line-clamp-3 text-cream-100/70">{{ $factory->tr('description') }}</p>
                    <div class="mt-auto flex flex-wrap gap-2 text-xs text-cream-100/70">
                        <span class="rounded-full border border-cream-100/15 px-3 py-1">📍 {{ $factory->address }}</span>
                        @if ($factory->tr('visit_hours'))<span class="rounded-full border border-cream-100/15 px-3 py-1">🕘 {{ $factory->tr('visit_hours') }}</span>@endif
                        @if ($factory->has_museum)<span class="rounded-full border border-gold/40 px-3 py-1 text-gold">🏛 {{ __('Museo') }}</span>@endif
                        <span class="rounded-full border border-cream-100/15 px-3 py-1">🍫 {{ __(':n productos', ['n' => $factory->products_count]) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
