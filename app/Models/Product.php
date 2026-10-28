<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class Product extends Model
{
    use HasImage, Translatable;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(ChocolateFactory::class, 'factory_id');
    }
}
