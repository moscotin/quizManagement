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
                'id' => $quiz->id,
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

        // Get full user statistics
        // get users and the quizzes they have taken
        $fullUserStats = User::query()
            ->whereHas('participants', function ($q) {
                $q->whereNotNull('completed_at');
            })
            ->with(['participants' => function ($q) {
                $q->whereNotNull('completed_at')
                    ->with('quiz')
                    ->orderByDesc('completed_at');
            }])
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'age' => $user->age,
                    'organization' => $user->organization,
                    'quizzes_taken' => $user->participants->map(function ($participant) {
                        return [
                            'quiz_name' => $participant->quiz->name,
                            'score' => $participant->score,
                            'passed' => $participant->passed,
                            'completed_at' => $participant->completed_at ? $participant->completed_at->format('d.m.Y H:i') : null,
                        ];
                    }),
                ];
            });

        return view('admin.statistics', [
            'totalUsers' => $totalUsers,
            'totalQuizzes' => $totalQuizzes,
            'totalParticipants' => $totalParticipants,
            'totalAttempts' => $totalAttempts,
            'quizStats' => $quizStats,
            'userStats' => $userStats,
            'fullUserStats' => $fullUserStats,
        ]);
    }
}
