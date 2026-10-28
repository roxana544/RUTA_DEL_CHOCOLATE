<?php

namespace App\Http\Controllers;

use App\Models\ChocolateFactory;

class FactoryController extends Controller
{
    public function index()
    {
        return view('factories.index', [
            'factories' => ChocolateFactory::active()->withCount('products')->orderBy('route_order')->get(),
        ]);
    }

    public function show(ChocolateFactory $factory)
    {
        abort_unless($factory->is_active, 404);
        $factory->load(['stores' => fn ($q) => $q->active(), 'products' => fn ($q) => $q->active(), 'tours' => fn ($q) => $q->active()]);

        return view('factories.show', [
            'factory' => $factory,
            'mapData' => [[
                'slug' => $factory->slug, 'name' => $factory->name, 'lat' => $factory->lat, 'lng' => $factory->lng, 'color' => $factory->color,
            ]],
        ]);
    }
}
