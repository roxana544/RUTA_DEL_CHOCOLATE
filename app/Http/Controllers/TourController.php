<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index()
    {
        return view('tours.index', [
            'tours' => Tour::active()->with('factories')->get(),
        ]);
    }

    public function show(Tour $tour)
    {
        abort_unless($tour->is_active, 404);
        $tour->load('factories');

        return view('tours.show', [
            'tour' => $tour,
            'mapData' => $tour->factories->map(fn ($f) => [
                'slug' => $f->slug, 'name' => $f->name, 'lat' => $f->lat, 'lng' => $f->lng, 'color' => $f->color,
            ]),
        ]);
    }

    public function availability(Request $request, Tour $tour)
    {
        $data = $request->validate(['date' => ['required', 'date', 'after_or_equal:today']]);
        $date = Carbon::parse($data['date']);

        if (! $tour->is_active || ! $tour->runsOn($date)) {
            return response()->json(['times' => []]);
        }

        return response()->json([
            'times' => collect($tour->departure_times)->map(fn ($time) => [
                'time' => $time,
                'seats' => $tour->seatsLeft($date->toDateString(), $time),
            ])->values(),
        ]);
    }
}
