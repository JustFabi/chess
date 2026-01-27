<?php

namespace App\Http\Controllers;

use App\Services\Chess\PlayVsAiGameService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlayVsAiController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('PlayVsAi');
    }

    public function match(Request $request, PlayVsAiGameService $gameService): Response
    {
        $settings = $request->validate([
            'side' => ['nullable', 'in:white,black,random'],
            'timeControl' => ['nullable', 'in:3+2,5+0,10+5,15+10'],
            'variant' => ['nullable', 'in:standard'],
        ]);

        return Inertia::render('PlayVsAiMatch', [
            'gameState' => $gameService->createGameState($settings),
        ]);
    }
}
