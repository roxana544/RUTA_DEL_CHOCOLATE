<?php

namespace App\Support;

use App\Mail\MemoryReady;
use App\Models\Event;
use App\Models\Memory;
use App\Models\Participation;
use App\Models\Photo;
use Illuminate\Support\Facades\Mail;

class Memories
{
    /** Genera (o actualiza) el recuerdo de cada inscrito confirmado y le envía el aviso por correo. */
    public static function generateFor(Event $event): int
    {
        $event->load('tour');
        $total = $event->stops()->count();
        $count = 0;

        foreach ($event->activeEnrollments()->with('user')->get() as $enrollment) {
            $user = $enrollment->user;
            $participations = Participation::with('activity')->where('event_id', $event->id)->where('user_id', $user->id)->get();
            $photos = Photo::where('event_id', $event->id)->where('user_id', $user->id)->count();
            $points = (int) $participations->sum('score');
            $done = $participations->count();

            $memory = Memory::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $user->id],
                [
                    'generated_at' => now(),
                    'metadata' => [
                        'points' => $points,
                        'activities' => $done,
                        'photos' => $photos,
                        'stops' => $total,
                        'achievements' => self::achievements($points, $done, $photos),
                    ],
                ],
            );
            Mail::to($user)->send(new MemoryReady($memory, $event));
            $count++;
        }

        return $count;
    }

    /** @return list<string> */
    private static function achievements(int $points, int $activities, int $photos): array
    {
        return array_values(array_filter([
            $photos >= 1 ? 'photographer' : null,
            $activities >= 3 ? 'explorer' : null,
            $points >= 50 ? 'master' : null,
        ]));
    }
}
