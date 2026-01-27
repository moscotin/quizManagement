<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'start',
        'end',
        'required_correct_answers',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start' => 'datetime',
            'end' => 'datetime',
        ];
    }

    /**
     * Get the questions for the quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }

    /**
     * Get the participants for the quiz.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(QuizParticipant::class);
    }

    /* Get the quiz start month */
    public function getStartMonthAttribute()
    {
        return $this->start->format('F');
    }

    /* Check if the quiz was taken by user */
    public function isTakenByUser($user) {
        $userId = is_object($user) ? $user->id : $user;
        return $this->participants()
            ->where('user_id', $userId)
            ->where('completed_at', '!=', null)
            ->exists();
    }

    /* Check if the quiz was started by user */
    public function isStartedByUser($user) {
        $userId = is_object($user) ? $user->id : $user;
        return $this->participants()
            ->where('user_id', $userId)
            ->where('completed_at', null)
            ->exists();
    }
}
