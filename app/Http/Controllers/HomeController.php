<?php

namespace App\Http\Controllers;

use App\Models\ChocolateFactory;
use App\Models\Product;
use App\Models\Tour;

class HomeController extends Controller
{
    public function __invoke()
    {
        $factories = ChocolateFactory::active()
            ->with(['stores' => fn ($q) => $q->active()])
            ->orderBy('route_order')
            ->get();

        return view('home', [
            'factories' => $factories,
            'mapData' => $factories->map(fn (ChocolateFactory $f) => [
                'slug' => $f->slug,
                'name' => $f->name,
                'lat' => $f->lat,
                'lng' => $f->lng,
                'color' => $f->color,
                'stores' => $f->stores->where('is_factory_outlet', false)->map(fn ($s) => [
                    'name' => $s->name, 'lat' => $s->lat, 'lng' => $s->lng,
                ])->values(),
            ]),
            'tours' => Tour::active()->with('factories')->get(),
            'products' => Product::active()->with('factory')->inRandomOrder()->take(4)->get(),
        ]);
    }
}
