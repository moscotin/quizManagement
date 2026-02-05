<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizParticipant;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function statistics()
    {
        // Only allow admin users
        $user = auth()->user();
        abort_unless($user && ($user->is_admin ?? false), 403, 'Доступ запрещен.');

        // Get overall statistics
        $totalUsers = User::count();
        $totalQuizzes = Quiz::count();
        $totalParticipants = QuizParticipant::whereNotNull('completed_at')->count();
        $totalAttempts = QuizParticipant::count();

        // Get quiz-specific statistics
        $quizStats = Quiz::withCount([
            'participants as total_attempts',
            'participants as completed_attempts' => function ($query) {
                $query->whereNotNull('completed_at');
            },
            'participants as passed_attempts' => function ($query) {
                $query->where('passed', true);
            },
        ])
        ->with('questions')
        ->get()
        ->map(function ($quiz) {
            return [
                'quiz_id' => $quiz->id,
                'name' => $quiz->name,
                'start' => $quiz->start->format('d.m.Y'),
                'total_questions' => $quiz->questions->count(),
                'required_correct_answers' => $quiz->required_correct_answers,
                'total_attempts' => $quiz->total_attempts,
                'completed_attempts' => $quiz->completed_attempts,
                'passed_attempts' => $quiz->passed_attempts,
                'pass_rate' => $quiz->completed_attempts > 0
                    ? round(($quiz->passed_attempts / $quiz->completed_attempts) * 100, 1)
                    : 0,
            ];
        });

        // Get user statistics
        $userStats = User::withCount([
            'participants as total_quizzes_taken' => function ($query) {
                $query->whereNotNull('completed_at');
            },
            'participants as quizzes_passed' => function ($query) {
                $query->where('passed', true);
            },
        ])
        ->get()
        ->filter(function ($user) {
            return $user->total_quizzes_taken > 0;
        })
        ->sortByDesc('total_quizzes_taken')
        ->take(20)
        ->map(function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
                'organization' => $user->organization,
                'total_quizzes_taken' => $user->total_quizzes_taken,
                'quizzes_passed' => $user->quizzes_passed,
                'pass_rate' => $user->total_quizzes_taken > 0
                    ? round(($user->quizzes_passed / $user->total_quizzes_taken) * 100, 1)
                    : 0,
            ];
        });

        // Get full user statistics for each quiz
        $fullUserStats = Quiz::with(['participants' => function ($q) {
            $q->whereNotNull('completed_at')->with('user');
        }])
            ->get()
            ->map(function ($quiz) {
                return [
                    'quiz_id' => $quiz->id,
                    'participants' => $quiz->participants->map(function ($p) {
                        return [
                            'user_name' => $p->user->name,
                            'user_email' => $p->user->email,
                            'user_phone_number' => $p->user->phone_number,
                            'user_organization' => $p->user->organization,
                            'score' => $p->score,
                            'passed' => (bool) $p->passed,
                            'started_at' => $p->started_at ? $p->started_at->format('d.m.Y H:i') : null,
                            'completed_at' => $p->completed_at ? $p->completed_at->format('d.m.Y H:i') : null,
                        ];
                    })->values(),
                ];
            });

        $participantsByQuiz = $fullUserStats->keyBy('quiz_id');

        return view('admin.statistics', [
            'totalUsers' => $totalUsers,
            'totalQuizzes' => $totalQuizzes,
            'totalParticipants' => $totalParticipants,
            'totalAttempts' => $totalAttempts,
            'quizStats' => $quizStats,
            'userStats' => $userStats,
            'participantsByQuiz' => $participantsByQuiz,
        ]);
    }
}
