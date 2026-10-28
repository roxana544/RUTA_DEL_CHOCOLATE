<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.bookings.index', [
            'status' => $request->query('status'),
            'bookings' => Booking::with('tour')
                ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
                ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($w) => $w
                    ->where('code', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_email', 'like', "%{$term}%")))
                ->orderByDesc('date')->orderBy('time')
                ->paginate(25)->withQueryString(),
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $booking->update($request->validate(['status' => ['required', Rule::in(Booking::STATUSES)]]));

        return back()->with('status', "Reserva {$booking->code} actualizada.");
    }
}
