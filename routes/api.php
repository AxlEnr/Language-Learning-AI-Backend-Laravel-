<?php

use App\Http\Controllers\Api\V1\AIController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\ModuleController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VocabularyController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::get('/languages', [LanguageController::class, 'index']);
    Route::get('/languages/{id}', [LanguageController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::prefix('auth')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/user', [AuthController::class, 'user']);
        });

        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::prefix('modules')->group(function (): void {
            Route::get('/', [ModuleController::class, 'index']);
            Route::get('/{id}', [ModuleController::class, 'show']);
        });

        Route::prefix('lessons')->group(function (): void {
            Route::get('/{id}', [LessonController::class, 'show']);
        });

        Route::prefix('progress')->group(function (): void {
            Route::get('/', [ProgressController::class, 'overview']);
            Route::post('/start', [ProgressController::class, 'startLesson']);
            Route::post('/answer', [ProgressController::class, 'submitAnswer']);
            Route::post('/lessons/{lessonId}/complete', [ProgressController::class, 'completeLesson']);
        });

        Route::prefix('ai')->group(function (): void {
            Route::post('/conversations', [AIController::class, 'startConversation']);
            Route::get('/conversations', [AIController::class, 'listConversations']);
            Route::get('/conversations/{conversationId}', [AIController::class, 'getConversation']);
            Route::post('/conversations/{conversationId}/messages', [AIController::class, 'sendMessage']);
            Route::post('/exercises/generate', [AIController::class, 'generateExercise']);
            Route::get('/recommendations/lesson', [AIController::class, 'recommendLesson']);
        });

        Route::prefix('vocabulary')->group(function (): void {
            Route::get('/review', [VocabularyController::class, 'wordsForReview']);
            Route::post('/review/{userWordId}', [VocabularyController::class, 'reviewWord']);
            Route::post('/add/{wordId}', [VocabularyController::class, 'addWord']);
            Route::get('/mastered', [VocabularyController::class, 'masteredWords']);
            Route::get('/progress', [VocabularyController::class, 'progress']);
        });

        Route::prefix('user')->group(function (): void {
            Route::put('/profile', [UserController::class, 'updateProfile']);
            Route::get('/skills', [UserController::class, 'skills']);
            Route::get('/stats', [UserController::class, 'stats']);
        });
    });
});
