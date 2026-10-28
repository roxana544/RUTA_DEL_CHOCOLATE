<div style="font-family: Georgia, serif; color:#24140c; max-width:560px">
    <h2>{{ __('¡Nos vemos en la ruta, :name!', ['name' => \Illuminate\Support\Str::before($user->name, ' ')]) }}</h2>
    <p>{{ __('Tu inscripción a :tour fue confirmada.', ['tour' => $event->tour->tr('name')]) }}</p>
    <p><strong>{{ $event->date->locale(app()->getLocale())->isoFormat('dddd D MMM YYYY') }}</strong> · {{ substr($event->start_time, 0, 5) }}</p>
    <p>📍 {{ $event->tour->meetingPoint() }}</p>
    <ol>
        @foreach ($event->stops as $s)<li>{{ substr((string) $s->estimated_time, 0, 5) }} — {{ $s->stop->factory->name }}</li>@endforeach
    </ol>
    <p><a href="{{ route('events.show', $event) }}">{{ __('Ver el evento') }}</a></p>
</div>
