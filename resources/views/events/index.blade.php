@extends('layouts.app')
@section('title', __('Eventos'))

@section('content')
    @include('partials.page-header', [
        'eyebrow' => __('Recorridos guiados'),
        'title' => __('Eventos'),
        'subtitle' => __('Elige una salida programada, revisa el itinerario e inscríbete.'),
    ])
    <section class="px-4 pb-28 sm:px-6">
        <div class="mx-auto max-w-7xl">
            <form class="mb-8 flex flex-wrap gap-3">
                <select class="field max-w-[13rem]" name="estado" onchange="this.form.submit()">
                    <option value="open" @selected($status === 'open')>{{ __('Abiertos') }}</option>
                    <option value="all" @selected($status === 'all')>{{ __('Todos') }}</option>
                    @foreach (['scheduled', 'in_progress', 'finished'] as $s)<option value="{{ $s }}" @selected($status === $s)>{{ __(\App\Models\Event::LABELS[$s]) }}</option>@endforeach
                </select>
                <select class="field max-w-xs" name="tour" onchange="this.form.submit()">
                    <option value="">{{ __('Todos los tours') }}</option>
                    @foreach ($tours as $t)<option value="{{ $t->id }}" @selected((string) $tourId === (string) $t->id)>{{ $t->tr('name') }}</option>@endforeach
                </select>
                <button class="btn btn-ghost">{{ __('Filtrar') }}</button>
            </form>
            <div class="card divide-y divide-cream-100/10">
                @forelse ($events as $event)
                    @php($left = max(0, $event->capacity - $event->taken))
                    <a href="{{ route('events.show', $event) }}" class="flex flex-wrap items-center justify-between gap-3 p-5 hover:bg-cream-100/5">
                        <div>
                            <p class="font-semibold">{{ $event->tour->tr('name') }}</p>
                            <p class="text-sm text-cream-100/60">{{ $event->date->locale(app()->getLocale())->isoFormat('ddd D MMM YYYY') }} · {{ substr($event->start_time, 0, 5) }}@if ($event->guide) · {{ __('Guía') }}: {{ $event->guide->name }}@endif</p>
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            @include('events._badge')
                            <span class="{{ $left ? 'text-gold' : 'text-red-300' }}">{{ $left ? __(':n cupos', ['n' => $left]) : __('Completo') }}</span>
                        </div>
                    </a>
                @empty
                    <p class="p-6 text-cream-100/60">{{ __('No hay eventos con esos filtros.') }}</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $events->links() }}</div>
        </div>
    </section>
@endsection
