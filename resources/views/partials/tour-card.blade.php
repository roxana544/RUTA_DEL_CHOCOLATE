<a href="{{ route('tours.show', $tour) }}" class="reveal group card flex flex-col overflow-hidden transition hover:-translate-y-1 hover:border-gold/40">
    <div class="relative aspect-[16/10] overflow-hidden">
        @if ($tour->imageUrl())
            <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->tr('name') }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
        @else
            <div class="choco-placeholder h-full w-full transition duration-700 group-hover:scale-105" style="--c: {{ $tour->factories->first()?->color ?? '#6b3e26' }}"></div>
        @endif
        <span class="glass absolute left-4 top-4 rounded-full px-3 py-1 text-xs font-semibold">{{ $tour->durationLabel() }}</span>
    </div>
    <div class="flex flex-1 flex-col p-6">
        <h3 class="font-display text-2xl font-semibold">{{ $tour->tr('name') }}</h3>
        <p class="mt-2 line-clamp-3 text-sm text-cream-100/65">{{ $tour->tr('description') }}</p>
        <div class="mt-4 flex flex-wrap gap-1.5">
            @foreach ($tour->factories as $f)
                <span class="rounded-full border border-cream-100/15 px-2.5 py-0.5 text-xs text-cream-100/70">{{ $f->name }}</span>
            @endforeach
        </div>
        <div class="mt-auto flex items-end justify-between pt-6">
            <p><span class="text-2xl font-semibold text-gold">Bs {{ number_format($tour->price, 0) }}</span> <span class="text-xs text-cream-100/50">/ {{ __('persona') }}</span></p>
            <span class="text-sm font-semibold text-cream-50 group-hover:text-gold">{{ __('Reservar') }} →</span>
        </div>
    </div>
</a>
