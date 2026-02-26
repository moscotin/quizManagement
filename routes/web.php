<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Quiz routes
    Route::get('/quiz/{month}', [QuizController::class, 'showMonth'])->name('quiz.month');
    Route::get('/quiz/{month}/category/{category}', [QuizController::class, 'showCategory'])->name('quiz.category');
    Route::get('/quiz/{quizId}/start', [QuizController::class, 'startQuiz'])->name('quiz.start');
    Route::post('/quiz/{quizId}/question/{questionId}/answer', [QuizController::class, 'submitAnswer'])->name('quiz.submit-answer');
    Route::get('/quiz/{quizId}/complete', [QuizController::class, 'completeQuiz'])->name('quiz.complete');

    // Certificate and Diploma routes
    Route::get('/quiz/{quizId}/certificate', [QuizController::class, 'viewCertificate'])->name('quiz.certificate');
    Route::get('/quiz/{month}/category/{category}/diploma', [QuizController::class, 'viewDiploma'])->name('quiz.diploma');

    // Admin routes
    Route::get('/admin/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');
});

require __DIR__.'/auth.php';
