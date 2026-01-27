<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
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

require __DIR__.'/settings.php';
