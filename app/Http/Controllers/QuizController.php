<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\QuizQuestion;
use App\Models\QuizParticipant;
use App\Models\QuizResponse;
use App\Services\CertificateGenerator;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function showMonth($month)
    {
        $validMonths = ['february', 'march', 'april', 'may'];
        if (!in_array(strtolower($month), $validMonths)) {
            abort(404);
        }

        $quizCategories = QuizCategory::getCategoriesByMonth($month);

        return view('quiz.month', [
            'month' => ucfirst($month),
            'quizCats' => $quizCategories,
            'questionWording' => function ($count) {
                $lastDigit = $count % 10;
                if ($count % 100 >= 11 && $count % 100 <= 14) {
                    return 'вопросов';
                }
                return match ($lastDigit) {
                    1 => 'вопрос',
                    2, 3, 4 => 'вопроса',
                    default => 'вопросов',
                };
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
        $validMonths = ['february', 'march', 'april', 'may'];
        if (!in_array(strtolower($month), $validMonths)) {
            abort(404);
        }

        $monthNum = strtotime($month . ' 1 ' . date('Y'));

        $quizzes = Quiz::where('category_id', $category_id)
            ->whereMonth('start', date('m', $monthNum))
            ->get();

        $category_name = QuizCategory::getCategoryName($category_id);

        $user = Auth::user();
        $all_quizzes_passed = true;
        foreach ($quizzes as $quiz) {
            if (!$quiz->isTakenByUser($user)) {
                $all_quizzes_passed = false;
                break;
            } else {
                $participant = QuizParticipant::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$participant || !$participant->passed) {
                    $all_quizzes_passed = false;
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
                return match ($lastDigit) {
                    1 => 'вопрос',
                    2, 3, 4 => 'вопроса',
                    default => 'вопросов',
                };
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
            'all_quizzes_passed' => $all_quizzes_passed,
            'category_id' => $category_id,
            'category_name' => $category_name,
            'category_desc' => QuizCategory::getCategoryDesc($category_id),
        ]);
    }

    public function startQuiz($quizId)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        $user = Auth::user();

        // First check if the quiz started and not ended
        if (now()->lt($quiz->start)) {
            abort(403, 'Викторина еще не началась.');
        }
        if (now()->gt($quiz->end)) {
            abort(403, 'Время на прохождение викторины истекло.');
        }

        $participant = QuizParticipant::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->first();

        if ($participant && $participant->completed_at) {
            return redirect()->route('quiz.complete', $quiz->id);
        }

        if (!$participant) {
            $participant = QuizParticipant::create([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'started_at' => now(),
            ]);
        } elseif (!$participant->started_at) {
            $participant->update(['started_at' => now()]);
        }

        $answeredQuestionIds = QuizResponse::where('participant_id', $participant->id)
            ->pluck('question_id')
            ->toArray();

        $currentQuestion = $quiz->questions()
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();

        if (!$currentQuestion) {
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
        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::findOrFail($questionId);
        $user = Auth::user();

        // Security: ensure question belongs to this quiz
        abort_unless((int)$question->quiz_id === (int)$quiz->id, 404);

        $participant = QuizParticipant::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Time limit (20 minutes)
        if (!$participant->started_at || $participant->started_at->diffInMinutes(now()) >= 20) {
            return response()->json([
                'error' => 'Время на прохождение викторины истекло.',
                'redirect' => route('quiz.complete', $quiz->id),
            ], 422);
        }

        // Type-aware validation
        $type = $question->question_type ?? 'single_choice';

        $rules = match ($type) {
            'multiple_choice' => [
                'selected_options' => 'required|array|min:1',
                'selected_options.*' => 'integer|min:1|max:6',
            ],
            'fill_in_the_blank', 'text' => [
                'answer_text' => 'required|string|max:10000',
            ],
            'matching' => [
                'matching_response' => 'required|array|min:1',
            ],
            default => [
                'selected_option' => 'required|integer|min:1|max:6',
            ],
        };

        $validated = $request->validate($rules);

        // Compute correctness + build response payload
        $isCorrect = false;
        $payload = [
            'selected_option' => null,
            'selected_options' => null,
            'answer_text' => null,
            'matching_response' => null,
            'is_correct' => false,
        ];

        if ($type === 'single_choice') {
            $payload['selected_option'] = (int) $validated['selected_option'];
            $isCorrect = ((int)$payload['selected_option'] === (int)$question->correct_option);
        } elseif ($type === 'multiple_choice') {
            $selected = array_values(array_unique(array_map('intval', $validated['selected_options'])));
            sort($selected);

            $correct = is_array($question->correct_options) ? $question->correct_options : [];
            $correct = array_values(array_unique(array_map('intval', $correct)));
            sort($correct);

            $payload['selected_options'] = $selected;
            $isCorrect = ($selected === $correct);
        } elseif ($type === 'fill_in_the_blank' || $type === 'text') {
            $answer = trim((string)$validated['answer_text']);
            $payload['answer_text'] = $answer;

            $correct = trim((string)($question->correct_answer ?? ''));

            // simple normalization
            $norm = fn(string $s) => mb_strtolower(preg_replace('/\s+/u', ' ', trim($s)));
            $isCorrect = $correct !== '' && $norm($answer) === $norm($correct);
        } elseif ($type === 'matching') {
            $resp = $validated['matching_response'] ?? [];
            $payload['matching_response'] = $resp;

            $correct = $question->matching_pairs ?? [];

            // Convert either format into a canonical map: ['Lithuania' => 'Vilnius', ...]
            $toMap = function ($v): array {
                if (!is_array($v)) return [];

                $isAssoc = array_keys($v) !== range(0, count($v) - 1);

                // Case 1: associative map form: {"Lithuania":"Vilnius", ...}
                if ($isAssoc) {
                    $out = [];
                    foreach ($v as $left => $right) {
                        $l = trim((string)$left);
                        $r = trim((string)$right);
                        if ($l !== '' && $r !== '') $out[$l] = $r;
                    }
                    ksort($out);
                    return $out;
                }

                // Case 2: list form: [{"left":"Lithuania","right":"Vilnius"}, ...]
                $out = [];
                foreach ($v as $row) {
                    if (!is_array($row)) continue;
                    $l = isset($row['left']) ? trim((string)$row['left']) : '';
                    $r = isset($row['right']) ? trim((string)$row['right']) : '';
                    if ($l !== '' && $r !== '') $out[$l] = $r;
                }
                ksort($out);
                return $out;
            };

            // Optional: tolerant normalization (case/spacing). Remove mb_strtolower if you want case-sensitive.
            $norm = function (array $map): array {
                $out = [];
                foreach ($map as $k => $v) {
                    $kk = mb_strtolower(preg_replace('/\s+/u', ' ', trim((string)$k)));
                    $vv = mb_strtolower(preg_replace('/\s+/u', ' ', trim((string)$v)));
                    if ($kk !== '' && $vv !== '') $out[$kk] = $vv;
                }
                ksort($out);
                return $out;
            };

            $isCorrect = $norm($toMap($resp)) === $norm($toMap($correct));
        } else {
            // unknown type: treat as single choice
            $payload['selected_option'] = (int) $validated['selected_option'];
            $isCorrect = ((int)$payload['selected_option'] === (int)$question->correct_option);
        }

        $payload['is_correct'] = $isCorrect;

        QuizResponse::updateOrCreate(
            [
                'participant_id' => $participant->id,
                'question_id' => $question->id,
            ],
            $payload
        );

        // Next question
        $answeredQuestionIds = QuizResponse::where('participant_id', $participant->id)
            ->pluck('question_id')
            ->toArray();

        $nextQuestion = $quiz->questions()
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();

        if ($nextQuestion) {
            return response()->json([
                'success' => true,
                'next_question' => $this->questionPayload($nextQuestion),
                'question_number' => count($answeredQuestionIds),
            ]);
        }

        return response()->json([
            'success' => true,
            'completed' => true,
            'redirect' => route('quiz.complete', $quiz->id),
        ]);
    }

    private function questionPayload(QuizQuestion $q): array
    {
        // Return a consistent payload for any question type
        return [
            'id' => $q->id,
            'question' => $q->question,
            'question_type' => $q->question_type,
            'image' => $q->image,

            // options (keep both forms: array + legacy fields)
            'options' => array_values(array_filter([
                1 => $q->option_1,
                2 => $q->option_2,
                3 => $q->option_3,
                4 => $q->option_4,
                5 => $q->option_5,
                6 => $q->option_6,
            ], fn($v) => $v !== null && $v !== '')),

            'option_1' => $q->option_1,
            'option_2' => $q->option_2,
            'option_3' => $q->option_3,
            'option_4' => $q->option_4,
            'option_5' => $q->option_5,
            'option_6' => $q->option_6,

            // for matching only (frontend uses this to render)
            'matching_pairs' => $q->question_type === 'matching' ? $q->matching_pairs : null,
        ];
    }

    public function completeQuiz($quizId)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        $user = Auth::user();

        $participant = QuizParticipant::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $correctAnswers = QuizResponse::where('participant_id', $participant->id)
            ->where('is_correct', true)
            ->count();

        if($correctAnswers >= $quiz->required_correct_answers) {
            $passed = true;
        } else {
            $passed = false;
        }

        if ($participant->completed_at === null) {
            $participant->update([
                'score' => $correctAnswers,
                'passed' => $passed,
                'completed_at' => now(),
            ]);
        }

        return view('quiz.complete', [
            'quiz' => $quiz,
            'score' => $correctAnswers,
            'totalQuestions' => $quiz->questions->count(),
            'participant' => $participant,
            'minutesTaken' => CarbonInterval::minutes(
                ceil($participant->started_at->diffInMinutes($participant->completed_at))
            )->locale('ru')->forHumans(),
        ]);
    }

    public function viewCertificate($quizId)
    {
        $user = auth()->user();
        $quiz = Quiz::findOrFail($quizId);

        abort_unless($quiz->isTakenByUser($user), 403);

        $image = app(CertificateGenerator::class)->generate($quiz, $user->name, $user->organization);

        return response()->streamDownload(
            fn() => print($image),
            'certificate.jpg',
            ['Content-Type' => 'image/jpeg']
        );
    }

    public function viewDiploma($month, $category)
    {
        $user = auth()->user();

        $monthNum = strtotime($month . ' 1 ' . date('Y'));

        $quizzes = Quiz::where('category_id', $category)
            ->whereMonth('start', date('m', $monthNum))
            ->get();

        $quizzesTakenByUser = $quizzes->filter(fn($quiz) => $quiz->isTakenByUser($user));
        abort_unless($quizzesTakenByUser->count() === $quizzes->count(), 403);

        foreach ($quizzesTakenByUser as $quiz) {
            $participant = QuizParticipant::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->first();

            abort_unless($participant && $participant->passed, 403);
        }

        $image = app(CertificateGenerator::class)->generateDiploma($quizzes->first(), $user->name, $user->organization);

        return response()->streamDownload(
            fn() => print($image),
            'diploma.jpg',
            ['Content-Type' => 'image/jpeg']
        );
    }
}
