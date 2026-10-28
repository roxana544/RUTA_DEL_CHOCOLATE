@extends('layouts.admin')
@section('title', 'Pedidos para recoger')

@section('content')
    @include('admin._filters', ['statuses' => \App\Models\Order::STATUSES])
    <x-admin.table :head="['Código', 'Recojo', 'Lugar', 'Cliente', 'Ítems', 'Total', 'Estado', 'Cambiar']">
        @forelse ($orders as $o)
            <tr>
                <td class="px-4 py-3 font-mono"><a href="{{ route('admin.orders.show', $o) }}" class="text-gold hover:underline">{{ $o->code }}</a></td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $o->pickup_date->format('d/m/Y') }}</td>
                <td class="px-4 py-3">{{ $o->store->name }}</td>
                <td class="px-4 py-3">{{ $o->customer_name }}<br><span class="text-xs text-cream-100/50">{{ $o->customer_email }}</span></td>
                <td class="px-4 py-3">{{ $o->items_count }}</td>
                <td class="px-4 py-3">Bs {{ number_format($o->total, 2) }}</td>
                <td class="px-4 py-3">@include('admin._status', ['status' => $o->status])</td>
                <td class="px-4 py-3">@include('admin._status-form', ['action' => route('admin.orders.update', $o), 'statuses' => \App\Models\Order::STATUSES, 'current' => $o->status])</td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-6 text-cream-100/50">No hay pedidos.</td></tr>
        @endforelse
    </x-admin.table>
    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
