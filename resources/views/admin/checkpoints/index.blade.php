@extends('layouts.admin')
@section('title', 'Checkpoints')

@section('content')
    <p class="mb-4 text-sm text-cream-100/60">Afluencia real a cada fábrica según los pasos registrados por los turistas (foto + GPS).</p>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $f)
            <div class="card p-5"><p class="text-sm text-cream-100/60">{{ $f->name }}</p><p class="mt-2 font-display text-4xl text-gold">{{ $f->checkpoints_count }}</p><p class="text-xs text-cream-100/50">{{ $f->verified_count }} verificados por GPS</p></div>
        @endforeach
    </div>

    <div class="card mt-8 overflow-x-auto p-6">
        <table class="w-full text-sm">
            <tr class="text-left text-cream-100/50"><th class="pb-2">Foto</th><th>Turista</th><th>Fábrica</th><th>Fecha</th><th>GPS</th><th>Puntos</th></tr>
            @forelse ($checkpoints as $c)
                <tr class="border-t border-cream-100/10">
                    <td class="py-2"><a href="{{ $c->photoUrl() }}" target="_blank"><img src="{{ $c->photoUrl() }}" class="h-12 w-12 rounded-lg object-cover" alt=""></a></td>
                    <td>{{ $c->user->name }}<br><span class="text-xs text-cream-100/50">{{ $c->user->email }}</span></td>
                    <td>{{ $c->factory->name }}</td>
                    <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $c->gps_verified ? '✓ '.$c->distance_m.' m' : '—' }}</td>
                    <td>{{ $c->points }}</td>
                </tr>
            @empty
                <tr><td class="py-2 text-cream-100/50">Todavía no hay checkpoints.</td></tr>
            @endforelse
        </table>
        <div class="mt-4">{{ $checkpoints->links() }}</div>
    </div>
@endsection
