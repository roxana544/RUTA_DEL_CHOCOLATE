<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id'])]
class ChocolateFactory extends Model
{
    use HasImage, Translatable;

    protected $table = 'factories';

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'is_active' => 'boolean',
            'has_museum' => 'boolean',
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

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'factory_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'factory_id');
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(Checkpoint::class, 'factory_id');
    }

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'factory_tour', 'factory_id', 'tour_id');
    }
}
