<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Una parada del tour: una fábrica dentro de un tour (tabla pivote factory_tour). */
#[Guarded(['id'])]
class TourStop extends Model
{
    protected $table = 'factory_tour';

    public $timestamps = false;

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(ChocolateFactory::class, 'factory_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'tour_stop_id');
    }
}
