@extends('layouts.admin')
@section('title', 'Reservas de tours')

@section('content')
    @include('admin._filters', ['statuses' => \App\Models\Booking::STATUSES])
    <x-admin.table :head="['Código', 'Tour', 'Fecha', 'Pers.', 'Cliente', 'Total', 'Estado', 'Cambiar']">
        @forelse ($bookings as $b)
            <tr>
                <td class="px-4 py-3 font-mono">{{ $b->code }}</td>
                <td class="px-4 py-3">{{ $b->tour->name_es }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $b->date->format('d/m/Y') }} {{ $b->time }}</td>
                <td class="px-4 py-3">{{ $b->people }}</td>
                <td class="px-4 py-3">{{ $b->customer_name }}<br><span class="text-xs text-cream-100/50">{{ $b->customer_email }} {{ $b->customer_phone }} {{ $b->country ? '· '.$b->country : '' }}</span>@if ($b->notes)<br><span class="text-xs text-cacao-300">“{{ $b->notes }}”</span>@endif</td>
                <td class="px-4 py-3">Bs {{ number_format($b->total, 2) }}</td>
                <td class="px-4 py-3">@include('admin._status', ['status' => $b->status])</td>
                <td class="px-4 py-3">@include('admin._status-form', ['action' => route('admin.bookings.update', $b), 'statuses' => \App\Models\Booking::STATUSES, 'current' => $b->status])</td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-6 text-cream-100/50">No hay reservas.</td></tr>
        @endforelse
    </x-admin.table>
    <div class="mt-6">{{ $bookings->links() }}</div>
@endsection
