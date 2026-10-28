@extends('layouts.app')
@section('title', __('Mi recuerdo'))

@section('content')
    @php($m = $memory->metadata ?? [])
    @php($names = ['photographer' => ['📸', __('Fotógrafo')], 'explorer' => ['🧭', __('Explorador')], 'master' => ['🏆', __('Maestro chocolatero')]])
    <section class="px-4 pb-28 pt-32 sm:px-6">
        <div class="mx-auto max-w-4xl">
            <div class="card p-8 text-center sm:p-12">
                <p class="eyebrow">{{ __('Recuerdo digital') }}</p>
                <h1 class="mt-3 font-display text-5xl font-semibold">{{ $memory->event->tour->tr('name') }}</h1>
                <p class="mt-3 text-cream-100/70">{{ $memory->user->name }} · {{ $memory->event->date->locale(app()->getLocale())->isoFormat('D MMMM YYYY') }}</p>
                <dl class="mt-8 grid grid-cols-3 gap-4 border-t border-cream-100/10 pt-6">
                    <div><dt class="text-xs text-cream-100/50">{{ __('Puntos') }}</dt><dd class="font-display text-4xl text-gold">{{ $m['points'] ?? 0 }}</dd></div>
                    <div><dt class="text-xs text-cream-100/50">{{ __('Actividades') }}</dt><dd class="font-display text-4xl text-gold">{{ $m['activities'] ?? 0 }}</dd></div>
                    <div><dt class="text-xs text-cream-100/50">{{ __('Fotos') }}</dt><dd class="font-display text-4xl text-gold">{{ $m['photos'] ?? 0 }}</dd></div>
                </dl>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    @forelse ($m['achievements'] ?? [] as $a)<span class="rounded-full border border-gold bg-gold/15 px-4 py-2">{{ $names[$a][0] ?? '🍫' }} {{ $names[$a][1] ?? $a }}</span>@empty<span class="text-sm text-cream-100/50">{{ __('Aún sin logros') }}</span>@endforelse
                </div>
            </div>
            @if ($photos->isNotEmpty())
                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    @foreach ($photos as $ph)
                        <figure class="card overflow-hidden"><img src="{{ $ph->url() }}" alt="" class="aspect-square w-full object-cover" loading="lazy"><figcaption class="p-3 text-xs text-cream-100/70">{{ $ph->stop?->factory?->name }}@if ($ph->comment) — {{ $ph->comment }}@endif</figcaption></figure>
                    @endforeach
                </div>
            @endif
            <p class="mt-8 text-center"><button onclick="window.print()" class="btn btn-ghost">🖨 {{ __('Guardar / imprimir') }}</button></p>
        </div>
    </section>
@endsection
