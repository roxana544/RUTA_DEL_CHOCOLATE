<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class Memory extends Model
{
    protected $table = 'memories';

    protected function casts(): array
    {
        return ['metadata' => 'array', 'generated_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $m) => $m->token ??= \Illuminate\Support\Str::random(32));
    }

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
