<div class="reveal card flex flex-col overflow-hidden">
    <div class="relative aspect-square overflow-hidden">
        @if ($product->imageUrl())
            <img src="{{ $product->imageUrl() }}" alt="{{ $product->tr('name') }}" class="h-full w-full object-cover">
        @else
            <div class="choco-placeholder h-full w-full" style="--c: {{ $product->factory->color }}"></div>
        @endif
        @if ($product->cacao)
            <span class="glass absolute right-3 top-3 rounded-full px-2.5 py-1 text-xs font-semibold">{{ $product->cacao }} cacao</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        <p class="text-xs uppercase tracking-wider text-cacao-300">{{ $product->factory->name }}@if ($product->variety) · {{ $product->variety }}@endif</p>
        <h3 class="mt-1 text-lg font-semibold">{{ $product->tr('name') }}</h3>
        <p class="mt-1 line-clamp-2 text-sm text-cream-100/60">{{ $product->tr('description') }}</p>
        <div class="mt-auto flex items-center justify-between gap-3 pt-5">
            <div>
                <p class="text-xl font-semibold text-gold">Bs {{ number_format($product->price, 2) }}</p>
                @if ($product->weight)<p class="text-xs text-cream-100/45">{{ $product->weight }}</p>@endif
            </div>
            @if ($product->stock > 0)
                <form method="POST" action="{{ route('cart.add', $product) }}">
                    @csrf
                    <button class="btn btn-primary px-4 py-2">{{ __('Añadir') }}</button>
                </form>
            @else
                <span class="text-sm text-cream-100/50">{{ __('Agotado') }}</span>
            @endif
        </div>
    </div>
</div>
