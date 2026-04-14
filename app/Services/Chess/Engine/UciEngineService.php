<?php

namespace App\Services\Chess\Engine;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UciEngineService
{
    private string $baseUrl = 'http://localhost:3000';

    /**
     * Get the best move from the engine for a given game state.
     *
     * @param array $moves An array of moves in algebraic notation (e.g. ['e2e4', 'e7e5'])
     * @param int $movetime The time in milliseconds for the engine to think.
     * @return string|null The best move in algebraic notation (e.g. 'g1f3') or null on failure.
     */
    public function getBestMove(array $moves, int $movetime = 1000): ?string
    {
        $movesString = implode(' ', $moves);
        $positionCommand = $movesString ? "position startpos moves {$movesString}" : "position startpos";

        $commands = [
            "uci",
            "isready",
            $positionCommand,
            "go movetime {$movetime}"
        ];

        $lastOutput = '';

        try {
            foreach ($commands as $command) {
                Log::debug('Sending command to UCI Engine', ['command' => $command]);
                $response = Http::post("{$this->baseUrl}/uci", [
                    'command' => $command
                ]);

                if (!$response->successful()) {
                    Log::error('UCI Engine returned error status', [
                        'command' => $command,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return null;
                }

                $lastOutput = $response->body();
                Log::debug('UCI Engine response', [
                    'command' => $command,
                    'output' => $lastOutput
                ]);
            }

            $bestMove = $this->parseBestMove($lastOutput);
            Log::debug('Parsed best move', ['bestMove' => $bestMove]);
            return $bestMove;
        } catch (\Exception $e) {
            Log::error('Failed to communicate with UCI Engine', [
                'exception' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Parse the 'bestmove' from the engine output.
     *
     * @param string $output
     * @return string|null
     */
    private function parseBestMove(string $output): ?string
    {
        $data = json_decode($output, true);
        $responses = $data['responses'] ?? [];

        foreach (array_reverse($responses) as $line) {
            $line = trim($line);
            if (str_starts_with($line, 'bestmove')) {
                $parts = explode(' ', $line);
                return $parts[1] ?? null;
            }
        }
        return null;
    }
}
