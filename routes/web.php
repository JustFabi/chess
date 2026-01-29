<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LocalMatchController;
use App\Http\Controllers\PlayVsAiController;

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
Route::get('local-match', [LocalMatchController::class, 'index'])
    ->name('local-match');
Route::match(['get', 'post'], 'local-match/match', [LocalMatchController::class, 'match'])
    ->name('local-match.match');
Route::post('local-match/move', [LocalMatchController::class, 'move'])
    ->name('local-match.move');

require __DIR__.'/settings.php';
