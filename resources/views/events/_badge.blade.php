@php($c = ['scheduled' => 'bg-sky-500/15 text-sky-200', 'in_progress' => 'bg-yellow-500/15 text-yellow-200', 'finished' => 'bg-emerald-500/15 text-emerald-200', 'cancelled' => 'bg-red-500/15 text-red-200'])
<span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $c[$event->status] ?? '' }}">{{ $event->label() }}</span>
