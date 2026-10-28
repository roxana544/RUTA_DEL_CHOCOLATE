@extends('layouts.app')
@section('title', __('Pedido :code', ['code' => $order->code]))

@section('content')
    <section class="px-4 pb-28 pt-32 sm:px-6">
        <div class="mx-auto max-w-2xl">
            <div class="card p-8 sm:p-10">
                <div class="text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-gold/15 text-3xl">🛍️</div>
                    <p class="eyebrow mt-6">{{ __('Pedido registrado') }}</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold">{{ __('¡Tus chocolates te esperan!') }}</h1>
                    <p class="mt-4 text-cream-100/65">{{ __('Presenta este código al recoger tu pedido.') }}</p>
                    <p class="mt-6 inline-block rounded-2xl border border-dashed border-gold/60 px-6 py-3 font-mono text-3xl font-bold tracking-widest text-gold">{{ $order->code }}</p>
                </div>

                <dl class="mt-8 grid grid-cols-2 gap-4 border-t border-cream-100/10 pt-6 text-sm">
                    <div><dt class="text-cream-100/50">{{ __('Recoger en') }}</dt><dd class="font-semibold">{{ $order->store->name }}</dd><dd class="text-cream-100/55">{{ $order->store->address }}</dd></div>
                    <div><dt class="text-cream-100/50">{{ __('Fecha de recojo') }}</dt><dd class="font-semibold">{{ $order->pickup_date->locale(app()->getLocale())->isoFormat('dddd D MMM YYYY') }}</dd>@if ($order->store->hours)<dd class="text-cream-100/55">{{ $order->store->hours }}</dd>@endif</div>
                    <div><dt class="text-cream-100/50">{{ __('Estado') }}</dt><dd class="font-semibold">{{ __('status.'.$order->status) }}</dd></div>
                    @if ($order->booking)
                        <div><dt class="text-cream-100/50">{{ __('Tour vinculado') }}</dt><dd class="font-semibold">{{ $order->booking->code }} · {{ $order->booking->tour->tr('name') }}</dd></div>
                    @endif
                </dl>

                <ul class="mt-6 divide-y divide-cream-100/10 border-t border-cream-100/10 text-sm">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between py-3"><span>{{ $item->quantity }} × {{ $item->product_name }}</span><span>Bs {{ number_format($item->quantity * $item->unit_price, 2) }}</span></li>
                    @endforeach
                    <li class="flex items-center justify-between pt-4"><span class="text-cream-100/60">{{ __('Total a pagar al recoger') }}</span><span class="text-2xl font-semibold text-gold">Bs {{ number_format($order->total, 2) }}</span></li>
                </ul>
            </div>
        </div>
    </section>
@endsection
