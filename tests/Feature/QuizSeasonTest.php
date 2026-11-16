<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class QuizSeasonTest extends TestCase
{
    use RefreshDatabase;

    public function test_period_pages_open_on_the_published_dates(): void
    {
        $this->actingAs(User::factory()->create(['age' => 15, 'phone_number' => '+79000000001']));
        $arts = QuizCategory::where('period', 'december-january')->firstOrFail();
        $firstQuiz = $arts->quizzes()->firstOrFail();

        foreach (config('quiz.periods') as $slug => $period) {
            $opensAt = Carbon::parse($period['opens_on']);
            $url = route('quiz.month', $slug);

            $this->travelTo($opensAt->copy()->subSecond());
            $this->get(route('dashboard'))->assertOk()
                ->assertSee($opensAt->format('d.m.Y'))
                ->assertDontSee($url);
            $this->get($url)->assertForbidden();

            if ($slug === 'december-january') {
                $this->get(route('quiz.category', [$slug, $arts->id]))->assertForbidden();
                $this->get(route('quiz.start', $firstQuiz->id))->assertForbidden();
            }

            $this->travelTo($opensAt);
            $this->get($url)->assertOk()->assertSee($period['label']);
        }

        $secondPeriod = QuizCategory::where('period', 'january-february')->get();
        $this->assertCount(2, $secondPeriod);
        $this->get(route('quiz.category', ['december-january', $secondPeriod->first()->id]))->assertNotFound();
        $this->get(route('quiz.category', ['january-february', $secondPeriod->first()->id]))
            ->assertOk()->assertSee('Викторины пока не опубликованы.')->assertDontSee('Скачать диплом');
        $this->get(route('quiz.diploma', ['january-february', $secondPeriod->first()->id]))->assertForbidden();
        $this->get(route('quiz.start', $firstQuiz->id))->assertForbidden();
    }

    public function test_arts_quizzes_issue_certificates_and_a_diploma(): void
    {
        $this->actingAs(User::factory()->create(['age' => 15, 'phone_number' => '+79000000002']));
        $this->travelTo(Carbon::parse('2027-01-15 10:00:00'));

        $category = QuizCategory::where('period', 'december-january')->firstOrFail();
        $categoryUrl = route('quiz.category', [$category->period, $category->id]);
        $diplomaUrl = route('quiz.diploma', [$category->period, $category->id]);
        $quizzes = $category->quizzes()->with('questions')->get();
        $this->assertCount(3, $quizzes);
        $this->get($diplomaUrl)->assertForbidden();

        $categoryPage = $this->get($categoryUrl)->assertOk();
        $this->assertSame(1, substr_count($categoryPage->getContent(), 'id="quiz-modal"'));

        foreach ($quizzes as $quiz) {
            $categoryPage->assertSee($quiz->name);
            $this->assertCount(15, $quiz->questions);
            $this->get(route('quiz.start', $quiz->id))->assertOk()->assertSee($quiz->questions->first()->question);

            foreach ($quiz->questions as $question) {
                $this->postJson(route('quiz.submit-answer', [$quiz->id, $question->id]), [
                    'selected_option' => $question->correct_option,
                ])->assertOk();
            }

            $this->get(route('quiz.complete', $quiz->id))->assertOk()->assertSee($categoryUrl);
            $certificate = $this->get(route('quiz.certificate', $quiz->id))->assertDownload('certificate.jpg');
            $this->assertStringStartsWith("\xFF\xD8", $certificate->streamedContent());
        }

        $this->get($categoryUrl)->assertOk()->assertSee('Скачать диплом');
        $diploma = $this->get($diplomaUrl)->assertDownload('diploma.jpg');
        $this->assertStringStartsWith("\xFF\xD8", $diploma->streamedContent());
    }

    public function test_statistics_keep_prior_quizzes_and_participants_in_the_archive(): void
    {
        $user = User::factory()->create(['age' => 15, 'phone_number' => '+79000000003']);
        $admin = User::factory()->create(['age' => 18, 'phone_number' => '+79000000004', 'is_admin' => true]);
        $archivedQuiz = Quiz::create([
            'name' => 'Прошлогодняя викторина',
            'category_id' => QuizCategory::create(['name' => 'Прошлогоднее направление'])->id,
            'start' => '2026-02-01 00:00:00',
            'end' => '2026-02-28 23:59:59',
            'required_correct_answers' => 1,
        ]);
        $archivedQuiz->participants()->create([
            'user_id' => $user->id,
            'score' => 1,
            'passed' => true,
            'started_at' => '2026-02-01 10:00:00',
            'completed_at' => '2026-02-01 10:05:00',
        ]);
        $seasonUrl = route('admin.statistics');
        $archiveUrl = route('admin.statistics', ['archive' => 1]);

        $this->actingAs($user)->get($archiveUrl)->assertForbidden();
        $this->actingAs($admin)->get($seasonUrl)->assertOk()
            ->assertSee('Знатоки искусств')->assertDontSee($archivedQuiz->name)->assertDontSee($user->email)
            ->assertViewHas('totalAttempts', 0);

        $this->get($archiveUrl)->assertOk()
            ->assertSee($archivedQuiz->name)->assertSee($user->email)->assertDontSee('Знатоки искусств')
            ->assertViewHas('totalAttempts', 1);

        $seasonQuiz = Quiz::forSeason(false)->firstOrFail();
        $seasonQuiz->participants()->create([
            'user_id' => $user->id,
            'started_at' => '2026-12-01 01:00:00',
        ]);
        $this->get($seasonUrl)->assertOk()->assertViewHas('totalAttempts', 1)->assertViewHas('totalParticipants', 0);
        $this->get($archiveUrl)->assertOk()->assertViewHas('totalAttempts', 1)->assertViewHas('totalParticipants', 1);
    }
}
