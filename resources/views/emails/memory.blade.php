<div style="font-family: Georgia, serif; color:#24140c; max-width:560px">
    <h2>🍫 {{ __('Tu recuerdo está listo') }}</h2>
    <p>{{ __('Gracias por recorrer :tour. Reunimos tus fotos y logros en un recuerdo digital.', ['tour' => $event->tour->tr('name')]) }}</p>
    <p>{{ __('Puntos') }}: <strong>{{ $memory->metadata['points'] ?? 0 }}</strong> · {{ __('Fotos') }}: <strong>{{ $memory->metadata['photos'] ?? 0 }}</strong></p>
    <p><a href="{{ route('memories.show', $memory) }}">{{ __('Ver mi recuerdo') }}</a></p>
</div>
