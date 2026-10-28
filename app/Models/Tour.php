<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use App\Models\Concerns\Translatable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id'])]
class Tour extends Model
{
    use HasImage, Translatable;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'departure_times' => 'array',
            'weekdays' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function factories(): BelongsToMany
    {
        return $this->belongsToMany(ChocolateFactory::class, 'factory_tour', 'tour_id', 'factory_id')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(TourStop::class)->orderBy('position');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Punto de encuentro: el definido para el tour o, si no, la primera fábrica de la ruta. */
    public function meetingPoint(): string
    {
        if ($text = $this->tr('meeting_point')) {
            return $text;
        }
        $first = $this->factories->first();

        return $first ? "{$first->name} — {$first->address}" : '';
    }

    public function runsOn(CarbonInterface $date): bool
    {
        // weekdays uses ISO numbering: 1 = Monday ... 7 = Sunday
        return in_array($date->dayOfWeekIso, array_map('intval', $this->weekdays ?? []), true);
    }

    public function seatsLeft(string $date, string $time): int
    {
        $taken = $this->bookings()
            ->whereDate('date', $date)
            ->where('time', $time)
            ->where('status', '!=', Booking::STATUS_CANCELLED)
            ->sum('people');

        return max(0, $this->capacity - (int) $taken);
    }

    public function weekdayLabels(): string
    {
        $week = now()->locale(app()->getLocale())->startOfWeek();

        return collect($this->weekdays)->sort()
            ->map(fn ($d) => $week->copy()->addDays($d - 1)->isoFormat('ddd'))
            ->implode(' · ');
    }

    public function durationLabel(): string
    {
        $h = intdiv($this->duration_minutes, 60);
        $m = $this->duration_minutes % 60;

        return trim(($h ? "{$h} h " : '').($m ? "{$m} min" : ''));
    }
}
