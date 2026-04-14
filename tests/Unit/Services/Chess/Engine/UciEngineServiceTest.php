<?php

namespace Tests\Unit\Services\Chess\Engine;

use App\Services\Chess\Engine\UciEngineService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UciEngineServiceTest extends TestCase
{
    public function test_get_best_move_returns_move_on_success()
    {
        Http::fake([
            'http://localhost:3000/uci' => Http::sequence()
                ->push(['responses' => ['id name Stockfish', 'uciok']], 200) // uci
                ->push(['responses' => ['readyok']], 200) // isready
                ->push(['responses' => []], 200) // position
                ->push(['responses' => [
                    'id name Stockfish',
                    'id author the Stockfish developers',
                    'option name Debug Log File type string default ',
                    'bestmove e2e4 ponder e7e5'
                ]], 200), // go
        ]);

        $service = new UciEngineService();
        $move = $service->getBestMove(['e2e4', 'e7e5'], 1000);

        $this->assertEquals('e2e4', $move);

        Http::assertSentCount(4);
    }

    public function test_get_best_move_returns_null_on_failure()
    {
        Http::fake([
            'http://localhost:3000/uci' => Http::response(['error' => 'Internal Server Error'], 500),
        ]);

        $service = new UciEngineService();
        $move = $service->getBestMove(['e2e4', 'e7e5'], 1000);

        $this->assertNull($move);
    }

    public function test_get_best_move_with_empty_moves()
    {
         Http::fake([
            'http://localhost:3000/uci' => Http::sequence()
                ->push(['responses' => ['uciok']], 200)
                ->push(['responses' => ['readyok']], 200)
                ->push(['responses' => []], 200)
                ->push(['responses' => ['bestmove d2d4']], 200),
        ]);

        $service = new UciEngineService();
        $move = $service->getBestMove([], 500);

        $this->assertEquals('d2d4', $move);
    }

    public function test_get_best_move_returns_null_when_parse_fails()
    {
        Http::fake([
            'http://localhost:3000/uci' => Http::sequence()
                ->push(['responses' => ['uciok']], 200)
                ->push(['responses' => ['readyok']], 200)
                ->push(['responses' => []], 200)
                ->push(['responses' => ["info depth 10", "info score cp 0"]], 200), // No bestmove line
        ]);

        $service = new UciEngineService();
        $move = $service->getBestMove(['e2e4'], 1000);

        $this->assertNull($move);
    }
}
