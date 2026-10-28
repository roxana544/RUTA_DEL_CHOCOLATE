@extends('layouts.app')
@section('title', __('Tours'))

@section('content')
    @include('partials.page-header', [
        'eyebrow' => __('Tours'),
        'title' => __('Elige tu experiencia'),
        'subtitle' => __('Reserva online en un minuto. Pagas al llegar a la primera parada.'),
    ])
    <section class="px-4 pb-28 sm:px-6">
        <div class="mx-auto grid max-w-7xl gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($tours as $tour)
                @include('partials.tour-card')
            @empty
                <p class="text-cream-100/60">{{ __('Pronto tendremos tours disponibles.') }}</p>
            @endforelse
        </div>
    </section>
@endsection
