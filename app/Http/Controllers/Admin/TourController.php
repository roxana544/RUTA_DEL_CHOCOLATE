<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChocolateFactory;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TourController extends Controller
{
    public function index()
    {
        return view('admin.tours.index', [
            'tours' => Tour::with('factories')->withCount('bookings')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.tours.form', [
            'tour' => new Tour(['is_active' => true, 'capacity' => 15, 'departure_times' => ['09:00'], 'weekdays' => [1, 2, 3, 4, 5, 6]]),
            'factories' => ChocolateFactory::orderBy('route_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        [$data, $factoryIds] = $this->validated($request);
        Tour::create($data)->factories()->sync($factoryIds);

        return redirect()->route('admin.tours.index')->with('status', 'Tour creado.');
    }

    public function edit(Tour $tour)
    {
        return view('admin.tours.form', [
            'tour' => $tour->load('factories'),
            'factories' => ChocolateFactory::orderBy('route_order')->get(),
        ]);
    }

    public function update(Request $request, Tour $tour)
    {
        [$data, $factoryIds] = $this->validated($request, $tour);
        $tour->update($data);
        $tour->factories()->sync($factoryIds);

        return redirect()->route('admin.tours.index')->with('status', 'Tour actualizado.');
    }

    public function destroy(Tour $tour)
    {
        if ($tour->bookings()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene reservas. Desactívalo en su lugar.');
        }
        $tour->image && Storage::disk('public')->delete($tour->image);
        $tour->delete();

        return redirect()->route('admin.tours.index')->with('status', 'Tour eliminado.');
    }

    private function validated(Request $request, ?Tour $tour = null): array
    {
        $data = $request->validate([
            'slug' => ['required', 'alpha_dash', 'max:80', Rule::unique('tours')->ignore($tour)],
            'name_es' => ['required', 'string', 'max:150'],
            'name_en' => ['required', 'string', 'max:150'],
            'description_es' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'price' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'meeting_point_es' => ['nullable', 'string', 'max:200'],
            'meeting_point_en' => ['nullable', 'string', 'max:200'],
            'meeting_lat' => ['nullable', 'numeric', 'between:-90,90', 'required_with:meeting_lng'],
            'meeting_lng' => ['nullable', 'numeric', 'between:-180,180', 'required_with:meeting_lat'],
            'departure_times' => ['required', 'string'],
            'weekdays' => ['required', 'array', 'min:1'],
            'weekdays.*' => ['integer', 'between:1,7'],
            'factories' => ['required', 'array', 'min:1'],
            'factories.*' => ['integer', 'exists:factories,id'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $times = collect(explode(',', $data['departure_times']))->map(fn ($t) => trim($t))->filter();
        if ($times->contains(fn ($t) => ! preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $t))) {
            throw \Illuminate\Validation\ValidationException::withMessages(['departure_times' => 'Usa horas en formato HH:MM separadas por comas.']);
        }
        $data['departure_times'] = $times->unique()->sort()->values()->all();
        $data['weekdays'] = array_map('intval', $data['weekdays']);
        $data['is_active'] = $request->boolean('is_active');

        // Keep the order in which factories were ticked as the route order.
        $factoryIds = collect($data['factories'])->values()
            ->mapWithKeys(fn ($id, $i) => [(int) $id => ['position' => $i + 1]])->all();
        unset($data['factories']);

        return [$this->withImage($request, $data, $tour, 'tours'), $factoryIds];
    }
}
