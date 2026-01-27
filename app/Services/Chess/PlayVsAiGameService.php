<?php

namespace App\Services\Chess;

class PlayVsAiGameService
{
    public function createGameState(array $settings = []): array
    {
        $files = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        $ranks = [8, 7, 6, 5, 4, 3, 2, 1];
        $backRank = [
            'rook',
            'knight',
            'bishop',
            'queen',
            'king',
            'bishop',
            'knight',
            'rook',
        ];

        $pieces = [];

        foreach ($files as $index => $file) {
            $pieces[] = $this->makePiece($backRank[$index], 'white', "{$file}1");
        }

        foreach ($files as $file) {
            $pieces[] = $this->makePiece('pawn', 'white', "{$file}2");
        }

        foreach ($files as $file) {
            $pieces[] = $this->makePiece('pawn', 'black', "{$file}7");
        }

        foreach ($files as $index => $file) {
            $pieces[] = $this->makePiece($backRank[$index], 'black', "{$file}8");
        }

        $piecesBySquare = [];
        foreach ($pieces as $piece) {
            $piecesBySquare[$piece['square']] = $piece;
        }

        $squares = [];
        foreach ($ranks as $rank) {
            foreach ($files as $fileIndex => $file) {
                $key = "{$file}{$rank}";
                $squares[] = [
                    'key' => $key,
                    'file' => $file,
                    'rank' => $rank,
                    'isDark' => ($rank + $fileIndex) % 2 === 1,
                    'piece' => $piecesBySquare[$key] ?? null,
                ];
            }
        }

        return [
            'settings' => [
                'side' => $settings['side'] ?? 'random',
                'timeControl' => $settings['timeControl'] ?? '10+5',
                'variant' => $settings['variant'] ?? 'standard',
            ],
            'board' => [
                'files' => $files,
                'ranks' => $ranks,
                'squares' => $squares,
            ],
            'lastMove' => null,
            'capturedByBlack' => [],
            'capturedByWhite' => [
                [],
            ],
            'moves' => [

            ],
        ];
    }

    private function makePiece(string $type, string $color, string $square): array
    {
        return [
            'id' => "{$color}-{$type}-{$square}",
            'type' => $type,
            'color' => $color,
            'square' => $square,
        ];
    }
}
