<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LocalMatchController;
use App\Http\Controllers\PlayVsAiController;
use App\Http\Controllers\QuickMatchController;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('play-vs-ai', [PlayVsAiController::class, 'index'])->name('play-vs-ai');
Route::match(['get', 'post'], 'play-vs-ai/match', [PlayVsAiController::class, 'match'])
    ->name('play-vs-ai.match');
Route::post('play-vs-ai/move', [PlayVsAiController::class, 'move'])
    ->name('play-vs-ai.move');
Route::post('play-vs-ai/action', [PlayVsAiController::class, 'action'])
    ->name('play-vs-ai.action');
Route::get('local-match', [LocalMatchController::class, 'index'])
    ->name('local-match');
Route::match(['get', 'post'], 'local-match/match', [LocalMatchController::class, 'match'])
    ->name('local-match.match');
Route::post('local-match/move', [LocalMatchController::class, 'move'])
    ->name('local-match.move');
Route::post('local-match/action', [LocalMatchController::class, 'action'])
    ->name('local-match.action');

Route::post('quick-match/queue', [QuickMatchController::class, 'queue'])
    ->name('quick-match.queue');
Route::get('quick-match/status/{queueKey}', [QuickMatchController::class, 'status'])
    ->name('quick-match.status');
Route::post('quick-match/cancel', [QuickMatchController::class, 'cancel'])
    ->name('quick-match.cancel');
Route::get('quick-match/match/{game}', [QuickMatchController::class, 'match'])
    ->name('quick-match.match');
Route::post('quick-match/move', [QuickMatchController::class, 'move'])
    ->name('quick-match.move');
Route::post('quick-match/action', [QuickMatchController::class, 'action'])
    ->name('quick-match.action');

require __DIR__.'/settings.php';
