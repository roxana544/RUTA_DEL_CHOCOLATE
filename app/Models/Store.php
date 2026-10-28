<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id'])]
class Store extends Model
{
    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'is_factory_outlet' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(ChocolateFactory::class, 'factory_id');
    }
}
