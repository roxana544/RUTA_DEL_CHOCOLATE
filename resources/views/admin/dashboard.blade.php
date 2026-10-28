@extends('layouts.admin')
@section('title', 'Resumen')

@section('content')
    <div class="grid gap-4 sm:grid-cols-4">
        <a href="{{ route('admin.checkpoints.index') }}" class="card p-6 hover:border-gold/40"><p class="text-sm text-cream-100/60">Checkpoints hoy</p><p class="mt-2 font-display text-4xl text-gold">{{ $checkpointsToday }}</p></a>
        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="card p-6 hover:border-gold/40"><p class="text-sm text-cream-100/60">Reservas por confirmar</p><p class="mt-2 font-display text-4xl text-gold">{{ $pendingBookings }}</p></a>
        <a href="{{ route('admin.orders.index') }}" class="card p-6 hover:border-gold/40"><p class="text-sm text-cream-100/60">Pedidos por entregar</p><p class="mt-2 font-display text-4xl text-gold">{{ $pendingOrders }}</p></a>
        <div class="card p-6"><p class="text-sm text-cream-100/60">Ingresos cobrados</p><p class="mt-2 font-display text-4xl text-gold">Bs {{ number_format($revenue, 0) }}</p></div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <div class="card p-6">
            <h2 class="mb-4 font-semibold">Próximos tours</h2>
            <table class="w-full text-sm">
                @forelse ($upcomingBookings as $b)
                    <tr class="border-t border-cream-100/10"><td class="py-2 font-mono">{{ $b->code }}</td><td>{{ $b->date->format('d/m') }} {{ $b->time }}</td><td>{{ $b->tour->name_es }}</td><td>{{ $b->people }} pers.</td><td>@include('admin._status', ['status' => $b->status])</td></tr>
                @empty
                    <tr><td class="text-cream-100/50">Sin reservas próximas.</td></tr>
                @endforelse
            </table>
        </div>
        <div class="card p-6">
            <h2 class="mb-4 font-semibold">Pedidos recientes</h2>
            <table class="w-full text-sm">
                @forelse ($recentOrders as $o)
                    <tr class="border-t border-cream-100/10"><td class="py-2 font-mono"><a class="hover:text-gold" href="{{ route('admin.orders.show', $o) }}">{{ $o->code }}</a></td><td>{{ $o->pickup_date->format('d/m') }}</td><td>{{ $o->store->name }}</td><td>Bs {{ number_format($o->total, 2) }}</td><td>@include('admin._status', ['status' => $o->status])</td></tr>
                @empty
                    <tr><td class="text-cream-100/50">Sin pedidos todavía.</td></tr>
                @endforelse
            </table>
        </div>
    </div>

    @if ($lowStock->isNotEmpty())
        <div class="card mt-6 p-6">
            <h2 class="mb-4 font-semibold">Stock bajo</h2>
            <div class="flex flex-wrap gap-2">
                @foreach ($lowStock as $p)
                    <a href="{{ route('admin.products.edit', $p) }}" class="rounded-full border border-red-300/30 px-3 py-1 text-sm">{{ $p->name_es }}: <strong>{{ $p->stock }}</strong></a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
