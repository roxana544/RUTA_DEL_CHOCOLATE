<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id'])]
class Event extends Model
{
    public const SCHEDULED = 'scheduled';
    public const IN_PROGRESS = 'in_progress';
    public const FINISHED = 'finished';
    public const CANCELLED = 'cancelled';

    public const STATUSES = [self::SCHEDULED, self::IN_PROGRESS, self::FINISHED, self::CANCELLED];

    public const LABELS = [
        self::SCHEDULED => 'Programado',
        self::IN_PROGRESS => 'En curso',
        self::FINISHED => 'Finalizado',
        self::CANCELLED => 'Cancelado',
    ];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function label(): string
    {
        return __(self::LABELS[$this->status] ?? $this->status);
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where('status', self::SCHEDULED)->whereDate('date', '>=', today());
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guide_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(EventStop::class)->orderBy('position');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', Enrollment::CONFIRMED);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class);
    }

    public function memories(): HasMany
    {
        return $this->hasMany(Memory::class);
    }

    public function seatsLeft(): int
    {
        return max(0, $this->capacity - $this->activeEnrollments()->count());
    }

    public function isOpenForEnrollment(): bool
    {
        return $this->status === self::SCHEDULED && $this->date->gte(today());
    }

    public function startsAt(): \Carbon\Carbon
    {
        return $this->date->copy()->setTimeFromTimeString($this->start_time);
    }
}
