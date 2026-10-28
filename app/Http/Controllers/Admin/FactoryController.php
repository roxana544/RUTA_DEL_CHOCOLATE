<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChocolateFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FactoryController extends Controller
{
    public function index()
    {
        return view('admin.factories.index', [
            'factories' => ChocolateFactory::withCount(['stores', 'products'])->orderBy('route_order')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.factories.form', ['factory' => new ChocolateFactory(['color' => '#6b3e26', 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        ChocolateFactory::create($this->validated($request));

        return redirect()->route('admin.factories.index')->with('status', 'Fábrica creada.');
    }

    public function edit(ChocolateFactory $factory)
    {
        return view('admin.factories.form', ['factory' => $factory]);
    }

    public function update(Request $request, ChocolateFactory $factory)
    {
        $factory->update($this->validated($request, $factory));

        return redirect()->route('admin.factories.index')->with('status', 'Fábrica actualizada.');
    }

    public function destroy(ChocolateFactory $factory)
    {
        if ($factory->stores()->whereHas('orders')->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene pedidos asociados. Desactívala en su lugar.');
        }
        $factory->image && Storage::disk('public')->delete($factory->image);
        $factory->delete();

        return redirect()->route('admin.factories.index')->with('status', 'Fábrica eliminada.');
    }

    private function validated(Request $request, ?ChocolateFactory $factory = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'alpha_dash', 'max:80', Rule::unique('factories')->ignore($factory)],
            'tagline_es' => ['nullable', 'string', 'max:200'],
            'tagline_en' => ['nullable', 'string', 'max:200'],
            'description_es' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:200'],
            'visit_hours_es' => ['nullable', 'string', 'max:200'],
            'visit_hours_en' => ['nullable', 'string', 'max:200'],
            'phone' => ['nullable', 'string', 'max:40'],
            'website' => ['nullable', 'url', 'max:200'],
            'social_url' => ['nullable', 'url', 'max:200'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'founded_year' => ['nullable', 'integer', 'between:1800,2100'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'route_order' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['has_museum'] = $request->boolean('has_museum');

        return $this->withImage($request, $data, $factory, 'factories');
    }
}
