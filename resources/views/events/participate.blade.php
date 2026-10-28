@extends('layouts.app')
@section('title', __('Actividades'))

@section('content')
    @include('partials.page-header', ['eyebrow' => $event->tour->tr('name'), 'title' => __('Actividades del recorrido'), 'subtitle' => __('Completa las actividades de cada parada, sube tus fotos y suma puntos.')])
    <section class="px-4 pb-28 sm:px-6">
        <div class="mx-auto max-w-4xl space-y-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('events.show', $event) }}" class="text-sm text-cream-100/60 hover:text-cream-50">← {{ __('Volver al evento') }}</a>
                <span class="rounded-full border border-gold/50 px-4 py-1.5 font-semibold text-gold">{{ __('Puntos') }}: {{ $points }}</span>
            </div>
            @unless ($open)<p class="rounded-xl border border-yellow-300/40 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-100">{{ __('El registro de actividades está cerrado (el evento no está en curso).') }}</p>@endunless
            @if ($errors->has('photo') || $errors->has('answer'))<p class="rounded-xl border border-red-300/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ $errors->first() }}</p>@endif

            @foreach ($event->stops as $i => $s)
                <div class="card p-6">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-full font-bold text-white" style="background: {{ $s->stop->factory->color }}">{{ $i + 1 }}</span>
                        <div><h2 class="text-lg font-semibold">{{ $s->stop->factory->name }}</h2><p class="text-xs text-cream-100/50">{{ $s->hour() }} · {{ $s->stop->factory->address }}</p></div>
                    </div>
                    <div class="mt-5 space-y-4">
                        @forelse ($s->stop->activities as $a)
                            @php($p = $participations->get($a->id))
                            <div class="rounded-2xl border border-cream-100/10 p-4" data-checkpoint-form-wrap>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="font-semibold">{{ $a->tr('name') }}</p>
                                    <span class="text-xs text-cream-100/50">{{ \App\Models\Activity::TYPES[$a->type] }} · {{ $a->max_points }} pts</span>
                                </div>
                                <p class="mt-1 text-sm text-cream-100/65">{{ $a->tr('description') }}</p>
                                @if ($p)
                                    <p class="mt-3 text-sm text-gold">✓ {{ __('Completada') }} · +{{ $p->score }} pts</p>
                                @elseif ($open)
                                    <form method="POST" action="{{ route('events.complete', [$event, $a]) }}" enctype="multipart/form-data" class="mt-3 space-y-3" @if ($a->type === 'checkpoint') data-checkpoint-form @endif>
                                        @csrf
                                        @if ($a->type === 'quiz')
                                            @php($q = $a->quiz())
                                            <p class="font-medium">{{ $q['question'] }}</p>
                                            @foreach ($q['options'] as $n => $opt)
                                                <label class="flex items-center gap-2 text-sm"><input type="radio" name="answer" value="{{ $n }}" required class="accent-[#d9a441]"> {{ $opt }}</label>
                                            @endforeach
                                        @else
                                            @if ($a->type === 'checkpoint')<input type="hidden" name="lat"><input type="hidden" name="lng">@endif
                                            <input class="field" type="file" name="photo" accept="image/*" capture="environment" required>
                                            <input class="field" name="comment" maxlength="200" placeholder="{{ __('Comentario (opcional)') }}">
                                            @if ($a->type === 'checkpoint')<p class="text-xs text-cream-100/50" data-gps-status>{{ __('Debes estar a menos de :r m de la fábrica; te pediremos tu ubicación.', ['r' => $radius]) }}</p>@endif
                                        @endif
                                        <button class="btn btn-primary">{{ $a->type === 'quiz' ? __('Responder') : '📸 '.__('Enviar') }}</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-cream-100/50">{{ __('Esta parada no tiene actividades.') }}</p>
                        @endforelse
                    </div>
                </div>
            @endforeach

            @if ($photos->isNotEmpty())
                <div><h2 class="font-display text-3xl font-semibold">{{ __('Mis fotos') }}</h2>
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">@foreach ($photos as $ph)<img src="{{ $ph->url() }}" alt="" class="aspect-square w-full rounded-xl object-cover" loading="lazy">@endforeach</div></div>
            @endif
        </div>
    </section>
@endsection
