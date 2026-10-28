<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class Photo extends Model
{
    public function url(): string
    {
        return asset('storage/'.$this->path);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(TourStop::class, 'tour_stop_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
