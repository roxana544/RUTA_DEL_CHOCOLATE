<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request, Tour $tour)
    {
        abort_unless($tour->is_active, 404);

        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today', 'before:+1 year'],
            'time' => ['required', Rule::in($tour->departure_times)],
            'people' => ['required', 'integer', 'min:1', 'max:'.$tour->capacity],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:160'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $date = Carbon::parse($data['date']);
        if (! $tour->runsOn($date)) {
            throw ValidationException::withMessages(['date' => __('Este tour no sale el día seleccionado.')]);
        }

        $booking = DB::transaction(function () use ($tour, $data, $date) {
            // Lock the tour row so concurrent bookings can't oversell the same slot.
            Tour::whereKey($tour->id)->lockForUpdate()->first();

            if ($tour->seatsLeft($date->toDateString(), $data['time']) < $data['people']) {
                throw ValidationException::withMessages(['people' => __('No quedan suficientes cupos para esa salida.')]);
            }

            return $tour->bookings()->create([
                ...$data,
                'date' => $date->toDateString(),
                'code' => Booking::newCode(),
                'user_id' => $request->user()?->id,
                'total' => $tour->price * $data['people'],
                'language' => app()->getLocale(),
            ]);
        });

        // Pre-fill the booking code at checkout so the order can be picked up during the tour.
        session(['booking_code' => $booking->code]);

        return redirect()->route('bookings.show', $booking);
    }

    public function show(Booking $booking)
    {
        $booking->load('tour.factories.stores');

        return view('tours.booking', ['booking' => $booking]);
    }
}
