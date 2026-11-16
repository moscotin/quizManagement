<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizParticipant;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function statistics(Request $request)
    {
        // Only allow admin users
        $user = auth()->user();
        abort_unless($user && ($user->is_admin ?? false), 403, 'Доступ запрещен.');

        $archive = $request->boolean('archive');
        $seasonQuiz = fn ($query) => $query->forSeason($archive);
        $seasonAttempt = fn ($query) => $query->whereHas('quiz', $seasonQuiz);
        $completedAttempt = fn ($query) => $seasonAttempt($query)->whereNotNull('completed_at');
        $attempts = QuizParticipant::whereHas('quiz', $seasonQuiz);

        $totalUsers = (clone $attempts)->distinct()->count('user_id');
        $totalQuizzes = Quiz::forSeason($archive)->count();
        $totalParticipants = (clone $attempts)->whereNotNull('completed_at')->count();
        $totalAttempts = $attempts->count();

        // Get quiz-specific statistics
        $quizStats = Quiz::forSeason($archive)->withCount([
            'questions',
            'participants as total_attempts',
            'participants as completed_attempts' => function ($query) {
                $query->whereNotNull('completed_at');
            },
            'participants as passed_attempts' => function ($query) {
                $query->where('passed', true);
            },
        ])
        ->orderBy('start')
        ->orderBy('id')
        ->get()
        ->map(function ($quiz) {
            return [
                'quiz_id' => $quiz->id,
                'name' => $quiz->name,
                'start' => $quiz->start->format('d.m.Y'),
                'total_questions' => $quiz->questions_count,
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
        $userStats = User::whereHas('participants', $completedAttempt)
        ->withCount([
            'participants as total_quizzes_taken' => $completedAttempt,
            'participants as quizzes_passed' => fn ($query) => $completedAttempt($query)->where('passed', true),
        ])
        ->orderByDesc('total_quizzes_taken')
        ->orderBy('id')
        ->limit(20)
        ->get()
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
        $fullUserStats = Quiz::forSeason($archive)->with(['participants' => function ($q) {
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
            'archive' => $archive,
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
