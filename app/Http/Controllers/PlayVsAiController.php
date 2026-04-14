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

class PlayVsAiController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('PlayVsAi');
    }

    public function match(Request $request, ChessEngineService $gameService): Response
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

    public function move(Request $request, ChessEngineService $gameService, UciEngineService $uciService): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'gameId' => ['required', 'integer', 'exists:chess_games,id'],
            'move.from' => ['required', 'string', 'regex:/^[a-h][1-8]$/'],
            'move.to' => ['required', 'string', 'regex:/^[a-h][1-8]$/'],
            'move.promotion' => ['nullable', 'string', 'in:q,r,b,n'],
        ]);

        $move = $validated['move'];
        \Illuminate\Support\Facades\Log::debug('PlayVsAiController@move called', [
            'gameId' => $validated['gameId'],
            'move' => $move,
        ]);

        $game = ChessGame::findOrFail($validated['gameId']);
        $gameState = is_array($game->state) ? $game->state : [];

        $now = time();
        $gameState = $gameService->syncClock($gameState, $now);

        \Illuminate\Support\Facades\Log::debug('Game state synced in move', [
            'active' => $gameState['clock']['active'] ?? null,
            'result' => $gameState['result'] ?? null,
        ]);

        $timeout = ($gameState['result']['reason'] ?? null) === 'timeout';
        if ($timeout) {
            $game->state = $this->stripPossibleMoves($gameState);
            $game->status = 'finished';
            $game->save();

            $payload = [
                'gameId' => $game->id,
                'gameState' => $gameService->hydrateState($game->state),
            ];

            return response()->json($payload);
        }

        if (!empty($gameState['result'])) {
            throw ValidationException::withMessages([
                'move' => 'Game is already finished.',
            ]);
        }

        $nextState = $gameService->applyMove($gameState, $move, $now);
        if (isset($nextState['error'])) {
            throw ValidationException::withMessages([
                'move' => $nextState['error'],
            ]);
        }

        $game->state = $this->stripPossibleMoves($nextState);
        if (!empty($nextState['result'])) {
            $game->status = 'finished';
        }
        $game->save();

        $payload = [
            'gameId' => $game->id,
            'gameState' => $nextState,
        ];

        return response()->json($payload);
    }

    public function engineMove(Request $request, ChessEngineService $gameService, UciEngineService $uciService): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'gameId' => ['required', 'integer', 'exists:chess_games,id'],
        ]);

        \Illuminate\Support\Facades\Log::debug('PlayVsAiController@engineMove called', [
            'gameId' => $validated['gameId'],
        ]);

        $game = ChessGame::findOrFail($validated['gameId']);
        $gameState = is_array($game->state) ? $game->state : [];

        $now = time();
        $gameState = $gameService->syncClock($gameState, $now);

        if (!empty($gameState['result'])) {
            throw ValidationException::withMessages([
                'game' => 'Game is already finished.',
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

            // Simple move time heuristic: 1/40th of remaining time + 90% of increment
            // Aim for at least 100ms and no more than 10 seconds for standard play
            $movetime = (int) (($remaining / 40) + ($increment * 0.9)) * 1000;
            $movetime = max(100, min(10000, $movetime));
        }

        \Illuminate\Support\Facades\Log::debug('Requesting AI move for game', [
            'gameId' => $game->id,
            'moves_count' => count($uciMoves),
            'movetime' => $movetime,
        ]);

        $uciMoveStr = $uciService->getBestMove($uciMoves, $movetime);
        \Illuminate\Support\Facades\Log::debug('AI move result', [
            'gameId' => $game->id,
            'bestMove' => $uciMoveStr,
        ]);
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
        } else {
            // Log if engine didn't return a move
            \Illuminate\Support\Facades\Log::warning('UCI engine returned no move', [
                'gameId' => $game->id,
                'moves' => $uciMoves
            ]);
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
                'in:offer-draw,accept-draw,decline-draw,resign,restart',
            ],
            'side' => ['nullable', 'in:white,black'],
        ]);

        $game = ChessGame::findOrFail($validated['gameId']);
        $gameState = is_array($game->state) ? $game->state : [];
        $action = $validated['action'];
        $side = $validated['side'] ?? null;

        if ($action !== 'restart') {
            if (!empty($gameState['result'])) {
                throw ValidationException::withMessages([
                    'action' => 'Game is already finished.',
                ]);
            }

            $timedState = $gameService->syncClock($gameState);
            $timeout = ($timedState['result']['reason'] ?? null) === 'timeout';
            if ($timeout) {
                $game->state = $this->stripPossibleMoves($timedState);
                $game->status = 'finished';
                $game->save();

                return response()->json([
                    'gameId' => $game->id,
                    'gameState' => $gameService->hydrateState($game->state),
                ]);
            }
        }

        if (in_array($action, ['offer-draw', 'resign'], true) && !$side) {
            throw ValidationException::withMessages([
                'side' => 'Side is required.',
            ]);
        }

        if ($action === 'restart') {
            $settings = $gameState['settings'] ?? [];
            $nextState = $gameService->createGameState($settings);
            $game->status = 'active';
        } else {
            $hydratedState = $gameService->hydrateState($gameState);
            $drawOffer = $hydratedState['drawOffer'] ?? null;
            $hasPendingOffer =
                is_array($drawOffer) && ($drawOffer['status'] ?? null) === 'pending';

            if ($action === 'offer-draw') {
                if ($hasPendingOffer) {
                    throw ValidationException::withMessages([
                        'action' => 'A draw offer is already pending.',
                    ]);
                }

                $hydratedState['drawOffer'] = [
                    'from' => $side,
                    'status' => 'pending',
                ];
                $nextState = $hydratedState;
                $game->status = 'active';
            } elseif ($action === 'accept-draw') {
                if (!$hasPendingOffer) {
                    throw ValidationException::withMessages([
                        'action' => 'No draw offer to accept.',
                    ]);
                }

                $nextState = $this->finalizeState($hydratedState, [
                    'winner' => 'draw',
                    'reason' => 'draw',
                ]);
                $game->status = 'finished';
            } elseif ($action === 'decline-draw') {
                if (!$hasPendingOffer) {
                    throw ValidationException::withMessages([
                        'action' => 'No draw offer to decline.',
                    ]);
                }

                $hydratedState['drawOffer'] = null;
                $nextState = $hydratedState;
                $game->status = 'active';
            } else {
                $nextState = $this->finalizeState($hydratedState, [
                    'winner' => $side === 'white' ? 'black' : 'white',
                    'reason' => 'resign',
                ]);
                $game->status = 'finished';
            }
        }

        $game->state = $this->stripPossibleMoves($nextState);
        $game->save();

        return response()->json([
            'gameId' => $game->id,
            'gameState' => $nextState,
        ]);
    }

    private function stripPossibleMoves(array $state): array
    {
        if (isset($state['board']) && is_array($state['board'])) {
            unset($state['board']['possibleMoves']);
        }

        return $state;
    }

    private function finalizeState(array $state, array $result): array
    {
        $state['result'] = $result;
        $state['drawOffer'] = null;
        if (isset($state['board']) && is_array($state['board'])) {
            $state['board']['possibleMoves'] = [];
        }

        return $state;
    }
}
