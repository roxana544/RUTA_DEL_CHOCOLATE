<?php

namespace App\Http\Controllers;

use App\Mail\EnrollmentConfirmed;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('estado', 'open');

        return view('events.index', [
            'status' => $status,
            'tourId' => $request->query('tour'),
            'tours' => Tour::active()->get(),
            'events' => Event::with(['tour', 'guide'])->withCount(['activeEnrollments as taken'])
                ->where('status', '!=', Event::CANCELLED)
                ->when($status === 'open', fn ($q) => $q->where('status', Event::SCHEDULED)->whereDate('date', '>=', today()))
                ->when(in_array($status, [Event::SCHEDULED, Event::IN_PROGRESS, Event::FINISHED], true), fn ($q) => $q->where('status', $status))
                ->when($request->query('tour'), fn ($q, $t) => $q->where('tour_id', $t))
                ->orderBy('date')->orderBy('start_time')->paginate(15)->withQueryString(),
        ]);
    }

    public function show(Request $request, Event $event)
    {
        abort_if($event->status === Event::CANCELLED && ! $request->user()?->canManageEvents(), 404);
        $event->load(['tour', 'guide', 'stops.stop.factory']);

        return view('events.show', [
            'event' => $event,
            'taken' => $event->activeEnrollments()->count(),
            'enrollment' => $request->user()?->enrollments()->where('event_id', $event->id)->first(),
        ]);
    }

    public function enroll(Request $request, Event $event)
    {
        $user = $request->user();
        abort_unless($event->isOpenForEnrollment(), 404);

        $data = $request->validate([
            'phone' => ['required', 'string', 'max:40'],
            'origin_city' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($event, $user, $data) {
            // Lock the event row so two people can't take the last seat at the same time.
            Event::whereKey($event->id)->lockForUpdate()->first();

            if ($event->seatsLeft() < 1) {
                throw ValidationException::withMessages(['event' => __('El evento está lleno: ya no quedan cupos.')]);
            }

            $existing = Enrollment::where('event_id', $event->id)->where('user_id', $user->id)->first();
            if ($existing?->status === Enrollment::CONFIRMED) {
                throw ValidationException::withMessages(['event' => __('Ya estás inscrito en este evento.')]);
            }

            Enrollment::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $user->id],
                ['status' => Enrollment::CONFIRMED, 'notes' => $data['notes'] ?? null],
            );
            $user->update(array_filter(['phone' => $data['phone'], 'origin_city' => $data['origin_city'] ?? null]));
        });

        Mail::to($user)->send(new EnrollmentConfirmed($event->load(['tour', 'stops.stop.factory']), $user));

        return redirect()->route('events.show', $event)->with('status', __('¡Inscripción realizada! Te enviamos la confirmación por correo.'));
    }

    public function cancel(Request $request, Event $event)
    {
        abort_if(in_array($event->status, [Event::FINISHED, Event::CANCELLED], true), 404);
        $request->user()->enrollments()->where('event_id', $event->id)->update(['status' => Enrollment::CANCELLED]);

        return redirect()->route('events.show', $event)->with('status', __('Tu inscripción fue cancelada.'));
    }
}
