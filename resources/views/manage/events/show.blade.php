@extends('layouts.admin')
@section('title', $event->tour->name_es)
@section('actions')
    <div class="flex flex-wrap gap-2">
        @if ($event->status === 'scheduled')
            <form method="POST" action="{{ route('manage.events.start', $event) }}">@csrf<button class="btn btn-primary">▶ Iniciar evento</button></form>
        @endif
        @if ($event->status === 'in_progress')
            <form method="POST" action="{{ route('manage.events.finish', $event) }}" onsubmit="return confirm('¿Finalizar el evento? Se cerrará el registro de actividades y se generarán los recuerdos.')">@csrf<button class="btn btn-primary">✔ Finalizar evento</button></form>
        @endif
        @if (in_array($event->status, ['scheduled', 'in_progress']))
            <a href="{{ route('manage.events.edit', $event) }}" class="btn btn-ghost">Editar</a>
            <form method="POST" action="{{ route('manage.events.cancel', $event) }}" onsubmit="return confirm('¿Cancelar este evento?')">@csrf<button class="btn btn-ghost">Cancelar evento</button></form>
        @endif
    </div>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-4">
        <div class="card p-5"><p class="text-sm text-cream-100/60">Estado</p><p class="mt-2">@include('events._badge')</p></div>
        <div class="card p-5"><p class="text-sm text-cream-100/60">Fecha y hora</p><p class="mt-2 font-semibold">{{ $event->date->format('d/m/Y') }} {{ substr($event->start_time, 0, 5) }}</p></div>
        <div class="card p-5"><p class="text-sm text-cream-100/60">Inscritos</p><p class="mt-2 font-display text-3xl text-gold">{{ $event->enrollments->where('status', 'confirmed')->count() }} / {{ $event->capacity }}</p></div>
        <div class="card p-5"><p class="text-sm text-cream-100/60">Guía · fotos</p><p class="mt-2 font-semibold">{{ $event->guide?->name ?? '—' }} · {{ $photos }}</p></div>
    </div>
    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <div class="card p-6"><h2 class="mb-4 font-semibold">Recorrido</h2>
            <ol class="space-y-2 text-sm">@foreach ($event->stops as $s)<li class="flex justify-between border-t border-cream-100/10 pt-2"><span>{{ $s->position }}. {{ $s->stop->factory->name }}</span><span class="text-gold">{{ $s->hour() ?: '—' }}</span></li>@endforeach</ol></div>
        <div class="card overflow-x-auto p-6"><h2 class="mb-4 font-semibold">Inscritos</h2>
            <table class="w-full text-sm">@forelse ($event->enrollments as $en)<tr class="border-t border-cream-100/10"><td class="py-2">{{ $en->user->name }}<br><span class="text-xs text-cream-100/50">{{ $en->user->email }} · {{ $en->user->phone }}</span></td><td>{{ $en->status === 'confirmed' ? 'Confirmado' : 'Cancelado' }}</td></tr>@empty<tr><td class="text-cream-100/50">Nadie se ha inscrito.</td></tr>@endforelse</table></div>
    </div>
@endsection
