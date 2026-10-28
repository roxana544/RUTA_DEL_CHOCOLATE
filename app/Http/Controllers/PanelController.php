<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Event;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $enrollments = Enrollment::with(['event.tour', 'event.guide'])
            ->where('user_id', $user->id)->where('status', Enrollment::CONFIRMED)->get()
            ->sortBy(fn ($e) => $e->event->date->format('Y-m-d').$e->event->start_time)->values();

        $counts = collect(Event::STATUSES)->mapWithKeys(fn ($s) => [$s => $enrollments->where('event.status', $s)->count()]);

        return view('account.panel', [
            'enrollments' => $enrollments,
            'counts' => $counts,
            'memories' => $user->memories()->with('event.tour')->latest('generated_at')->get(),
        ]);
    }
}
