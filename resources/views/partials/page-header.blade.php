<section class="px-4 pb-12 pt-36 sm:px-6">
    <div class="mx-auto max-w-7xl">
        <p class="eyebrow">{{ $eyebrow }}</p>
        <h1 class="mt-3 font-display text-5xl font-semibold tracking-tight sm:text-6xl">{{ $title }}</h1>
        @isset($subtitle)<p class="mt-4 max-w-2xl text-lg text-cream-100/65">{{ $subtitle }}</p>@endisset
    </div>
</section>
