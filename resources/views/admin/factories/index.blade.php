@extends('layouts.admin')
@section('title', 'Fábricas')
@section('actions')<a href="{{ route('admin.factories.create') }}" class="btn btn-primary">Nueva fábrica</a>@endsection

@section('content')
    <x-admin.table :head="['#', 'Nombre', 'Coordenadas', 'Tiendas', 'Productos', 'Estado', '']">
        @foreach ($factories as $f)
            <tr>
                <td class="px-4 py-3"><span class="inline-block h-3 w-3 rounded-full" style="background: {{ $f->color }}"></span> {{ $f->route_order }}</td>
                <td class="px-4 py-3 font-semibold">{{ $f->name }}</td>
                <td class="px-4 py-3 font-mono text-xs">{{ $f->lat }}, {{ $f->lng }}</td>
                <td class="px-4 py-3">{{ $f->stores_count }}</td>
                <td class="px-4 py-3">{{ $f->products_count }}</td>
                <td class="px-4 py-3">{{ $f->is_active ? 'Activa' : 'Oculta' }}</td>
                <td class="space-x-3 px-4 py-3 text-right"><a href="{{ route('admin.factories.edit', $f) }}" class="text-gold hover:underline">Editar</a><x-admin.delete :action="route('admin.factories.destroy', $f)" /></td>
            </tr>
        @endforeach
    </x-admin.table>
@endsection
