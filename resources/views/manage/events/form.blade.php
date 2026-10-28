@extends('layouts.admin')
@section('title', $event->exists ? 'Editar evento' : 'Crear evento')

@section('content')
    <form method="GET" action="{{ route('manage.events.create') }}" class="{{ $event->exists ? 'hidden' : 'mb-4' }} flex items-end gap-3">
        <div><label class="label">Tour</label><select class="field" name="tour" onchange="this.form.submit()">@foreach ($tours as $t)<option value="{{ $t->id }}" @selected($tour?->id === $t->id)>{{ $t->name_es }}</option>@endforeach</select></div>
        <noscript><button class="btn btn-ghost">Cargar paradas</button></noscript>
    </form>

    <form method="POST" action="{{ $event->exists ? route('manage.events.update', $event) : route('manage.events.store') }}" class="card max-w-4xl space-y-5 p-6 md:p-8">
        @csrf
        @if ($event->exists) @method('PUT') @endif
        <input type="hidden" name="tour_id" value="{{ $tour?->id }}">
        <p class="text-sm text-cream-100/60">Tour: <strong class="text-cream-100">{{ $tour?->name_es }}</strong>@if (! $event->exists) (cámbialo arriba antes de llenar el formulario)@endif</p>
        <div class="grid gap-4 md:grid-cols-3">
            <x-admin.input name="date" label="Fecha" type="date" :value="$event->date?->toDateString()" required />
            <x-admin.input name="start_time" label="Hora de inicio" type="time" :value="substr((string) $event->start_time, 0, 5)" required />
            <x-admin.input name="capacity" label="Cupo máximo de turistas" type="number" :value="$event->capacity" required />
            @if (auth()->user()->isAdmin())
                <div class="md:col-span-3"><label class="label" for="guide_id">Guía asignado</label>
                    <select class="field" id="guide_id" name="guide_id" required>@foreach ($guides as $g)<option value="{{ $g->id }}" @selected((int) old('guide_id', $event->guide_id) === $g->id)>{{ $g->name }} ({{ \App\Models\User::ROLES[$g->role] }})</option>@endforeach</select>
                    @error('guide_id')<p class="error">{{ $message }}</p>@enderror</div>
            @endif
            <x-admin.input name="notes" label="Notas para los turistas" type="textarea" :value="$event->notes" class="md:col-span-3" />
        </div>

        <div>
            <span class="label">Recorrido (paradas del tour)</span>
            <div class="space-y-2">
                @foreach ($tour?->stops ?? [] as $i => $stop)
                    @php($ev = $chosen->get($stop->id))
                    @php($on = old("stops.{$stop->id}.on", $event->exists ? (bool) $ev : true))
                    <div class="flex flex-wrap items-center gap-3 rounded-xl border border-cream-100/10 p-3 text-sm">
                        <label class="flex flex-1 items-center gap-2"><input type="checkbox" name="stops[{{ $stop->id }}][on]" value="1" class="accent-[#d9a441]" @checked($on)> {{ $stop->factory->name }}</label>
                        <label class="flex items-center gap-2">Orden <input class="field w-20" type="number" min="1" name="stops[{{ $stop->id }}][position]" value="{{ old("stops.{$stop->id}.position", $ev?->position ?? $i + 1) }}"></label>
                        <label class="flex items-center gap-2">Hora <input class="field w-32" type="time" name="stops[{{ $stop->id }}][time]" value="{{ old("stops.{$stop->id}.time", $ev ? $ev->hour() : '') }}"></label>
                    </div>
                @endforeach
            </div>
            @error('stops')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3 border-t border-cream-100/10 pt-5">
            <button class="btn btn-primary">{{ $event->exists ? 'Guardar cambios' : 'Crear evento' }}</button>
            <a href="{{ route('manage.events.index') }}" class="btn btn-ghost">Volver</a>
        </div>
    </form>
@endsection
