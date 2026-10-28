<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class Activity extends Model
{
    use Translatable;

    public const TYPE_CHECKPOINT = 'checkpoint';
    public const TYPE_PHOTO = 'photo';
    public const TYPE_QUIZ = 'quiz';

    public const TYPES = [
        self::TYPE_CHECKPOINT => 'Checkpoint (foto + GPS)',
        self::TYPE_PHOTO => 'Foto libre',
        self::TYPE_QUIZ => 'Pregunta (quiz)',
    ];

    protected function casts(): array
    {
        return ['config' => 'array', 'is_active' => 'boolean'];
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(TourStop::class, 'tour_stop_id');
    }

    /** Pregunta del quiz en el idioma actual: ['question' => , 'options' => [], 'answer' => int]. */
    public function quiz(): array
    {
        $c = $this->config ?? [];
        $en = app()->getLocale() === 'en';

        return [
            'question' => ($en ? ($c['question_en'] ?? null) : null) ?: ($c['question_es'] ?? ''),
            'options' => ($en && ! empty($c['options_en'])) ? $c['options_en'] : ($c['options_es'] ?? []),
            'answer' => (int) ($c['answer'] ?? 0),
        ];
    }
}
