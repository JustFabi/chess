<?php

namespace App\Http\Controllers;

use App\Models\ChessGame;
use App\Services\Chess\PlayVsAiGameService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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

        $gameState = $gameService->createGameState($settings);
        $game = ChessGame::create([
            'state' => $this->stripPossibleMoves($gameState),
            'status' => 'active',
        ]);

        return Inertia::render('PlayVsAiMatch', [
            'gameId' => $game->id,
            'gameState' => $gameState,
        ]);
    }

    public function move(Request $request, PlayVsAiGameService $gameService): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'gameId' => ['required', 'integer', 'exists:chess_games,id'],
            'move.from' => ['required', 'string', 'regex:/^[a-h][1-8]$/'],
            'move.to' => ['required', 'string', 'regex:/^[a-h][1-8]$/'],
        ]);

        $move = $validated['move'];
        $game = ChessGame::findOrFail($validated['gameId']);
        $gameState = is_array($game->state) ? $game->state : [];

        $nextState = $gameService->applyMove($gameState, $move);
        if (isset($nextState['error'])) {
            throw ValidationException::withMessages([
                'move' => $nextState['error'],
            ]);
        }

        $game->state = $this->stripPossibleMoves($nextState);
        $game->save();

        $payload = [
            'gameId' => $game->id,
            'gameState' => $nextState,
        ];

        return response()->json($payload);
    }

    private function stripPossibleMoves(array $state): array
    {
        if (isset($state['board']) && is_array($state['board'])) {
            unset($state['board']['possibleMoves']);
        }

        return $state;
    }
}
