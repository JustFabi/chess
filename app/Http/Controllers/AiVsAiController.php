<?php

namespace App\Http\Controllers;

use App\Models\ChessGame;
use App\Services\Chess\Engine\ChessEngineService;
use App\Services\Chess\Engine\UciEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AiVsAiController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('AiVsAi');
    }

    public function match(Request $request, ChessEngineService $gameService): Response
    {
        $settings = $request->validate([
            'timeControl' => ['nullable', 'in:3+2,5+0,10+5,15+10'],
            'variant' => ['nullable', 'in:standard'],
        ]);

        // In AI vs AI, side doesn't matter for player, but we can set a default
        $settings['side'] = 'white';

        $gameState = $gameService->createGameState($settings);

        $game = ChessGame::create([
            'state' => $this->stripPossibleMoves($gameState),
            'status' => 'active',
        ]);

        return Inertia::render('AiVsAiMatch', [
            'gameId' => $game->id,
            'gameState' => $gameState,
        ]);
    }

    public function engineMove(Request $request, ChessEngineService $gameService, UciEngineService $uciService): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'gameId' => ['required', 'integer', 'exists:chess_games,id'],
        ]);

        $game = ChessGame::findOrFail($validated['gameId']);
        $gameState = is_array($game->state) ? $game->state : [];

        $now = time();
        $gameState = $gameService->syncClock($gameState, $now);

        if (!empty($gameState['result'])) {
            return response()->json([
                'gameId' => $game->id,
                'gameState' => $gameService->hydrateState($gameState),
            ]);
        }

        $uciMoves = [];
        foreach ($gameState['moves'] ?? [] as $m) {
            $mStr = $m['from'] . $m['to'] . ($m['promotion'] ?? '');
            $uciMoves[] = $mStr;
        }

        $movetime = 1000;
        if (isset($gameState['clock'])) {
            $side = $gameState['clock']['active'] ?? 'white';
            $remaining = (int) ($gameState['clock'][$side] ?? 0);
            $increment = (int) ($gameState['clock']['increment'] ?? 0);

            $movetime = (int) (($remaining / 40) + ($increment * 0.9)) * 1000;
            $movetime = max(100, min(10000, $movetime));
        }

        $uciMoveStr = $uciService->getBestMove($uciMoves, $movetime);
        if ($uciMoveStr) {
            $uciMove = [
                'from' => substr($uciMoveStr, 0, 2),
                'to' => substr($uciMoveStr, 2, 2),
                'promotion' => strlen($uciMoveStr) > 4 ? substr($uciMoveStr, 4, 1) : null,
            ];

            $gameState = $gameService->applyMove($gameState, $uciMove, time());

            $game->state = $this->stripPossibleMoves($gameState);
            if (!empty($gameState['result'])) {
                $game->status = 'finished';
            }
            $game->save();
        }

        return response()->json([
            'gameId' => $game->id,
            'gameState' => $gameState,
        ]);
    }

    public function action(Request $request, ChessEngineService $gameService): JsonResponse
    {
        $validated = $request->validate([
            'gameId' => ['required', 'integer', 'exists:chess_games,id'],
            'action' => [
                'required',
                'string',
                'in:restart',
            ],
        ]);

        $game = ChessGame::findOrFail($validated['gameId']);
        $gameState = is_array($game->state) ? $game->state : [];
        $action = $validated['action'];

        if ($action === 'restart') {
            $settings = $gameState['settings'] ?? [];
            $nextState = $gameService->createGameState($settings);
            $game->status = 'active';
            $game->state = $this->stripPossibleMoves($nextState);
            $game->save();

            return response()->json([
                'gameId' => $game->id,
                'gameState' => $nextState,
            ]);
        }

        return response()->json(['error' => 'Invalid action'], 400);
    }

    private function stripPossibleMoves(array $state): array
    {
        if (isset($state['board']) && is_array($state['board'])) {
            unset($state['board']['possibleMoves']);
        }

        return $state;
    }
}
