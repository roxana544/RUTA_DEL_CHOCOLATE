@extends('layouts.admin')
@section('title', 'Puntos de recojo')
@section('actions')<a href="{{ route('admin.stores.create') }}" class="btn btn-primary">Nuevo punto</a>@endsection

@section('content')
    <x-admin.table :head="['Nombre', 'Fábrica', 'Dirección', 'Horario', 'Tipo', 'Estado', '']">
        @foreach ($stores as $s)
            <tr>
                <td class="px-4 py-3 font-semibold">{{ $s->name }}</td>
                <td class="px-4 py-3">{{ $s->factory->name }}</td>
                <td class="px-4 py-3">{{ $s->address }}</td>
                <td class="px-4 py-3">{{ $s->hours }}</td>
                <td class="px-4 py-3">{{ $s->is_factory_outlet ? 'Fábrica' : 'Tienda' }}</td>
                <td class="px-4 py-3">{{ $s->is_active ? 'Activo' : 'Oculto' }}</td>
                <td class="space-x-3 px-4 py-3 text-right"><a href="{{ route('admin.stores.edit', $s) }}" class="text-gold hover:underline">Editar</a><x-admin.delete :action="route('admin.stores.destroy', $s)" /></td>
            </tr>
        @endforeach
    </x-admin.table>
@endsection
