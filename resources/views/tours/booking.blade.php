@extends('layouts.app')
@section('title', __('Reserva :code', ['code' => $booking->code]))

@section('content')
    <section class="px-4 pb-28 pt-32 sm:px-6">
        <div class="mx-auto max-w-2xl">
            <div class="card p-8 text-center sm:p-10">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-gold/15 text-3xl">🍫</div>
                <p class="eyebrow mt-6">{{ __('Reserva registrada') }}</p>
                <h1 class="mt-3 font-display text-4xl font-semibold">{{ __('¡Nos vemos en la ruta, :name!', ['name' => \Illuminate\Support\Str::before($booking->customer_name, ' ')]) }}</h1>
                <p class="mt-4 text-cream-100/65">{{ __('Guarda este código y muéstralo al llegar. Te contactaremos para confirmar.') }}</p>
                <p class="mt-6 inline-block rounded-2xl border border-dashed border-gold/60 px-6 py-3 font-mono text-3xl font-bold tracking-widest text-gold">{{ $booking->code }}</p>

                <dl class="mt-8 grid grid-cols-2 gap-4 border-t border-cream-100/10 pt-6 text-left text-sm">
                    <div><dt class="text-cream-100/50">{{ __('Tour') }}</dt><dd class="font-semibold">{{ $booking->tour->tr('name') }}</dd></div>
                    <div><dt class="text-cream-100/50">{{ __('Estado') }}</dt><dd class="font-semibold">{{ __('status.'.$booking->status) }}</dd></div>
                    <div><dt class="text-cream-100/50">{{ __('Fecha y hora') }}</dt><dd class="font-semibold">{{ $booking->date->locale(app()->getLocale())->isoFormat('dddd D MMM YYYY') }} · {{ $booking->time }}</dd></div>
                    <div><dt class="text-cream-100/50">{{ __('Personas') }}</dt><dd class="font-semibold">{{ $booking->people }}</dd></div>
                    <div class="col-span-2"><dt class="text-cream-100/50">{{ __('Punto de encuentro') }}</dt><dd class="font-semibold">{{ $booking->tour->meetingPoint() }}</dd></div>
                    <div class="col-span-2 flex items-center justify-between border-t border-cream-100/10 pt-4"><dt class="text-cream-100/50">{{ __('Total a pagar al llegar') }}</dt><dd class="text-2xl font-semibold text-gold">Bs {{ number_format($booking->total, 2) }}</dd></div>
                </dl>
            </div>

            @guest
                <div class="card mt-6 p-6 text-center text-sm text-cream-100/70">{{ __('Crea una cuenta para registrar checkpoints en cada fábrica y ganar puntos.') }} <a class="text-gold hover:underline" href="{{ route('register') }}">{{ __('Crear cuenta') }}</a></div>
            @endguest
            <div class="card mt-6 flex flex-col items-center gap-4 p-8 text-center sm:flex-row sm:text-left">
                <div class="flex-1">
                    <h2 class="text-lg font-semibold">{{ __('¿Quieres llevarte chocolates?') }}</h2>
                    <p class="mt-1 text-sm text-cream-100/60">{{ __('Haz tu pedido con este código y lo tendremos listo durante tu visita.') }}</p>
                </div>
                <a href="{{ route('shop') }}" class="btn btn-primary">{{ __('Ir a la tienda') }}</a>
            </div>
        </div>
    </section>
@endsection
