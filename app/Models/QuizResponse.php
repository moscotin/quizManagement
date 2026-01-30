<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizResponse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'participant_id',
        'question_id',

        // single choice
        'selected_option',

        // multiple choice
        'selected_options',

        // text / fill in the blank
        'answer_text',

        // matching
        'matching_response',

        'is_correct',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'selected_options'  => 'array',
            'matching_response' => 'array',
            'is_correct'        => 'boolean',
        ];
    }

    /**
     * Get the participant who gave this response.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(QuizParticipant::class, 'participant_id');
    }

    /**
     * Get the question that was answered.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
