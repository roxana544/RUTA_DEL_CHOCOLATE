@extends('layouts.app')
@section('title', $tour->tr('name'))

@section('content')
    <section class="px-4 pb-28 pt-28 sm:px-6">
        <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[1fr_26rem]">
            <div>
                <a href="{{ route('tours.index') }}" class="text-sm text-cream-100/60 hover:text-cream-50">← {{ __('Todos los tours') }}</a>
                <h1 class="mt-4 font-display text-5xl font-semibold tracking-tight">{{ $tour->tr('name') }}</h1>
                <div class="mt-5 flex flex-wrap gap-2 text-sm">
                    <span class="rounded-full border border-cream-100/15 px-3 py-1">⏱ {{ $tour->durationLabel() }}</span>
                    <span class="rounded-full border border-cream-100/15 px-3 py-1">👥 {{ __('máx. :n personas', ['n' => $tour->capacity]) }}</span>
                    <span class="rounded-full border border-cream-100/15 px-3 py-1">📅 {{ $tour->weekdayLabels() }}</span>
                    <span class="rounded-full border border-cream-100/15 px-3 py-1">🕘 {{ implode(' · ', $tour->departure_times) }}</span>
                </div>
                <p class="mt-6 max-w-2xl text-lg text-cream-100/75">{{ $tour->tr('description') }}</p>
                <p class="mt-4 text-sm text-cream-100/70">📍 <strong>{{ __('Punto de encuentro') }}:</strong> {{ $tour->meetingPoint() }}</p>

                <div class="mt-10 h-[26rem] overflow-hidden rounded-3xl border border-cream-100/10" data-route-map data-stops="{{ $mapData->toJson() }}"></div>

                <h2 class="mt-12 font-display text-3xl font-semibold">{{ __('Paradas del recorrido') }}</h2>
                <ol class="mt-6 space-y-4">
                    @foreach ($tour->factories as $i => $factory)
                        <li class="card flex gap-5 p-5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full font-bold text-white" style="background: {{ $factory->color }}">{{ $i + 1 }}</span>
                            <div>
                                <h3 class="text-lg font-semibold">{{ $factory->name }}</h3>
                                <p class="text-sm text-cacao-300">{{ $factory->tr('tagline') }}</p>
                                <p class="mt-2 text-sm text-cream-100/65">{{ $factory->tr('description') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <aside>
                <form method="POST" action="{{ route('bookings.store', $tour) }}" class="card sticky top-24 space-y-5 p-6"
                      data-booking-form
                      data-price="{{ $tour->price }}"
                      data-availability="{{ route('tours.availability', $tour) }}"
                      data-old-time="{{ old('time') }}"
                      data-i18n="{{ json_encode([
                          'noDepartures' => __('No hay salidas ese día. Prueba otra fecha.'),
                          'full' => __('Completo'),
                          'seats' => __(':n cupos'),
                          'loading' => __('Buscando horarios…'),
                          'error' => __('No pudimos cargar los horarios.'),
                      ]) }}">
                    @csrf
                    <div class="flex items-baseline justify-between">
                        <p><span class="text-3xl font-semibold text-gold">Bs {{ number_format($tour->price, 0) }}</span> <span class="text-sm text-cream-100/50">/ {{ __('persona') }}</span></p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label" for="date">{{ __('Fecha') }}</label>
                            <input class="field" type="date" id="date" name="date" min="{{ today()->toDateString() }}" value="{{ old('date', today()->toDateString()) }}" required>
                            @error('date')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="people">{{ __('Personas') }}</label>
                            <input class="field" type="number" id="people" name="people" min="1" max="{{ $tour->capacity }}" value="{{ old('people', 2) }}" required>
                            @error('people')<p class="error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <span class="label">{{ __('Horario') }}</span>
                        <div class="space-y-2" data-times></div>
                        @error('time')<p class="error">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-3 border-t border-cream-100/10 pt-5">
                        <div>
                            <label class="label" for="customer_name">{{ __('Nombre completo') }}</label>
                            <input class="field" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                            @error('customer_name')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="customer_email">{{ __('Correo electrónico') }}</label>
                            <input class="field" type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                            @error('customer_email')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label" for="customer_phone">{{ __('Teléfono / WhatsApp') }}</label>
                                <input class="field" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                            </div>
                            <div>
                                <label class="label" for="country">{{ __('País') }}</label>
                                <input class="field" id="country" name="country" value="{{ old('country') }}">
                            </div>
                        </div>
                        <div>
                            <label class="label" for="notes">{{ __('Comentarios (alergias, idioma del guía…)') }}</label>
                            <textarea class="field" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-cream-100/10 pt-5">
                        <span class="text-cream-100/60">{{ __('Total a pagar al llegar') }}</span>
                        <span class="text-xl font-semibold" data-total></span>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">{{ __('Confirmar reserva') }}</button>
                    <p class="text-center text-xs text-cream-100/45">{{ __('Sin pagos online: pagas en efectivo o QR al inicio del tour.') }}</p>
                </form>
            </aside>
        </div>
    </section>
@endsection
