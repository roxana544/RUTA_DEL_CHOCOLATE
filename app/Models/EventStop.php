<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class EventStop extends Model
{
    public $timestamps = false;

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(TourStop::class, 'tour_stop_id');
    }

    public function hour(): string
    {
        return substr((string) $this->estimated_time, 0, 5);
    }
}
