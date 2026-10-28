<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Guarded(['id'])]
class Booking extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_COMPLETED, self::STATUS_CANCELLED];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public static function newCode(): string
    {
        do {
            $code = 'RC-'.Str::upper(Str::random(6));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
