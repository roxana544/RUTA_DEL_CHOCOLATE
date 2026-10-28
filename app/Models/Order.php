<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Guarded(['id'])]
class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_READY = 'ready';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [self::STATUS_PENDING, self::STATUS_READY, self::STATUS_PICKED_UP, self::STATUS_CANCELLED];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
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
            $code = 'CH-'.Str::upper(Str::random(6));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
