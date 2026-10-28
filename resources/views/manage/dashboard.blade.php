@extends('layouts.admin')
@section('title', 'Resumen de eventos')
@section('actions')<a href="{{ route('manage.events.create') }}" class="btn btn-primary">Crear evento</a>@endsection

@section('content')
    <p class="mb-4 text-sm text-cream-100/60">{{ auth()->user()->name }} · {{ \App\Models\User::ROLES[auth()->user()->role] ?? auth()->user()->role }}@if (auth()->user()->isAdmin()) · alcance: todos los eventos @else · alcance: tus eventos @endif</p>
    <div class="grid gap-4 sm:grid-cols-4">
        @foreach (\App\Models\Event::LABELS as $key => $label)
            <a href="{{ route('manage.events.index', ['estado' => $key]) }}" class="card p-6 hover:border-gold/40"><p class="text-sm text-cream-100/60">{{ $label }}</p><p class="mt-2 font-display text-4xl text-gold">{{ $counts[$key] }}</p></a>
        @endforeach
    </div>
    <div class="card mt-8 overflow-x-auto p-6">
        <h2 class="mb-4 font-semibold">Próximos eventos</h2>
        <table class="w-full text-sm">
            @forelse ($next as $event)
                <tr class="border-t border-cream-100/10"><td class="py-2"><a class="hover:text-gold" href="{{ route('manage.events.show', $event) }}">{{ $event->date->format('d/m/Y') }} {{ substr($event->start_time, 0, 5) }}</a></td><td>{{ $event->tour->name_es }}</td><td>{{ $event->active_enrollments_count }} / {{ $event->capacity }} inscritos</td><td>@include('events._badge')</td></tr>
            @empty
                <tr><td class="text-cream-100/50">No hay eventos próximos.</td></tr>
            @endforelse
        </table>
    </div>
@endsection
