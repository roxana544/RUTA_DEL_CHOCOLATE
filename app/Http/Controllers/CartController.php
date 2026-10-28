<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index()
    {
        return view('shop.cart', [
            'lines' => $this->cart->lines(),
            'total' => $this->cart->total(),
            'stores' => Store::active()->with('factory')->orderByDesc('is_factory_outlet')->get(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);
        $qty = (int) ($request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:99']])['quantity'] ?? 1);
        $this->cart->add($product, max(1, $qty));

        return back()->with('status', __(':name se añadió a tu carrito.', ['name' => $product->tr('name')]));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:99']]);
        $this->cart->set($product, $data['quantity']);

        return back();
    }

    public function remove(Product $product)
    {
        $this->cart->set($product, 0);

        return back();
    }
}
