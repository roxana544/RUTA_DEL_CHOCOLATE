@extends('layouts.admin')
@section('title', $mine ? 'Mis eventos' : 'Eventos')
@section('actions')<a href="{{ route('manage.events.create') }}" class="btn btn-primary">Crear evento</a>@endsection

@section('content')
    <form class="mb-6 flex flex-wrap gap-3">
        <select class="field max-w-[12rem]" name="estado" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            @foreach (\App\Models\Event::LABELS as $k => $l)<option value="{{ $k }}" @selected($status === $k)>{{ $l }}</option>@endforeach
        </select>
        <select class="field max-w-xs" name="tour" onchange="this.form.submit()">
            <option value="">Todos los tours</option>
            @foreach ($tours as $t)<option value="{{ $t->id }}" @selected((string) $tourId === (string) $t->id)>{{ $t->name_es }}</option>@endforeach
        </select>
        <button class="btn btn-ghost">Filtrar</button>
    </form>
    <x-admin.table :head="['Fecha', 'Tour', 'Guía', 'Inscritos', 'Estado', '']">
        @forelse ($events as $e)
            <tr>
                <td class="px-4 py-3">{{ $e->date->format('d/m/Y') }} {{ substr($e->start_time, 0, 5) }}</td>
                <td class="px-4 py-3 font-semibold">{{ $e->tour->name_es }}</td>
                <td class="px-4 py-3">{{ $e->guide?->name ?? '—' }}</td>
                <td class="px-4 py-3">{{ $e->active_enrollments_count }} / {{ $e->capacity }}</td>
                <td class="px-4 py-3">@php($event = $e)@include('events._badge')</td>
                <td class="space-x-3 px-4 py-3 text-right"><a class="text-gold hover:underline" href="{{ route('manage.events.show', $e) }}">Ver</a>@if (in_array($e->status, ['scheduled', 'in_progress']) && (auth()->user()->isAdmin() || $e->guide_id === auth()->id()))<a class="text-gold hover:underline" href="{{ route('manage.events.edit', $e) }}">Editar</a>@endif</td>
            </tr>
        @empty
            <tr><td class="px-4 py-6 text-cream-100/50" colspan="6">No hay eventos.</td></tr>
        @endforelse
    </x-admin.table>
    <div class="mt-4">{{ $events->links() }}</div>
@endsection
