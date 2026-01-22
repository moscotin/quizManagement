<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizParticipant;
use App\Models\QuizResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $quizzes = Quiz::whereMonth('start', date('m', strtotime($month)))
            ->orWhere('name', 'like', '%' . ucfirst($month) . '%')
            ->get();

        return view('quiz.month', [
            'month' => ucfirst($month),
            'quizzes' => $quizzes
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
            return redirect()->route('dashboard')->with('error', 'You have already completed this quiz.');
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
            // All questions answered
            $currentQuestion = $quiz->questions()->first();
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
                'error' => 'Time limit exceeded',
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
                'question_number' => count($answeredQuestionIds) + 1,
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
        $participant->update([
            'score' => $correctAnswers,
            'completed_at' => now(),
        ]);

        return view('quiz.complete', [
            'quiz' => $quiz,
            'score' => $correctAnswers,
            'totalQuestions' => $quiz->questions->count(),
            'participant' => $participant,
        ]);
    }
}
