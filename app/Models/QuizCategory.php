<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the quizzes for the category.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'category_id');
    }


    /** Get the categories of certain month
     *
     */
    public static function getCategoriesByMonth($month)
    {
        $date = $month . ' 1 '.date('Y');
        // Make beginning of month format YYYY-MM
        $month = date('Y-m', strtotime($date));
        // Get all categories that have quizzes in the given month
        return self::whereHas('quizzes', function ($query) use ($month) {
            $query->where('start', 'like', $month . '%');
        })->get();
    }
}
