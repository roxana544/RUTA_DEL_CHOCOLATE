<?php

namespace App\Http\Controllers;

use App\Models\ChocolateFactory;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __invoke(Request $request)
    {
        $factory = $request->query('fabrica');

        return view('shop.index', [
            'factories' => ChocolateFactory::active()->orderBy('route_order')->get(),
            'current' => $factory,
            'products' => Product::active()->with('factory')
                ->when($factory, fn ($q) => $q->whereHas('factory', fn ($f) => $f->where('slug', $factory)))
                ->orderBy('factory_id')->get(),
        ]);
    }
}
