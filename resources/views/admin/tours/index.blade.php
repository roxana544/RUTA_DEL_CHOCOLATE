@extends('layouts.admin')
@section('title', 'Tours')
@section('actions')<a href="{{ route('admin.tours.create') }}" class="btn btn-primary">Nuevo tour</a>@endsection

@section('content')
    <x-admin.table :head="['Nombre', 'Paradas', 'Duración', 'Precio', 'Horarios', 'Reservas', 'Estado', '']">
        @foreach ($tours as $t)
            <tr>
                <td class="px-4 py-3 font-semibold">{{ $t->name_es }}</td>
                <td class="px-4 py-3">{{ $t->factories->pluck('name')->implode(' → ') }}</td>
                <td class="px-4 py-3">{{ $t->durationLabel() }}</td>
                <td class="px-4 py-3">Bs {{ number_format($t->price, 2) }}</td>
                <td class="px-4 py-3">{{ implode(', ', $t->departure_times) }}</td>
                <td class="px-4 py-3">{{ $t->bookings_count }}</td>
                <td class="px-4 py-3">{{ $t->is_active ? 'Activo' : 'Oculto' }}</td>
                <td class="space-x-3 px-4 py-3 text-right"><a href="{{ route('admin.tours.edit', $t) }}" class="text-gold hover:underline">Editar</a><x-admin.delete :action="route('admin.tours.destroy', $t)" /></td>
            </tr>
        @endforeach
    </x-admin.table>
@endsection
