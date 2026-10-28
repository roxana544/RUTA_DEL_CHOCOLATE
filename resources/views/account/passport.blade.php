@extends('layouts.app')
@section('title', __('Mi ruta'))

@section('content')
    @include('partials.page-header', [
        'eyebrow' => __('Pasaporte del chocolate'),
        'title' => __('Mi ruta'),
        'subtitle' => __('Hola, :name. Visita cada fábrica, toma una foto y registra tu checkpoint.', ['name' => \Illuminate\Support\Str::before(auth()->user()->name, ' ')]),
    ])
    <section class="px-4 pb-28 sm:px-6">
        <div class="mx-auto max-w-7xl space-y-10">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="card p-6"><p class="text-sm text-cream-100/60">{{ __('Checkpoints') }}</p><p class="mt-2 font-display text-4xl text-gold">{{ $done }} / {{ $total }}</p>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-cream-100/10"><div class="h-full bg-gold" style="width: {{ $total ? round($done / $total * 100) : 0 }}%"></div></div></div>
                <div class="card p-6"><p class="text-sm text-cream-100/60">{{ __('Puntos') }}</p><p class="mt-2 font-display text-4xl text-gold">{{ $points }}</p></div>
                <div class="card p-6"><p class="text-sm text-cream-100/60">{{ __('Insignias') }}</p>
                    <div class="mt-3 flex gap-3">
                        @foreach ($badges as $b)
                            <span title="{{ $b['name'] }}" class="grid h-12 w-12 place-items-center rounded-full border text-2xl {{ $b['earned'] ? 'border-gold bg-gold/15' : 'border-cream-100/15 opacity-30 grayscale' }}">{{ $b['icon'] }}</span>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-cream-100/50">{{ collect($badges)->where('earned', true)->pluck('name')->implode(' · ') ?: __('Aún sin insignias') }}</p></div>
            </div>

            <div class="h-80 overflow-hidden rounded-3xl border border-cream-100/10" data-route-map data-stops="{{ $mapData->toJson() }}"></div>

            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($factories as $i => $factory)
                    @php($cp = $checkpoints->get($factory->id))
                    <div class="card p-6" data-checkpoint data-lat="{{ $factory->lat }}" data-lng="{{ $factory->lng }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-full font-bold text-white" style="background: {{ $factory->color }}">{{ $i + 1 }}</span>
                            <div class="flex-1"><h2 class="font-semibold">{{ $factory->name }}</h2><p class="text-xs text-cream-100/50">{{ $factory->address }}</p></div>
                            @if ($cp)<span class="rounded-full bg-gold/15 px-3 py-1 text-xs font-semibold text-gold">✓ +{{ $cp->points }}</span>@endif
                        </div>
                        @if ($cp)
                            <img src="{{ $cp->photoUrl() }}" alt="{{ $factory->name }}" class="mt-4 h-44 w-full rounded-xl object-cover" loading="lazy">
                            <p class="mt-2 text-xs text-cream-100/50">{{ $cp->created_at->locale(app()->getLocale())->isoFormat('D MMM YYYY, HH:mm') }} · {{ $cp->gps_verified ? __('Ubicación verificada') : __('Sin verificar por GPS') }}</p>
                        @else
                            <form method="POST" action="{{ route('passport.store', $factory) }}" enctype="multipart/form-data" class="mt-4 space-y-3" data-checkpoint-form>
                                @csrf
                                <input type="hidden" name="lat"><input type="hidden" name="lng">
                                <input class="field" type="file" name="photo" accept="image/*" capture="environment" required>
                                <p class="text-xs text-cream-100/50" data-gps-status>{{ __('Debes estar a menos de :r m de la fábrica; te pediremos tu ubicación.', ['r' => $radius]) }}</p>
                                <button class="btn btn-primary w-full">📸 {{ __('Registrar checkpoint') }}</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($errors->has('photo'))
                <p class="rounded-xl border border-red-300/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ $errors->first('photo') }}</p>
            @endif

            <div>
                <h2 class="font-display text-3xl font-semibold">{{ __('Mis reservas') }}</h2>
                <div class="card mt-4 divide-y divide-cream-100/10 text-sm">
                    @forelse ($bookings as $b)
                        <a href="{{ route('bookings.show', $b) }}" class="flex flex-wrap items-center justify-between gap-2 p-4 hover:bg-cream-100/5"><span class="font-mono">{{ $b->code }}</span><span>{{ $b->tour->tr('name') }}</span><span class="text-cream-100/60">{{ $b->date->format('d/m/Y') }} {{ $b->time }}</span><span>{{ __('status.'.$b->status) }}</span></a>
                    @empty
                        <p class="p-4 text-cream-100/50">{{ __('Aún no tienes reservas.') }} <a class="text-gold hover:underline" href="{{ route('tours.index') }}">{{ __('Reservar un tour') }}</a></p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
