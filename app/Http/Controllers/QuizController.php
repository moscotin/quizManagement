<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\QuizQuestion;
use App\Models\QuizParticipant;
use App\Models\QuizResponse;
use App\Services\CertificateGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use CarbonInterval
use Carbon\CarbonInterval;

class QuizController extends Controller
{
    public function showMonth($month)
    {
        // Validate month
        $validMonths = ['february', 'march', 'april', 'may'];
        if (!in_array(strtolower($month), $validMonths)) {
            abort(404);
        }

        // Get quizzes from database for this month
        $quizCategories = QuizCategory::getCategoriesByMonth($month);

        return view('quiz.month', [
            'month' => ucfirst($month),
            'quizCats' => $quizCategories,
            'questionWording' => function ($count) {
                $lastDigit = $count % 10;
                if ($count % 100 >= 11 && $count % 100 <= 14) {
                    return 'вопросов';
                }
                switch ($lastDigit) {
                    case 1:
                        return 'вопрос';
                    case 2:
                    case 3:
                    case 4:
                        return 'вопроса';
                    default:
                        return 'вопросов';
                }
            },
            'russianMonthName' => function ($month) {
                $months = [
                    'january' => 'Январь',
                    'february' => 'Февраль',
                    'march' => 'Март',
                    'april' => 'Апрель',
                    'may' => 'Май',
                    'june' => 'Июнь',
                    'july' => 'Июль',
                    'august' => 'Август',
                    'september' => 'Сентябрь',
                    'october' => 'Октябрь',
                    'november' => 'Ноябрь',
                    'december' => 'Декабрь',
                ];
                return $months[strtolower($month)] ?? $month;
            },
        ]);
    }

    public function showCategory($month, $category_id)
    {
        // Validate month
        $validMonths = ['february', 'march', 'april', 'may'];
        if (!in_array(strtolower($month), $validMonths)) {
            abort(404);
        }

        // month from string to number
        $monthNum = strtotime($month);

        // Get quizzes from database for this month
        $quizzes = Quiz::where('category_id', $category_id)
            ->whereMonth('start', date('m', $monthNum))
            ->get();

        // Check if user completed all quizzes in this category
        $user = Auth::user();
        if ($user) {
            $all_quizzes_completed = true;
            foreach ($quizzes as $quiz) {
                if (!$quiz->isTakenByUser($user)) {
                    $all_quizzes_completed = false;
                    break;
                }
            }
        }

        return view('quiz.category', [
            'month' => ucfirst($month),
            'quizzes' => $quizzes,
            'questionWording' => function ($count) {
                $lastDigit = $count % 10;
                if ($count % 100 >= 11 && $count % 100 <= 14) {
                    return 'вопросов';
                }
                switch ($lastDigit) {
                    case 1:
                        return 'вопрос';
                    case 2:
                    case 3:
                    case 4:
                        return 'вопроса';
                    default:
                        return 'вопросов';
                }
            },
            'russianMonthName' => function ($month) {
                $months = [
                    'january' => 'Январь',
                    'february' => 'Февраль',
                    'march' => 'Март',
                    'april' => 'Апрель',
                    'may' => 'Май',
                    'june' => 'Июнь',
                    'july' => 'Июль',
                    'august' => 'Август',
                    'september' => 'Сентябрь',
                    'october' => 'Октябрь',
                    'november' => 'Ноябрь',
                    'december' => 'Декабрь',
                ];
                return $months[strtolower($month)] ?? $month;
            },
            'all_quizzes_completed' => $all_quizzes_completed ?? false,
        ]);
    }

    public function startQuiz($quizId)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        $user = Auth::user();

        // Check if user already has a participant record for this quiz
        $participant = QuizParticipant::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->first();

        if ($participant && $participant->completed_at) {
            return redirect()->route('quiz.complete', $quiz->id);
        }

        // Create or update participant record
        if (!$participant) {
            $participant = QuizParticipant::create([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'started_at' => now(),
            ]);
        } elseif (!$participant->started_at) {
            $participant->update(['started_at' => now()]);
        }

        // Get the first unanswered question or the first question
        $answeredQuestionIds = QuizResponse::where('participant_id', $participant->id)
            ->pluck('question_id')
            ->toArray();

