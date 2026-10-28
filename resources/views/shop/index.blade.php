@extends('layouts.app')
@section('title', __('Tienda'))

@section('content')
    @include('partials.page-header', [
        'eyebrow' => __('Tienda'),
        'title' => __('Compra hoy, recoge en tu visita'),
        'subtitle' => __('Aparta tus chocolates favoritos y recógelos en la fábrica o tienda que prefieras.'),
    ])
    <section class="px-4 pb-28 sm:px-6">
        <div class="mx-auto max-w-7xl">
            <div class="mb-10 flex flex-wrap gap-2">
                <a href="{{ route('shop') }}" class="rounded-full border px-4 py-1.5 text-sm {{ $current ? 'border-cream-100/20 text-cream-100/70' : 'border-gold bg-gold text-cacao-950' }}">{{ __('Todas') }}</a>
                @foreach ($factories as $factory)
                    <a href="{{ route('shop', ['fabrica' => $factory->slug]) }}" class="rounded-full border px-4 py-1.5 text-sm {{ $current === $factory->slug ? 'border-gold bg-gold text-cacao-950' : 'border-cream-100/20 text-cream-100/70 hover:text-cream-50' }}">{{ $factory->name }}</a>
                @endforeach
            </div>
            <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @forelse ($products as $product)
                    @include('partials.product-card')
                @empty
                    <p class="col-span-full text-cream-100/60">{{ __('No hay productos disponibles.') }}</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
