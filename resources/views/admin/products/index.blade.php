@extends('layouts.admin')
@section('title', 'Productos')
@section('actions')<a href="{{ route('admin.products.create') }}" class="btn btn-primary">Nuevo producto</a>@endsection

@section('content')
    <x-admin.table :head="['Producto', 'Fábrica', 'Precio', 'Stock', 'Estado', '']">
        @foreach ($products as $p)
            <tr>
                <td class="px-4 py-3 font-semibold">{{ $p->name_es }} <span class="font-normal text-cream-100/50">{{ $p->weight }}</span></td>
                <td class="px-4 py-3">{{ $p->factory->name }}</td>
                <td class="px-4 py-3">Bs {{ number_format($p->price, 2) }}</td>
                <td class="px-4 py-3 {{ $p->stock < 10 ? 'text-red-300' : '' }}">{{ $p->stock }}</td>
                <td class="px-4 py-3">{{ $p->is_active ? 'Activo' : 'Oculto' }}</td>
                <td class="space-x-3 px-4 py-3 text-right"><a href="{{ route('admin.products.edit', $p) }}" class="text-gold hover:underline">Editar</a><x-admin.delete :action="route('admin.products.destroy', $p)" /></td>
            </tr>
        @endforeach
    </x-admin.table>
@endsection
