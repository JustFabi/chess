<?php

namespace App\Http\Controllers;

use App\Events\QuickMatchFound;
use App\Events\QuickMatchMove;
use App\Models\ChessGame;
use App\Models\QuickMatchQueue;
use App\Services\Chess\Engine\ChessEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class QuickMatchController extends Controller
{
    public function queue(Request $request, ChessEngineService $gameService): JsonResponse
    {
        $sessionId = $request->session()->getId();
        $userId = $request->user()?->id;

        $existing = QuickMatchQueue::query()
            ->where('session_id', $sessionId)
            ->where('status', 'waiting')
            ->latest('created_at')
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'waiting',
                'queueKey' => $existing->queue_key,
            ]);
        }

        $result = DB::transaction(function () use ($sessionId, $userId, $gameService) {
            $queue = QuickMatchQueue::create([
                'queue_key' => (string) Str::uuid(),
                'user_id' => $userId,
                'session_id' => $sessionId,
                'status' => 'waiting',
            ]);

            $opponent = QuickMatchQueue::query()
                ->where('status', 'waiting')
                ->where('session_id', '!=', $sessionId)
                ->where('id', '!=', $queue->id)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if (!$opponent) {
                return [
                    'status' => 'waiting',
                    'queue' => $queue,
                ];
            }

            $gameState = $gameService->createGameState();
            $game = ChessGame::create([
                'state' => $this->stripPossibleMoves($gameState),
                'status' => 'active',
            ]);

            $opponentSide = Arr::random(['white', 'black']);
            $queueSide = $opponentSide === 'white' ? 'black' : 'white';

            $opponent->update([
                'status' => 'matched',
                'game_id' => $game->id,
                'side' => $opponentSide,
            ]);

            $queue->update([
                'status' => 'matched',
                'game_id' => $game->id,
                'side' => $queueSide,
            ]);

            return [
                'status' => 'matched',
                'queue' => $queue,
                'opponent' => $opponent,
                'game' => $game,
            ];
        });

        if ($result['status'] === 'matched') {
            $gameId = $result['game']->id;
            broadcast(new QuickMatchFound($result['queue']->queue_key, $gameId));
            broadcast(new QuickMatchFound($result['opponent']->queue_key, $gameId));

            return response()->json([
                'status' => 'matched',
                'queueKey' => $result['queue']->queue_key,
                'gameId' => $gameId,
            ]);
        }

        return response()->json([
            'status' => 'waiting',
            'queueKey' => $result['queue']->queue_key,
        ]);
    }

    public function status(Request $request, string $queueKey): JsonResponse
    {
        $queue = QuickMatchQueue::query()
            ->where('queue_key', $queueKey)
            ->firstOrFail();

        if ($queue->session_id !== $request->session()->getId()) {
            abort(403);
        }

        return response()->json([
            'status' => $queue->status,
            'gameId' => $queue->game_id,
            'side' => $queue->side,
        ]);
    }

    public function cancel(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'queueKey' => ['required', 'string'],
        ]);

        $queue = QuickMatchQueue::query()
            ->where('queue_key', $validated['queueKey'])
            ->where('session_id', $request->session()->getId())
            ->where('status', 'waiting')
            ->first();

        if ($queue) {
            $queue->status = 'canceled';
            $queue->save();
        }

        return response()->json([
            'status' => $queue ? 'canceled' : 'idle',
        ]);
    }

    public function match(
        Request $request,
        ChessGame $game,
        ChessEngineService $gameService,
    ): Response {
        $queue = QuickMatchQueue::query()
            ->where('game_id', $game->id)
            ->where('session_id', $request->session()->getId())
            ->where('status', 'matched')
            ->first();

        if (!$queue) {
            abort(403);
        }

        $storedState = is_array($game->state) ? $game->state : [];
        $gameState = $gameService->hydrateState($storedState);

        return Inertia::render('QuickMatchMatch', [
            'gameId' => $game->id,
            'gameState' => $gameState,
            'playerSide' => $queue->side,
        ]);
    }

    public function move(Request $request, ChessEngineService $gameService): JsonResponse
    {
        $validated = $request->validate([
            'gameId' => ['required', 'integer', 'exists:chess_games,id'],
            'move.from' => ['required', 'string', 'regex:/^[a-h][1-8]$/'],
            'move.to' => ['required', 'string', 'regex:/^[a-h][1-8]$/'],
        ]);

        $queue = QuickMatchQueue::query()
            ->where('game_id', $validated['gameId'])
            ->where('session_id', $request->session()->getId())
            ->where('status', 'matched')
            ->first();

        if (!$queue) {
            abort(403);
        }

        $game = ChessGame::findOrFail($validated['gameId']);
        $gameState = is_array($game->state) ? $game->state : [];

        if (!empty($gameState['result'])) {
            throw ValidationException::withMessages([
                'move' => 'Game is already finished.',
            ]);
        }

        if ($queue->side !== $this->sideToMove($gameState)) {
            throw ValidationException::withMessages([
                'move' => 'Not your turn.',
            ]);
        }

        $nextState = $gameService->applyMove($gameState, $validated['move']);
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

        broadcast(new QuickMatchMove($game->id, $nextState));

        return response()->json($payload);
    }

    private function stripPossibleMoves(array $state): array
    {
        if (isset($state['board']) && is_array($state['board'])) {
            unset($state['board']['possibleMoves']);
        }

        return $state;
    }

    private function sideToMove(array $state): string
    {
        $lastMove = $state['lastMove'] ?? null;
        $lastColor = $lastMove['piece']['color'] ?? null;

        if ($lastColor === 'white') {
            return 'black';
        }

        return 'white';
    }
}
