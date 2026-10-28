<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Checkpoint;
use App\Models\ChocolateFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PassportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $factories = ChocolateFactory::active()->orderBy('route_order')->get();
        $checkpoints = $user->checkpoints()->get()->keyBy('factory_id');
        $done = $checkpoints->count();
        $total = $factories->count();

        return view('account.passport', [
            'factories' => $factories,
            'checkpoints' => $checkpoints,
            'done' => $done,
            'total' => $total,
            'points' => $user->routePoints($total),
            'badges' => $this->badges($done, $total),
            'bookings' => Booking::with('tour')->where('user_id', $user->id)->latest('date')->take(10)->get(),
            'mapData' => $factories->map(fn ($f) => [
                'slug' => $f->slug, 'name' => $f->name, 'lat' => $f->lat, 'lng' => $f->lng,
                'color' => $f->color, 'done' => $checkpoints->has($f->id),
            ])->values(),
            'radius' => config('checkpoints.radius_m'),
        ]);
    }

    public function store(Request $request, ChocolateFactory $factory)
    {
        abort_unless($factory->is_active, 404);
        $user = $request->user();

        $data = $request->validate([
            'photo' => ['required', 'image', 'max:8192'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'photo.required' => __('Toma una foto en la fábrica para registrar tu paso.'),
            'photo.image' => __('El archivo debe ser una imagen.'),
            'photo.max' => __('La foto es demasiado pesada (máx. 8 MB).'),
        ]);

        if ($user->checkpoints()->where('factory_id', $factory->id)->exists()) {
            return back()->with('status', __('Ya registraste tu paso por :name.', ['name' => $factory->name]));
        }

        $distance = null;
        $verified = false;
        if (isset($data['lat'], $data['lng'])) {
            $distance = Checkpoint::distanceMeters((float) $data['lat'], (float) $data['lng'], $factory->lat, $factory->lng);
            $verified = $distance <= config('checkpoints.radius_m');
        }

        if (config('checkpoints.strict') && ! $verified) {
            throw ValidationException::withMessages([
                'photo' => $distance === null
                    ? __('Activa la ubicación (GPS) de tu dispositivo para validar que estás en la fábrica.')
                    : __('Estás a :m m de la fábrica. Acércate (máx. :r m) para registrar tu paso.', ['m' => $distance, 'r' => config('checkpoints.radius_m')]),
            ]);
        }

        $user->checkpoints()->create([
            'factory_id' => $factory->id,
            'booking_id' => Booking::where('user_id', $user->id)
                ->whereDate('date', today())->where('status', '!=', Booking::STATUS_CANCELLED)->value('id'),
            'photo' => $request->file('photo')->store('checkpoints', 'public'),
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'distance_m' => $distance,
            'gps_verified' => $verified,
            'points' => $verified ? config('checkpoints.points') : config('checkpoints.unverified_points'),
        ]);

        return redirect()->route('passport.index')->with('status', __('¡Checkpoint registrado en :name!', ['name' => $factory->name]));
    }

    /** @return array<int, array{key: string, name: string, icon: string, earned: bool}> */
    private function badges(int $done, int $total): array
    {
        return [
            ['key' => 'first', 'name' => __('Primer paso'), 'icon' => '🍫', 'earned' => $done >= 1],
            ['key' => 'half', 'name' => __('Mitad de la ruta'), 'icon' => '🗺️', 'earned' => $total > 0 && $done >= ceil($total / 2)],
            ['key' => 'full', 'name' => __('Maestro chocolatero'), 'icon' => '🏆', 'earned' => $total > 0 && $done >= $total],
        ];
    }
}
