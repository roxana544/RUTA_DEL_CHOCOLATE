@extends('layouts.admin')
@section('title', 'Actividades por parada')
@section('actions')<a href="{{ route('manage.activities.create') }}" class="btn btn-primary">Nueva actividad</a>@endsection

@section('content')
    <div class="space-y-4">
        @foreach ($stops as $stop)
            <div class="card p-5">
                <p class="font-semibold">{{ $stop->tour->name_es }} <span class="text-cream-100/50">→ Parada {{ $stop->position }}: {{ $stop->factory->name }}</span></p>
                <ul class="mt-3 space-y-1 text-sm">
                    @forelse ($stop->activities as $a)
                        <li class="flex flex-wrap items-center justify-between gap-2 border-t border-cream-100/10 pt-2"><span>{{ $a->name_es }} <span class="text-xs text-cream-100/50">· {{ \App\Models\Activity::TYPES[$a->type] }} · {{ $a->max_points }} pts{{ $a->is_active ? '' : ' · inactiva' }}</span></span>
                            <span class="space-x-3"><a class="text-gold hover:underline" href="{{ route('manage.activities.edit', $a) }}">Editar</a><x-admin.delete :action="route('manage.activities.destroy', $a)" /></span></li>
                    @empty
                        <li class="text-cream-100/50">Sin actividades.</li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    </div>
@endsection
