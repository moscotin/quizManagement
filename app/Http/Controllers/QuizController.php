<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function showMonth($month)
    {
        // Validate month
        $validMonths = ['february', 'march', 'april', 'may'];
        if (!in_array(strtolower($month), $validMonths)) {
            abort(404);
        }

        // Define quizzes for each month
        $quizzes = [
            'Quiz 1 - ' . ucfirst($month),
            'Quiz 2 - ' . ucfirst($month),
            'Quiz 3 - ' . ucfirst($month),
        ];

        return view('quiz.month', [
            'month' => ucfirst($month),
            'quizzes' => $quizzes
        ]);
    }
}
