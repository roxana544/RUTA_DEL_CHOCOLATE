<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Checkpoint;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'pendingBookings' => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'upcomingBookings' => Booking::with('tour')
                ->whereDate('date', '>=', today())
                ->where('status', '!=', Booking::STATUS_CANCELLED)
                ->orderBy('date')->orderBy('time')->take(8)->get(),
            'checkpointsToday' => Checkpoint::whereDate('created_at', today())->count(),
            'pendingOrders' => Order::whereIn('status', [Order::STATUS_PENDING, Order::STATUS_READY])->count(),
            'recentOrders' => Order::with('store')->latest()->take(8)->get(),
            'lowStock' => Product::where('stock', '<', 10)->orderBy('stock')->get(),
            'revenue' => Booking::where('status', Booking::STATUS_COMPLETED)->sum('total')
                + Order::where('status', Order::STATUS_PICKED_UP)->sum('total'),
        ]);
    }
}
