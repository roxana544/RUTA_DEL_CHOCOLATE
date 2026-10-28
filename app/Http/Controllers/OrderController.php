<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Order;
use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request, Cart $cart)
    {
        $data = $request->validate([
            'store_id' => ['required', Rule::exists('stores', 'id')->where('is_active', true)],
            'pickup_date' => ['required', 'date', 'after_or_equal:today', 'before:+6 months'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:160'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'booking_code' => ['nullable', 'string', 'max:12'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $items = $cart->items();
        if (! $items) {
            return redirect()->route('shop');
        }

        $booking = null;
        if (! empty($data['booking_code'])) {
            $booking = Booking::where('code', strtoupper(trim($data['booking_code'])))->first();
            if (! $booking) {
                throw ValidationException::withMessages(['booking_code' => __('No encontramos ese código de reserva.')]);
            }
        }

        $order = DB::transaction(function () use ($items, $data, $booking) {
            $products = Product::active()->whereIn('id', array_keys($items))->lockForUpdate()->get();
            $total = 0;

            foreach ($products as $product) {
                if ($product->stock < $items[$product->id]) {
                    throw ValidationException::withMessages([
                        'cart' => __('Solo quedan :stock unidades de :name.', ['stock' => $product->stock, 'name' => $product->tr('name')]),
                    ]);
                }
                $total += $product->price * $items[$product->id];
            }

            $order = Order::create([
                'code' => Order::newCode(),
                'store_id' => $data['store_id'],
                'booking_id' => $booking?->id,
                'pickup_date' => $data['pickup_date'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total' => $total,
            ]);

            foreach ($products as $product) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->tr('name'),
                    'quantity' => $items[$product->id],
                    'unit_price' => $product->price,
                ]);
                $product->decrement('stock', $items[$product->id]);
            }

            return $order;
        });

        $cart->clear();

        return redirect()->route('orders.show', $order);
    }

    public function show(Order $order)
    {
        $order->load('items', 'store.factory', 'booking.tour');

        return view('shop.order', ['order' => $order]);
    }
}
