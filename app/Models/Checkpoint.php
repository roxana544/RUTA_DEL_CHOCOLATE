<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class Checkpoint extends Model
{
    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'gps_verified' => 'boolean',
        ];
    }

    public function photoUrl(): string
    {
        return asset('storage/'.$this->photo);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(ChocolateFactory::class, 'factory_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /** Distancia en metros entre dos coordenadas (fórmula de Haversine). */
    public static function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return (int) round($r * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
