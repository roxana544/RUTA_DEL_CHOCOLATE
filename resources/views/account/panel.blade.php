@extends('layouts.app')
@section('title', __('Mi panel'))

@section('content')
    @include('partials.page-header', ['eyebrow' => __('Turista'), 'title' => __('Mi panel'), 'subtitle' => auth()->user()->name.' · '.__('Turista')])
    <section class="px-4 pb-28 sm:px-6">
        <div class="mx-auto max-w-5xl space-y-10">
            <div class="grid gap-4 sm:grid-cols-4">
                @foreach (\App\Models\Event::LABELS as $key => $label)
                    <div class="card p-5"><p class="text-sm text-cream-100/60">{{ __($label) }}</p><p class="mt-2 font-display text-4xl text-gold">{{ $counts[$key] }}</p></div>
                @endforeach
            </div>

            <div>
                <div class="flex items-center justify-between"><h2 class="font-display text-3xl font-semibold">{{ __('Próximos eventos') }}</h2><a href="{{ route('events.index') }}" class="btn btn-ghost">{{ __('Ver eventos disponibles') }}</a></div>
                <div class="card mt-4 divide-y divide-cream-100/10 text-sm">
                    @forelse ($enrollments as $en)
                        @php($event = $en->event)
                        <a href="{{ route('events.show', $event) }}" class="flex flex-wrap items-center justify-between gap-3 p-4 hover:bg-cream-100/5">
                            <span>{{ $event->date->format('d/m/Y') }} {{ substr($event->start_time, 0, 5) }}</span><span class="font-semibold">{{ $event->tour->tr('name') }}</span>@include('events._badge')<span class="text-cream-100/60">{{ __(':n cupos', ['n' => $event->seatsLeft()]) }}</span>
                        </a>
                    @empty
                        <p class="p-5 text-cream-100/50">{{ __('Aún no estás inscrito en ningún evento.') }}</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h2 class="font-display text-3xl font-semibold">{{ __('Mis recuerdos') }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @forelse ($memories as $m)
                        <a href="{{ route('memories.show', $m) }}" class="card p-5 hover:border-gold/40"><p class="font-semibold">🍫 {{ $m->event->tour->tr('name') }}</p><p class="text-sm text-cream-100/60">{{ $m->event->date->format('d/m/Y') }} · {{ $m->metadata['points'] ?? 0 }} pts · {{ $m->metadata['photos'] ?? 0 }} {{ __('fotos') }}</p></a>
                    @empty
                        <p class="text-cream-100/50">{{ __('Tus recuerdos aparecerán aquí cuando termine un evento.') }}</p>
                    @endforelse
                </div>
            </div>
            <p class="text-sm text-cream-100/60"><a class="text-gold hover:underline" href="{{ route('passport.index') }}">{{ __('Mi ruta') }} →</a> {{ __('checkpoints y puntos por fábrica') }}</p>
        </div>
    </section>
@endsection