        $currentQuestion = $quiz->questions()
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();

        if (!$currentQuestion) {
            // All questions answered, redirect to completion
            return redirect()->route('quiz.complete', $quiz->id);
        }

        return view('quiz.take', [
            'quiz' => $quiz,
            'participant' => $participant,
            'currentQuestion' => $currentQuestion,
            'questionNumber' => count($answeredQuestionIds) + 1,
            'totalQuestions' => $quiz->questions->count(),
        ]);
    }

    public function submitAnswer(Request $request, $quizId, $questionId)
    {
        $request->validate([
            'selected_option' => 'required|integer|min:1|max:4',
        ]);

        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::findOrFail($questionId);
        $user = Auth::user();

        // Get participant
        $participant = QuizParticipant::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Check if time limit exceeded (20 minutes)
        if ($participant->started_at && $participant->started_at->diffInMinutes(now()) > 20) {
            return response()->json([
                'error' => 'Время на прохождение викторины истекло.',
                'redirect' => route('quiz.complete', $quiz->id)
            ], 422);
        }

        // Save response
        $isCorrect = $request->selected_option == $question->correct_option;

        QuizResponse::updateOrCreate(
            [
                'participant_id' => $participant->id,
                'question_id' => $question->id,
            ],
            [
                'selected_option' => $request->selected_option,
                'is_correct' => $isCorrect,
            ]
        );

        // Get next question
        $answeredQuestionIds = QuizResponse::where('participant_id', $participant->id)
            ->pluck('question_id')
            ->toArray();

        $nextQuestion = $quiz->questions()
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();

        if ($nextQuestion) {
            return response()->json([
                'success' => true,
                'next_question' => [
                    'id' => $nextQuestion->id,
                    'question' => $nextQuestion->question,
                    'option_1' => $nextQuestion->option_1,
                    'option_2' => $nextQuestion->option_2,
                    'option_3' => $nextQuestion->option_3,
                    'option_4' => $nextQuestion->option_4,
                ],
                'question_number' => count($answeredQuestionIds),
            ]);
        } else {
            // Quiz complete
            return response()->json([
                'success' => true,
                'completed' => true,
                'redirect' => route('quiz.complete', $quiz->id)
            ]);
        }
    }

    public function completeQuiz($quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $user = Auth::user();

        $participant = QuizParticipant::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Calculate score
        $correctAnswers = QuizResponse::where('participant_id', $participant->id)
            ->where('is_correct', true)
            ->count();

        // Update participant with score and completion time
        // after checking if not already completed
        if ($participant->completed_at === null) {
            $participant->update([
                'score' => $correctAnswers,
                'completed_at' => now(),
            ]);
        }

        return view('quiz.complete', [
            'quiz' => $quiz,
            'score' => $correctAnswers,
            'totalQuestions' => $quiz->questions->count(),
            'participant' => $participant,
            'minutesTaken' => CarbonInterval::minutes(ceil($participant->started_at->diffInMinutes($participant->completed_at)))->locale('ru')->forHumans(),
        ]);
    }


    public function viewCertificate($quizId)
    {
        $user = auth()->user();

        $quiz = Quiz::findOrFail($quizId);

        abort_unless($quiz->isTakenByUser($user), 403);

        $image = app(CertificateGenerator::class)
            ->generate($quiz, $user->name);

        return response()->streamDownload(
            fn () => print($image),
            'certificate.jpg',
            ['Content-Type' => 'image/jpeg']
        );
    }

    public function viewDiploma($categoryId, $month)
    {
        $user = auth()->user();

        $quizzes = Quiz::where('category_id', $categoryId)
            ->whereMonth('start', date('m', strtotime($month)))
            ->get();
        // Check if user completed all quizzes in this category
        $all_quizzes_completed = true;
        foreach ($quizzes as $quiz) {
            if (!$quiz->isTakenByUser($user)) {
                $all_quizzes_completed = false;
                break;
            }
        }
        abort_unless($all_quizzes_completed, 403);

        $image = app(CertificateGenerator::class)
            ->generateDiploma($quizzes, $user->name, ucfirst($month));

        return response()->streamDownload(
            fn() => print($image),
            'diploma.jpg',
            ['Content-Type' => 'image/jpeg']
        );
    }

}
