<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Checkpoint;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ParticipationController extends Controller
{
    public function show(Request $request, Event $event)
    {
        $this->authorizeParticipant($request, $event);
        $event->load(['tour', 'stops.stop.factory', 'stops.stop.activities' => fn ($q) => $q->where('is_active', true)]);
        $userId = $request->user()->id;

        $participations = Participation::where('event_id', $event->id)->where('user_id', $userId)->get()->keyBy('activity_id');

        return view('events.participate', [
            'event' => $event,
            'participations' => $participations,
            'points' => $participations->sum('score'),
            'photos' => Photo::where('event_id', $event->id)->where('user_id', $userId)->latest()->get(),
            'open' => $event->status === Event::IN_PROGRESS,
            'radius' => config('checkpoints.radius_m'),
        ]);
    }

    public function complete(Request $request, Event $event, Activity $activity)
    {
        $this->authorizeParticipant($request, $event);
        abort_unless($event->status === Event::IN_PROGRESS, 403, __('El registro de actividades no está abierto.'));

        $stop = $activity->stop;
        abort_unless($activity->is_active && $event->stops()->where('tour_stop_id', $stop->id)->exists(), 404);

        $user = $request->user();
        if (Participation::where(['event_id' => $event->id, 'activity_id' => $activity->id, 'user_id' => $user->id])->exists()) {
            return back()->with('status', __('Ya completaste esta actividad.'));
        }

        $score = match ($activity->type) {
            Activity::TYPE_QUIZ => $this->quiz($request, $activity),
            Activity::TYPE_CHECKPOINT => $this->checkpoint($request, $event, $activity, $user),
            default => $this->photo($request, $event, $activity, $user),
        };

        Participation::create([
            'event_id' => $event->id, 'activity_id' => $activity->id, 'user_id' => $user->id,
            'score' => $score, 'completed_at' => now(),
        ]);

        return redirect()->route('events.participate', $event)->with('status', __('¡Actividad completada! +:n puntos', ['n' => $score]));
    }

    private function quiz(Request $request, Activity $activity): int
    {
        $data = $request->validate(['answer' => ['required', 'integer', 'min:0']]);

        return $data['answer'] === $activity->quiz()['answer'] ? $activity->max_points : (int) floor($activity->max_points / 5);
    }

    private function storePhoto(Request $request, Event $event, Activity $activity, $user): Photo
    {
        return Photo::create([
            'event_id' => $event->id, 'user_id' => $user->id,
            'tour_stop_id' => $activity->tour_stop_id, 'activity_id' => $activity->id,
            'path' => $request->file('photo')->store('photos', 'public'),
            'comment' => $request->input('comment'),
        ]);
    }

    private function photo(Request $request, Event $event, Activity $activity, $user): int
    {
        $request->validate(['photo' => ['required', 'image', 'max:8192'], 'comment' => ['nullable', 'string', 'max:200']]);
        $this->storePhoto($request, $event, $activity, $user);

        return $activity->max_points;
    }

    /** Foto + GPS: registra también el checkpoint del turista en la fábrica. */
    private function checkpoint(Request $request, Event $event, Activity $activity, $user): int
    {
        $data = $request->validate([
            'photo' => ['required', 'image', 'max:8192'],
            'comment' => ['nullable', 'string', 'max:200'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
        $factory = $activity->stop->factory;

        $distance = null;
        $verified = false;
        if (isset($data['lat'], $data['lng'])) {
            $distance = Checkpoint::distanceMeters((float) $data['lat'], (float) $data['lng'], $factory->lat, $factory->lng);
            $verified = $distance <= config('checkpoints.radius_m');
        }
        if (config('checkpoints.strict') && ! $verified) {
            throw ValidationException::withMessages(['photo' => $distance === null
                ? __('Activa la ubicación (GPS) de tu dispositivo para validar que estás en la fábrica.')
                : __('Estás a :m m de la fábrica. Acércate (máx. :r m) para registrar tu paso.', ['m' => $distance, 'r' => config('checkpoints.radius_m')])]);
        }

        $photo = $this->storePhoto($request, $event, $activity, $user);
        Checkpoint::firstOrCreate(['user_id' => $user->id, 'factory_id' => $factory->id], [
            'photo' => $photo->path, 'lat' => $data['lat'] ?? null, 'lng' => $data['lng'] ?? null,
            'distance_m' => $distance, 'gps_verified' => $verified,
            'points' => $verified ? config('checkpoints.points') : config('checkpoints.unverified_points'),
        ]);

        return $verified ? $activity->max_points : (int) ceil($activity->max_points / 2);
    }

    private function authorizeParticipant(Request $request, Event $event): void
    {
        abort_unless(
            Enrollment::where('event_id', $event->id)->where('user_id', $request->user()->id)->where('status', Enrollment::CONFIRMED)->exists(),
            403,
            __('Debes estar inscrito en el evento.'),
        );
    }
}
