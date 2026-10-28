@extends('layouts.app')
@section('title', __('Carrito'))

@section('content')
    @include('partials.page-header', ['eyebrow' => __('Carrito'), 'title' => __('Tu pedido')])

    <section class="px-4 pb-28 sm:px-6">
        <div class="mx-auto max-w-7xl">
            @if ($lines->isEmpty())
                <div class="card p-10 text-center">
                    <p class="text-lg text-cream-100/70">{{ __('Tu carrito está vacío.') }}</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary mt-6">{{ __('Ir a la tienda') }}</a>
                </div>
            @else
                <div class="grid gap-10 lg:grid-cols-[1fr_28rem]">
                    <div>
                        @error('cart')<p class="error mb-4 rounded-xl border border-red-300/30 p-3">{{ $message }}</p>@enderror
                        <ul class="card divide-y divide-cream-100/10">
                            @foreach ($lines as $line)
                                <li class="flex items-center gap-4 p-5">
                                    <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl">
                                        @if ($line->product->imageUrl())
                                            <img src="{{ $line->product->imageUrl() }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <div class="choco-placeholder h-full w-full" style="--c: {{ $line->product->factory->color }}"></div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold">{{ $line->product->tr('name') }}</p>
                                        <p class="text-sm text-cream-100/55">{{ $line->product->factory->name }} · Bs {{ number_format($line->product->price, 2) }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('cart.update', $line->product) }}" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <input type="number" name="quantity" min="0" max="{{ $line->product->stock }}" value="{{ $line->quantity }}" class="field w-20 py-2 text-center" onchange="this.form.submit()" aria-label="{{ __('Cantidad') }}">
                                    </form>
                                    <p class="w-24 text-right font-semibold">Bs {{ number_format($line->subtotal, 2) }}</p>
                                    <form method="POST" action="{{ route('cart.remove', $line->product) }}">
                                        @csrf @method('DELETE')
                                        <button class="grid h-8 w-8 place-items-center rounded-full text-cream-100/50 hover:bg-cream-100/10 hover:text-cream-50" aria-label="{{ __('Quitar') }}">✕</button>
                                    </form>
                                </li>
                            @endforeach
                            <li class="flex items-center justify-between p-5">
                                <span class="text-cream-100/60">{{ __('Total a pagar al recoger') }}</span>
                                <span class="text-2xl font-semibold text-gold">Bs {{ number_format($total, 2) }}</span>
                            </li>
                        </ul>

                        <div class="mt-6 h-80 overflow-hidden rounded-3xl border border-cream-100/10"
                             data-pickup-map data-select="store_id"
                             data-stores="{{ $stores->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'lat' => $s->lat, 'lng' => $s->lng])->toJson() }}"></div>
                        <p class="mt-2 text-xs text-cream-100/45">{{ __('Toca un punto del mapa para elegir dónde recoger.') }}</p>
                    </div>

                    <form method="POST" action="{{ route('orders.store') }}" class="card h-fit space-y-4 p-6">
                        @csrf
                        <h2 class="font-display text-2xl font-semibold">{{ __('Recojo') }}</h2>
                        <div>
                            <label class="label" for="store_id">{{ __('¿Dónde lo recoges?') }}</label>
                            <select class="field" id="store_id" name="store_id" required>
                                <option value="">{{ __('Elige una fábrica o tienda') }}</option>
                                @foreach ($stores->groupBy(fn ($s) => $s->factory->name) as $factoryName => $group)
                                    <optgroup label="{{ $factoryName }}">
                                        @foreach ($group as $store)
                                            <option value="{{ $store->id }}" @selected(old('store_id') == $store->id)>{{ $store->name }}@if ($store->hours) ({{ $store->hours }})@endif</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('store_id')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="pickup_date">{{ __('Fecha de recojo') }}</label>
                            <input class="field" type="date" id="pickup_date" name="pickup_date" min="{{ today()->toDateString() }}" value="{{ old('pickup_date', today()->toDateString()) }}" required>
                            @error('pickup_date')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="booking_code">{{ __('Código de reserva de tour (opcional)') }}</label>
                            <input class="field font-mono uppercase" id="booking_code" name="booking_code" placeholder="RC-XXXXXX" value="{{ old('booking_code', session('booking_code')) }}">
                            @error('booking_code')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-3 border-t border-cream-100/10 pt-4">
                            <div>
                                <label class="label" for="customer_name">{{ __('Nombre completo') }}</label>
                                <input class="field" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                @error('customer_name')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="label" for="customer_email">{{ __('Correo electrónico') }}</label>
                                <input class="field" type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                                @error('customer_email')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="label" for="customer_phone">{{ __('Teléfono / WhatsApp') }}</label>
                                <input class="field" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                            </div>
                            <div>
                                <label class="label" for="notes">{{ __('Comentarios') }}</label>
                                <textarea class="field" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <button class="btn btn-primary w-full">{{ __('Confirmar pedido') }} · Bs {{ number_format($total, 2) }}</button>
                        <p class="text-center text-xs text-cream-100/45">{{ __('Pagas al recoger, en efectivo o QR.') }}</p>
                    </form>
                </div>
            @endif
        </div>
    </section>
@endsection
