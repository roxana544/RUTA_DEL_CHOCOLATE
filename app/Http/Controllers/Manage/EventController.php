<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventStop;
use App\Models\Tour;
use App\Models\User;
use App\Support\Memories;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    /** Guías ven solo lo suyo; el administrador ve todo. */
    private function scope(Request $request)
    {
        $user = $request->user();

        return Event::query()->when(! $user->isAdmin(), fn ($q) => $q->where('guide_id', $user->id));
    }

    private function own(Request $request, Event $event): void
    {
        abort_unless($request->user()->isAdmin() || $event->guide_id === $request->user()->id, 403);
    }

    public function dashboard(Request $request)
    {
        $base = fn () => $this->scope($request);

        return view('manage.dashboard', [
            'counts' => collect(Event::STATUSES)->mapWithKeys(fn ($s) => [$s => $base()->where('status', $s)->count()]),
            'next' => $base()->with('tour')->withCount('activeEnrollments')
                ->whereIn('status', [Event::SCHEDULED, Event::IN_PROGRESS])->whereDate('date', '>=', today())
                ->orderBy('date')->orderBy('start_time')->take(8)->get(),
        ]);
    }

    public function index(Request $request)
    {
        $mine = $request->routeIs('manage.mine');
        $query = $mine ? $request->user()->guidedEvents() : Event::query();

        return view('manage.events.index', [
            'mine' => $mine,
            'status' => $request->query('estado'),
            'tourId' => $request->query('tour'),
            'tours' => Tour::all(),
            'events' => $query->with(['tour', 'guide'])->withCount('activeEnrollments')
                ->when($request->query('estado'), fn ($q, $s) => $q->where('status', $s))
                ->when($request->query('tour'), fn ($q, $t) => $q->where('tour_id', $t))
                ->orderByDesc('date')->orderBy('start_time')->paginate(20)->withQueryString(),
        ]);
    }

    public function create(Request $request)
    {
        $tour = Tour::with('stops.factory')->find($request->query('tour')) ?? Tour::with('stops.factory')->first();

        return view('manage.events.form', $this->formData(new Event(['capacity' => $tour?->capacity ?? 15, 'start_time' => '09:00', 'date' => today()->addDay(), 'guide_id' => $request->user()->id]), $tour));
    }

    public function store(Request $request)
    {
        [$data, $stops] = $this->validated($request);
        $event = Event::create($data);
        $this->syncStops($event, $stops);

        return redirect()->route('manage.events.show', $event)->with('status', 'Evento creado.');
    }

    public function show(Request $request, Event $event)
    {
        $this->own($request, $event);
        $event->load(['tour', 'guide', 'stops.stop.factory', 'enrollments.user']);

        return view('manage.events.show', ['event' => $event, 'photos' => $event->photos()->count()]);
    }

    public function edit(Request $request, Event $event)
    {
        $this->own($request, $event);
        $event->load('stops');

        return view('manage.events.form', $this->formData($event, $event->tour()->with('stops.factory')->first()));
    }

    public function update(Request $request, Event $event)
    {
        $this->own($request, $event);
        abort_if(in_array($event->status, [Event::FINISHED, Event::CANCELLED], true), 403, 'El evento ya no se puede modificar.');
        [$data, $stops] = $this->validated($request, $event);

        if ($data['capacity'] < $event->activeEnrollments()->count()) {
            throw ValidationException::withMessages(['capacity' => 'El cupo no puede ser menor que los inscritos actuales.']);
        }
        $event->update($data);
        $this->syncStops($event, $stops);

        return redirect()->route('manage.events.show', $event)->with('status', 'Evento actualizado.');
    }

    public function start(Request $request, Event $event)
    {
        $this->own($request, $event);
        abort_unless($event->status === Event::SCHEDULED, 403);
        $event->update(['status' => Event::IN_PROGRESS]);

        return back()->with('status', 'Evento en curso: los turistas ya pueden registrar actividades.');
    }

    public function finish(Request $request, Event $event)
    {
        $this->own($request, $event);
        abort_unless($event->status === Event::IN_PROGRESS, 403);
        $event->update(['status' => Event::FINISHED]);
        $sent = Memories::generateFor($event);

        return back()->with('status', "Evento finalizado. Se generaron {$sent} recuerdos y se enviaron por correo.");
    }

    public function cancel(Request $request, Event $event)
    {
        $this->own($request, $event);
        abort_if(in_array($event->status, [Event::FINISHED, Event::CANCELLED], true), 403);
        $event->update(['status' => Event::CANCELLED]);

        return back()->with('status', 'Evento cancelado.');
    }

    private function formData(Event $event, ?Tour $tour): array
    {
        $chosen = $event->exists ? $event->stops->keyBy('tour_stop_id') : collect();

        return [
            'event' => $event,
            'tour' => $tour,
            'tours' => Tour::orderBy('name_es')->get(),
            'guides' => User::whereIn('role', [User::ROLE_GUIDE, User::ROLE_ADMIN])->orderBy('name')->get(),
            'chosen' => $chosen,
        ];
    }

    /** @return array{0: array, 1: array<int, array{position: int, time: ?string}>} */
    private function validated(Request $request, ?Event $event = null): array
    {
        $user = $request->user();
        $data = $request->validate([
            'tour_id' => ['required', 'exists:tours,id'],
            'guide_id' => [$user->isAdmin() ? 'required' : 'nullable', 'exists:users,id'],
            'date' => ['required', 'date', $event ? 'date' : 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'stops' => ['required', 'array', 'min:1'],
            'stops.*.on' => ['nullable'],
            'stops.*.position' => ['nullable', 'integer', 'min:1', 'max:99'],
            'stops.*.time' => ['nullable', 'date_format:H:i'],
        ]);

        if (! $user->isAdmin()) {
            $data['guide_id'] = $user->id;
        }

        $validStops = Tour::findOrFail($data['tour_id'])->stops()->pluck('id')->all();
        $stops = collect($data['stops'])
            ->filter(fn ($s, $id) => ! empty($s['on']) && in_array((int) $id, $validStops, true))
            ->map(fn ($s) => ['position' => (int) ($s['position'] ?? 1), 'time' => $s['time'] ?? null])
            ->sortBy('position')->all();

        if (! $stops) {
            throw ValidationException::withMessages(['stops' => 'Marca al menos una parada del tour.']);
        }
        unset($data['stops']);

        return [$data, $stops];
    }

    private function syncStops(Event $event, array $stops): void
    {
        $event->stops()->delete();
        $i = 1;
        foreach ($stops as $stopId => $s) {
            EventStop::create(['event_id' => $event->id, 'tour_stop_id' => $stopId, 'position' => $i++, 'estimated_time' => $s['time']]);
        }
    }
}
