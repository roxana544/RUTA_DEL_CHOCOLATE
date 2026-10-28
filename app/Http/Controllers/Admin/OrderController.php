<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.orders.index', [
            'status' => $request->query('status'),
            'orders' => Order::with('store')->withCount('items')
                ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
                ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($w) => $w
                    ->where('code', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_email', 'like', "%{$term}%")))
                ->orderBy('pickup_date')
                ->paginate(25)->withQueryString(),
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load('items', 'store.factory', 'booking.tour')]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate(['status' => ['required', Rule::in(Order::STATUSES)]]);

        DB::transaction(function () use ($order, $data) {
            // Return stock when an order is cancelled (and take it again if reopened).
            $wasCancelled = $order->status === Order::STATUS_CANCELLED;
            $isCancelled = $data['status'] === Order::STATUS_CANCELLED;
            if ($wasCancelled !== $isCancelled) {
                foreach ($order->items as $item) {
                    $item->product()->increment('stock', $isCancelled ? $item->quantity : -$item->quantity);
                }
            }
            $order->update($data);
        });

        return back()->with('status', "Pedido {$order->code} actualizado.");
    }
}
