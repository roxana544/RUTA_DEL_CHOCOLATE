@extends('layouts.app')
@section('title', $event->tour->tr('name'))

@section('content')
    @php($enrolled = $enrollment?->status === 'confirmed')
    @php($left = max(0, $event->capacity - $taken))
    <section class="px-4 pb-28 pt-28 sm:px-6">
        <div class="mx-auto grid max-w-6xl gap-10 lg:grid-cols-[1fr_24rem]">
            <div>
                <a href="{{ route('events.index') }}" class="text-sm text-cream-100/60 hover:text-cream-50">← {{ __('Todos los eventos') }}</a>
                <div class="mt-4 flex flex-wrap items-center gap-3"><h1 class="font-display text-5xl font-semibold tracking-tight">{{ $event->tour->tr('name') }}</h1>@include('events._badge')</div>
                <div class="mt-5 flex flex-wrap gap-2 text-sm">
                    <span class="rounded-full border border-cream-100/15 px-3 py-1">📅 {{ $event->date->locale(app()->getLocale())->isoFormat('dddd D MMM YYYY') }}</span>
                    <span class="rounded-full border border-cream-100/15 px-3 py-1">🕘 {{ substr($event->start_time, 0, 5) }}</span>
                    <span class="rounded-full border border-cream-100/15 px-3 py-1">🧭 {{ __('Guía') }}: {{ $event->guide?->name ?? __('Por asignar') }}</span>
                    <span class="rounded-full border border-cream-100/15 px-3 py-1">👥 {{ __('Cupo total') }}: {{ $event->capacity }} · {{ __('libres') }}: {{ $left }}</span>
                </div>
                <p class="mt-6 max-w-2xl text-lg text-cream-100/75">{{ $event->tour->tr('description') }}</p>
                <p class="mt-4 text-sm text-cream-100/70">📍 <strong>{{ __('Punto de encuentro') }}:</strong> {{ $event->tour->meetingPoint() }}</p>
                @if ($event->notes)<p class="mt-2 text-sm text-cream-100/60">{{ $event->notes }}</p>@endif

                <div class="mt-10 h-[24rem] overflow-hidden rounded-3xl border border-cream-100/10" data-route-map data-stops="{{ $event->stops->map(fn ($s) => ['slug' => $s->stop->factory->slug, 'name' => $s->stop->factory->name, 'lat' => $s->stop->factory->lat, 'lng' => $s->stop->factory->lng, 'color' => $s->stop->factory->color])->values()->toJson() }}"></div>

                <h2 class="mt-12 font-display text-3xl font-semibold">{{ __('Itinerario') }}</h2>
                <ol class="mt-6 space-y-4">
                    @foreach ($event->stops as $i => $s)
                        <li class="card flex gap-5 p-5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full font-bold text-white" style="background: {{ $s->stop->factory->color }}">{{ $i + 1 }}</span>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-baseline justify-between gap-2"><h3 class="text-lg font-semibold">{{ $s->stop->factory->name }}</h3>@if ($s->estimated_time)<span class="text-gold">🕘 {{ $s->hour() }}</span>@endif</div>
                                <p class="text-sm text-cream-100/60">📍 {{ $s->stop->factory->address }}</p>
                                <p class="mt-1 text-sm text-cacao-300">{{ $s->stop->factory->tr('tagline') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <aside>
                <div class="card sticky top-24 space-y-4 p-6">
                    <h2 class="font-semibold">{{ __('Tu reserva') }}</h2>
                    @error('event')<p class="rounded-xl border border-red-300/40 bg-red-500/10 px-3 py-2 text-sm text-red-200">{{ $message }}</p>@enderror
                    @if ($enrolled)
                        <p class="rounded-xl border border-gold/40 bg-gold/10 px-4 py-3 text-sm">✓ {{ __('Estás inscrito en este evento.') }}</p>
                        @if ($event->status === 'in_progress')
                            <a href="{{ route('events.participate', $event) }}" class="btn btn-primary w-full">🍫 {{ __('Participar en las actividades') }}</a>
                        @elseif ($event->status === 'scheduled')
                            <form method="POST" action="{{ route('events.cancel', $event) }}" onsubmit="return confirm('{{ __('¿Cancelar tu inscripción?') }}')">@csrf @method('DELETE')<button class="btn btn-ghost w-full">{{ __('Cancelar inscripción') }}</button></form>
                        @elseif ($event->status === 'finished')
                            @php($mem = auth()->user()->memories()->where('event_id', $event->id)->first())
                            @if ($mem)<a href="{{ route('memories.show', $mem) }}" class="btn btn-primary w-full">{{ __('Ver mi recuerdo') }}</a>@endif
                        @endif
                    @elseif (! $event->isOpenForEnrollment())
                        <p class="text-sm text-cream-100/60">{{ __('Este evento ya no admite inscripciones.') }}</p>
                    @elseif ($left < 1)
                        <p class="rounded-xl border border-red-300/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ __('El evento está lleno: ya no quedan cupos.') }}</p>
                    @else
                        @auth
                            <form method="POST" action="{{ route('events.enroll', $event) }}" class="space-y-3">
                                @csrf
                                <div><label class="label">{{ __('Nombre') }}</label><input class="field" value="{{ auth()->user()->name }}" disabled></div>
                                <div><label class="label" for="phone">{{ __('Teléfono') }}</label><input class="field" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>@error('phone')<p class="error">{{ $message }}</p>@enderror</div>
                                <div><label class="label" for="origin_city">{{ __('Ciudad de origen') }}</label><input class="field" id="origin_city" name="origin_city" value="{{ old('origin_city', auth()->user()->origin_city) }}"></div>
                                <div><label class="label" for="notes">{{ __('Observaciones') }}</label><textarea class="field" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea></div>
                                <button class="btn btn-primary w-full">{{ __('Inscribirme en este evento') }}</button>
                            </form>
                        @else
                            <p class="text-sm text-cream-100/70">{{ __('Inicia sesión o crea una cuenta para inscribirte.') }}</p>
                            <a href="{{ route('login') }}" class="btn btn-primary w-full">{{ __('Entrar') }}</a>
                            <a href="{{ route('register') }}" class="btn btn-ghost w-full">{{ __('Regístrate') }}</a>
                        @endauth
                    @endif
                </div>
            </aside>
        </div>
    </section>
@endsection
