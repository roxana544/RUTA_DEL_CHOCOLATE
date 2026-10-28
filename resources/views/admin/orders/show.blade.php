@extends('layouts.admin')
@section('title', 'Pedido '.$order->code)

@section('content')
    <div class="grid max-w-5xl gap-6 lg:grid-cols-[1fr_18rem]">
        <div class="card p-6">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase text-cream-100/50"><tr><th class="py-2 text-left">Producto</th><th>Cant.</th><th class="text-right">Subtotal</th></tr></thead>
                @foreach ($order->items as $item)
                    <tr class="border-t border-cream-100/10"><td class="py-2">{{ $item->product_name }}</td><td class="text-center">{{ $item->quantity }}</td><td class="text-right">Bs {{ number_format($item->quantity * $item->unit_price, 2) }}</td></tr>
                @endforeach
                <tr class="border-t border-cream-100/10 font-semibold"><td class="py-3" colspan="2">Total a cobrar</td><td class="text-right text-gold">Bs {{ number_format($order->total, 2) }}</td></tr>
            </table>
            @if ($order->notes)<p class="mt-4 text-sm text-cacao-300">“{{ $order->notes }}”</p>@endif
        </div>
        <div class="card space-y-3 p-6 text-sm">
            <p>@include('admin._status', ['status' => $order->status])</p>
            @include('admin._status-form', ['action' => route('admin.orders.update', $order), 'statuses' => \App\Models\Order::STATUSES, 'current' => $order->status])
            <p><span class="text-cream-100/50">Cliente:</span><br>{{ $order->customer_name }}<br>{{ $order->customer_email }}<br>{{ $order->customer_phone }}</p>
            <p><span class="text-cream-100/50">Recoge en:</span><br>{{ $order->store->name }} ({{ $order->store->factory->name }})</p>
            <p><span class="text-cream-100/50">Fecha:</span> {{ $order->pickup_date->format('d/m/Y') }}</p>
            @if ($order->booking)<p><span class="text-cream-100/50">Tour:</span> {{ $order->booking->code }} · {{ $order->booking->tour->name_es }} ({{ $order->booking->date->format('d/m') }} {{ $order->booking->time }})</p>@endif
        </div>
    </div>
@endsection
