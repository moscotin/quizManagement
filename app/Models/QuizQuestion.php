<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory;

    const TYPE_SINGLE   = 'single_choice';
    const TYPE_MULTIPLE = 'multiple_choice';
    const TYPE_TEXT     = 'text';
    const TYPE_MATCHING = 'matching';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'quiz_id',
        'question',
        'option_1',
        'option_2',
        'option_3',
        'option_4',
        'option_5',
        'option_6',
        'correct_option',
        'correct_options',
        'correct_answer',
        'matching_pairs',
        'image',
        'question_type',
    ];

    protected $casts = [
        'correct_options' => 'array',
        'matching_pairs'  => 'array',
    ];

    /**
     * Get the quiz that owns the question.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function isMultipleChoice(): bool
    {
        return $this->question_type === self::TYPE_MULTIPLE;
    }

    public function hasImage(): bool
    {
        return !empty($this->image);
    }
}
