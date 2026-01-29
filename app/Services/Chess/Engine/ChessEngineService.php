<?php

namespace App\Services\Chess\Engine;

/**
 * Core Chess Engine Service
 * Includes Heuristic Evaluation with Endgame Tapering.
 */
class ChessEngineService
{
    // Piece Types
    private const PAWN = 'p';
    private const KNIGHT = 'n';
    private const BISHOP = 'b';
    private const ROOK = 'r';
    private const QUEEN = 'q';
    private const KING = 'k';

    private const WHITE = 'white';
    private const BLACK = 'black';

    private const FILES = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];

    private const BACK_RANK_TYPES = [
        self::ROOK, self::KNIGHT, self::BISHOP, self::QUEEN,
        self::KING, self::BISHOP, self::KNIGHT, self::ROOK
    ];

    private const CASTLING_CONFIG = [
        self::WHITE => [
            'kingSide'  => ['kTo' => 6, 'rFrom' => 7, 'rTo' => 5, 'empty' => [5, 6], 'safe' => [4, 5, 6]],
            'queenSide' => ['kTo' => 2, 'rFrom' => 0, 'rTo' => 3, 'empty' => [1, 2, 3], 'safe' => [4, 3, 2]],
        ],
        self::BLACK => [
            'kingSide'  => ['kTo' => 62, 'rFrom' => 63, 'rTo' => 61, 'empty' => [61, 62], 'safe' => [60, 61, 62]],
            'queenSide' => ['kTo' => 58, 'rFrom' => 56, 'rTo' => 59, 'empty' => [57, 58, 59], 'safe' => [60, 59, 58]],
        ],
    ];

    // --- EVALUATION CONSTANTS ---

    private const PIECE_VALUES = [
        self::PAWN   => 100,
        self::KNIGHT => 320,
        self::BISHOP => 330,
        self::ROOK   => 500,
        self::QUEEN  => 900,
        self::KING   => 20000
    ];

    /**
     * Piece-Square Tables (PST)
     * Tables are defined from Black's perspective. White uses flipped indices.
     */
    private const PST = [
        self::PAWN => [
            0,  0,  0,  0,  0,  0,  0,  0,
            50, 50, 50, 50, 50, 50, 50, 50,
            10, 10, 20, 30, 30, 20, 10, 10,
            5,  5, 10, 25, 25, 10,  5,  5,
            0,  0,  0, 20, 20,  0,  0,  0,
            5, -5,-10,  0,  0,-10, -5,  5,
            5, 10, 10,-20,-20, 10, 10,  5,
            0,  0,  0,  0,  0,  0,  0,  0
        ],
        self::KNIGHT => [
            -50,-40,-30,-30,-30,-30,-40,-50,
            -40,-20,  0,  0,  0,  0,-20,-40,
            -30,  0, 10, 15, 15, 10,  0,-30,
            -30,  5, 15, 20, 20, 15,  5,-30,
            -30,  0, 15, 20, 20, 15,  0,-30,
            -30,  5, 10, 15, 15, 10,  5,-30,
            -40,-20,  0,  5,  5,  0,-20,-40,
            -50,-40,-30,-30,-30,-30,-40,-50
        ],
        self::BISHOP => [
            -20,-10,-10,-10,-10,-10,-10,-20,
            -10,  0,  0,  0,  0,  0,  0,-10,
            -10,  0,  5, 10, 10,  5,  0,-10,
            -10,  5,  5, 10, 10,  5,  5,-10,
            -10,  0, 10, 10, 10, 10,  0,-10,
            -10, 10, 10, 10, 10, 10, 10,-10,
            -10,  5,  0,  0,  0,  0,  5,-10,
            -20,-10,-10,-10,-10,-10,-10,-20
        ],
        self::ROOK => [
            0,  0,  0,  0,  0,  0,  0,  0,
            5, 10, 10, 10, 10, 10, 10,  5,
            -5,  0,  0,  0,  0,  0,  0, -5,
            -5,  0,  0,  0,  0,  0,  0, -5,
            -5,  0,  0,  0,  0,  0,  0, -5,
            -5,  0,  0,  0,  0,  0,  0, -5,
            -5,  0,  0,  0,  0,  0,  0, -5,
            0,  0,  0,  5,  5,  0,  0,  0
        ],
        self::QUEEN => [
            -20,-10,-10, -5, -5,-10,-10,-20,
            -10,  0,  0,  0,  0,  0,  0,-10,
            -10,  0,  5,  5,  5,  5,  0,-10,
            -5,  0,  5,  5,  5,  5,  0, -5,
            0,  0,  5,  5,  5,  5,  0, -5,
            -10,  5,  5,  5,  5,  5,  0,-10,
            -10,  0,  5,  0,  0,  0,  0,-10,
            -20,-10,-10, -5, -5,-10,-10,-20
        ],
        self::KING => [
            -30,-40,-40,-50,-50,-40,-40,-30,
            -30,-40,-40,-50,-50,-40,-40,-30,
            -30,-40,-40,-50,-50,-40,-40,-30,
            -30,-40,-40,-50,-50,-40,-40,-30,
            -20,-30,-30,-40,-40,-30,-30,-20,
            -10,-20,-20,-20,-20,-20,-20,-10,
            20, 20,  0,  0,  0,  0, 20, 20,
            20, 30, 10,  0,  0, 10, 30, 20
        ]
    ];

    private const PST_KING_ENDGAME = [
        -50,-40,-30,-20,-20,-30,-40,-50,
        -30,-20,-10,  0,  0,-10,-20,-30,
        -30,-10, 20, 30, 30, 20,-10,-30,
        -30,-10, 30, 40, 40, 30,-10,-30,
        -30,-10, 30, 40, 40, 30,-10,-30,
        -30,-10, 20, 30, 30, 20,-10,-30,
        -30,-30,  0,  0,  0,  0,-30,-30,
        -50,-30,-30,-30,-30,-30,-30,-50
    ];

    // --- COORDINATE UTILITIES ---

    private function sqToIndex(string $sq): int
    {
        $f = array_search($sq[0], self::FILES);
        $r = (int)$sq[1] - 1;
        return $r * 8 + $f;
    }

    private function indexToSq(int $idx): string
    {
        return self::FILES[$idx % 8] . (floor($idx / 8) + 1);
    }

    // --- GAME STATE ---

    public function createGameState(array $settings = []): array
    {
        $pieces = $this->buildInitialPieces();
        $castling = [
            self::WHITE => ['kingSide' => true, 'queenSide' => true],
            self::BLACK => ['kingSide' => true, 'queenSide' => true]
        ];

        return [
            'board' => [
                'pieces' => $pieces,
                'possibleMoves' => $this->calculateLegalMoves($pieces, null, $castling),
                'evaluation' => $this->evaluateBoard($pieces),
            ],
            'castling' => $castling,
            'lastMove' => null,
            'moves' => [],
            'result' => null,
        ];
    }

    public function applyMove(array $gameState, array $move): array
    {
        $pieces = $gameState['board']['pieces'];
        $lastMove = $gameState['lastMove'];
        $castling = $gameState['castling'];

        $legalMoves = $this->calculateLegalMoves($pieces, $lastMove, $castling);

        $matched = null;
        foreach ($legalMoves as $m) {
            if ($m['from'] === $move['from'] && $m['to'] === $move['to']) {
                if (isset($move['promotion']) && ($m['promotion'] ?? '') !== $move['promotion']) continue;
                $matched = $m; break;
            }
        }

        if (!$matched) return ['error' => 'Illegal move.'];

        $movingPiece = $pieces[$matched['from']];
        $capturedPiece = null;
        $captureSq = null;

        if (!empty($matched['enPassant'])) {
            $capIdx = $this->sqToIndex($matched['to']) + ($movingPiece['color'] === self::WHITE ? -8 : 8);
            $captureSq = $this->indexToSq($capIdx);
            $capturedPiece = $pieces[$captureSq];
            unset($pieces[$captureSq]);
        }
        elseif (isset($pieces[$matched['to']])) {
            $captureSq = $matched['to'];
            $capturedPiece = $pieces[$captureSq];
        }

        unset($pieces[$matched['from']]);
        $finalPiece = $movingPiece;
        if (!empty($matched['promotion'])) $finalPiece['type'] = $matched['promotion'];
        $pieces[$matched['to']] = $finalPiece;

        if (!empty($matched['castle'])) {
            $conf = self::CASTLING_CONFIG[$movingPiece['color']][$matched['castle']];
            $rook = $pieces[$this->indexToSq($conf['rFrom'])];
            unset($pieces[$this->indexToSq($conf['rFrom'])]);
            $pieces[$this->indexToSq($conf['rTo'])] = $rook;
        }

        $newCastling = $this->updateCastlingRights($castling, $movingPiece, $matched['from'], $capturedPiece, $captureSq);
        $newLastMove = array_merge($matched, ['piece' => $movingPiece]);

        $nextMoves = $this->calculateLegalMoves($pieces, $newLastMove, $newCastling);
        $result = $this->determineResult($pieces, $newLastMove, $nextMoves);

        return [
            'board' => [
                'pieces' => $pieces,
                'possibleMoves' => $nextMoves,
                'evaluation' => $this->evaluateBoard($pieces)
            ],
            'lastMove' => $newLastMove,
            'castling' => $newCastling,
            'result' => $result,
            'moves' => array_merge($gameState['moves'], [$matched])
        ];
    }

    // --- HEURISTIC EVALUATION ---

    public function evaluateBoard(array $pieces): int
    {
        $totalEval = 0;
        $phase = $this->getGamePhase($pieces);

        foreach ($pieces as $sq => $piece) {
            $idx = $this->sqToIndex($sq);
            $type = $piece['type'];
            $isWhite = $piece['color'] === self::WHITE;

            $val = self::PIECE_VALUES[$type];
            $pstIdx = $isWhite ? ((7 - (int)floor($idx / 8)) * 8 + ($idx % 8)) : $idx;

            if ($type === self::KING) {
                // Blend Opening/Middlegame PST with Endgame PST
                $mg = self::PST[self::KING][$pstIdx];
                $eg = self::PST_KING_ENDGAME[$pstIdx];
                $positionalVal = (($mg * (256 - $phase)) + ($eg * $phase)) / 256;
            } else {
                $positionalVal = self::PST[$type][$pstIdx] ?? 0;
            }

            if ($isWhite) {
                $totalEval += ($val + $positionalVal);
            } else {
                $totalEval -= ($val + $positionalVal);
            }
        }
        return (int)$totalEval;
    }

    private function getGamePhase(array $pieces): int
    {
        // Define total major material for a full board
        $midgameLimit = 24000;
        $currentMaterial = 0;

        foreach ($pieces as $p) {
            if ($p['type'] !== self::KING && $p['type'] !== self::PAWN) {
                $currentMaterial += self::PIECE_VALUES[$p['type']];
            }
        }

        $phase = 256 - ($currentMaterial * 256 / $midgameLimit);
        return max(0, min(256, (int)$phase));
    }

    // --- LOGIC ENGINE ---

    private function calculateLegalMoves(array $pieces, ?array $lastMove, array $castling): array
    {
        $side = $this->getSideToMove($lastMove);
        $pseudo = $this->generatePseudoMoves($pieces, $lastMove, $castling, $side);
        $legal = [];

        foreach ($pseudo as $move) {
            if ($this->isMoveLegal($pieces, $move, $side)) $legal[] = $move;
        }
        return $legal;
    }

    private function isMoveLegal(array $pieces, array $move, string $side): bool
    {
        $temp = $pieces;
        $piece = $temp[$move['from']];

        if (!empty($move['enPassant'])) {
            $capIdx = $this->sqToIndex($move['to']) + ($side === self::WHITE ? -8 : 8);
            unset($temp[$this->indexToSq($capIdx)]);
        }

        unset($temp[$move['from']]);
        $temp[$move['to']] = $piece;

        $kingSq = ($piece['type'] === self::KING) ? $move['to'] : $this->findKing($temp, $side);
        $opponent = ($side === self::WHITE) ? self::BLACK : self::WHITE;

        return $kingSq && !$this->isSquareAttacked($temp, $kingSq, $opponent);
    }

    public function isSquareAttacked(array $pieces, string $targetSq, string $attackerColor): bool
    {
        $idx = $this->sqToIndex($targetSq);
        $f = $idx % 8; $r = (int)floor($idx / 8);

        foreach ([[1,2],[2,1],[-1,2],[-2,1],[1,-2],[2,-1],[-1,-2],[-2,-1]] as [$df, $dr]) {
            $nf = $f + $df; $nr = $r + $dr;
            if ($nf >= 0 && $nf < 8 && $nr >= 0 && $nr < 8) {
                $p = $pieces[$this->indexToSq($nr * 8 + $nf)] ?? null;
                if ($p && $p['type'] === self::KNIGHT && $p['color'] === $attackerColor) return true;
            }
        }

        $dirs = [[0,1,'s'],[0,-1,'s'],[1,0,'s'],[-1,0,'s'],[1,1,'d'],[1,-1,'d'],[-1,1,'d'],[-1,-1,'d']];
        foreach ($dirs as [$df, $dr, $type]) {
            for ($i = 1; $i < 8; $i++) {
                $nf = $f + ($df * $i); $nr = $r + ($dr * $i);
                if ($nf < 0 || $nf > 7 || $nr < 0 || $nr > 7) break;
                $p = $pieces[$this->indexToSq($nr * 8 + $nf)] ?? null;
                if ($p) {
                    if ($p['color'] === $attackerColor) {
                        if ($i === 1 && $p['type'] === self::KING) return true;
                        if ($type === 's' && ($p['type'] === self::ROOK || $p['type'] === self::QUEEN)) return true;
                        if ($type === 'd' && ($p['type'] === self::BISHOP || $p['type'] === self::QUEEN)) return true;
                    }
                    break;
                }
            }
        }

        $pDir = ($attackerColor === self::WHITE) ? -1 : 1;
        foreach ([-1, 1] as $df) {
            $nf = $f + $df; $nr = $r + $pDir;
            if ($nf >= 0 && $nf < 8 && $nr >= 0 && $nr < 8) {
                $p = $pieces[$this->indexToSq($nr * 8 + $nf)] ?? null;
                if ($p && $p['type'] === self::PAWN && $p['color'] === $attackerColor) return true;
            }
        }
        return false;
    }

    private function determineResult(array $pieces, ?array $lastMove, array $possibleMoves): ?array
    {
        if (!empty($possibleMoves)) return null;

        $side = $this->getSideToMove($lastMove);
        $opponent = ($side === self::WHITE) ? self::BLACK : self::WHITE;
        $kingSq = $this->findKing($pieces, $side);

        if ($kingSq && $this->isSquareAttacked($pieces, $kingSq, $opponent)) {
            return ['winner' => $opponent, 'reason' => 'checkmate'];
        }
        return ['winner' => 'draw', 'reason' => 'stalemate'];
    }

    // --- MOVE GENERATORS ---

    private function generatePseudoMoves(array $pieces, ?array $lastMove, array $castling, string $side): array
    {
        $moves = [];
        foreach ($pieces as $sq => $piece) {
            if ($piece['color'] !== $side) continue;
            $idx = $this->sqToIndex($sq);
            $f = $idx % 8; $r = (int)floor($idx / 8);

            switch ($piece['type']) {
                case self::PAWN:   $this->genPawn($moves, $pieces, $sq, $f, $r, $side, $lastMove); break;
                case self::KNIGHT: $this->genLeap($moves, $pieces, $sq, $f, $r, [[1,2],[2,1],[-1,2],[-2,1],[1,-2],[2,-1],[-1,-2],[-2,-1]]); break;
                case self::KING:
                    $this->genLeap($moves, $pieces, $sq, $f, $r, [[1,0],[-1,0],[0,1],[0,-1],[1,1],[1,-1],[-1,1],[-1,-1]]);
                    $this->genCastle($moves, $pieces, $sq, $side, $castling);
                    break;
                case self::BISHOP: $this->genSlide($moves, $pieces, $sq, $f, $r, [[1,1],[1,-1],[-1,1],[-1,-1]]); break;
                case self::ROOK:   $this->genSlide($moves, $pieces, $sq, $f, $r, [[1,0],[-1,0],[0,1],[0,-1]]); break;
                case self::QUEEN:  $this->genSlide($moves, $pieces, $sq, $f, $r, [[1,1],[1,-1],[-1,1],[-1,-1],[1,0],[-1,0],[0,1],[0,-1]]); break;
            }
        }
        return $moves;
    }

    private function genSlide(&$moves, $pieces, $from, $f, $r, $dirs): void
    {
        $color = $pieces[$from]['color'];
        foreach ($dirs as [$df, $dr]) {
            for ($i = 1; $i < 8; $i++) {
                $nf = $f + ($df * $i); $nr = $r + ($dr * $i);
                if ($nf < 0 || $nf > 7 || $nr < 0 || $nr > 7) break;
                $target = $this->indexToSq($nr * 8 + $nf);
                if (isset($pieces[$target])) {
                    if ($pieces[$target]['color'] !== $color) $moves[] = ['from' => $from, 'to' => $target, 'capture' => true];
                    break;
                }
                $moves[] = ['from' => $from, 'to' => $target, 'capture' => false];
            }
        }
    }

    private function genLeap(&$moves, $pieces, $from, $f, $r, $offsets): void
    {
        $color = $pieces[$from]['color'];
        foreach ($offsets as [$df, $dr]) {
            $nf = $f + $df; $nr = $r + $dr;
            if ($nf >= 0 && $nf < 8 && $nr >= 0 && $nr < 8) {
                $target = $this->indexToSq($nr * 8 + $nf);
                if (!isset($pieces[$target])) $moves[] = ['from' => $from, 'to' => $target, 'capture' => false];
                elseif ($pieces[$target]['color'] !== $color) $moves[] = ['from' => $from, 'to' => $target, 'capture' => true];
            }
        }
    }

    private function genPawn(&$moves, $pieces, $sq, $f, $r, $side, $lastMove): void
    {
        $dir = ($side === self::WHITE) ? 1 : -1;
        $promoR = ($side === self::WHITE) ? 7 : 0;

        $t1 = $this->indexToSq(($r + $dir) * 8 + $f);
        if (!isset($pieces[$t1])) {
            $this->addPawnMove($moves, $sq, $t1, false, ($r + $dir) === $promoR);
            if (($side === self::WHITE && $r === 1) || ($side === self::BLACK && $r === 6)) {
                $t2 = $this->indexToSq(($r + 2 * $dir) * 8 + $f);
                if (!isset($pieces[$t2])) $moves[] = ['from' => $sq, 'to' => $t2, 'capture' => false];
            }
        }

        foreach ([-1, 1] as $df) {
            $nf = $f + $df; $nr = $r + $dir;
            if ($nf < 0 || $nf > 7) continue;
            $target = $this->indexToSq($nr * 8 + $nf);
            if (isset($pieces[$target]) && $pieces[$target]['color'] !== $side) {
                $this->addPawnMove($moves, $sq, $target, true, $nr === $promoR);
            }
            if ($lastMove && $lastMove['piece']['type'] === self::PAWN) {
                $lFrom = $this->sqToIndex($lastMove['from']); $lTo = $this->sqToIndex($lastMove['to']);
                if (abs((int)floor($lFrom / 8) - (int)floor($lTo / 8)) === 2 && $lTo === ($r * 8 + $nf)) {
                    $moves[] = ['from' => $sq, 'to' => $target, 'capture' => true, 'enPassant' => true];
                }
            }
        }
    }

    private function addPawnMove(&$moves, $from, $to, $cap, $isPromo): void
    {
        if ($isPromo) {
            foreach ([self::QUEEN, self::ROOK, self::BISHOP, self::KNIGHT] as $t) {
                $moves[] = ['from' => $from, 'to' => $to, 'capture' => $cap, 'promotion' => $t];
            }
        } else $moves[] = ['from' => $from, 'to' => $to, 'capture' => $cap];
    }

    private function genCastle(&$moves, $pieces, $sq, $side, $rights): void
    {
        $opp = ($side === self::WHITE) ? self::BLACK : self::WHITE;
        foreach (['kingSide', 'queenSide'] as $type) {
            if (!($rights[$side][$type] ?? false)) continue;
            $conf = self::CASTLING_CONFIG[$side][$type];
            foreach ($conf['empty'] as $e) if (isset($pieces[$this->indexToSq($e)])) continue 2;
            foreach ($conf['safe'] as $s) if ($this->isSquareAttacked($pieces, $this->indexToSq($s), $opp)) continue 2;
            $moves[] = ['from' => $sq, 'to' => $this->indexToSq($conf['kTo']), 'capture' => false, 'castle' => $type];
        }
    }

    // --- UTILS ---

    private function updateCastlingRights(array $rights, array $piece, string $from, ?array $cap, ?string $capSq): array
    {
        $side = $piece['color'];
        if ($piece['type'] === self::KING) $rights[$side] = ['kingSide' => false, 'queenSide' => false];
        elseif ($piece['type'] === self::ROOK) {
            if ($from === $this->indexToSq(self::CASTLING_CONFIG[$side]['kingSide']['rFrom'])) $rights[$side]['kingSide'] = false;
            if ($from === $this->indexToSq(self::CASTLING_CONFIG[$side]['queenSide']['rFrom'])) $rights[$side]['queenSide'] = false;
        }
        if ($cap && $cap['type'] === self::ROOK) {
            $os = $cap['color'];
            if ($capSq === $this->indexToSq(self::CASTLING_CONFIG[$os]['kingSide']['rFrom'])) $rights[$os]['kingSide'] = false;
            if ($capSq === $this->indexToSq(self::CASTLING_CONFIG[$os]['queenSide']['rFrom'])) $rights[$os]['queenSide'] = false;
        }
        return $rights;
    }

    private function getSideToMove(?array $lastMove): string
    {
        return (!$lastMove || $lastMove['piece']['color'] === self::BLACK) ? self::WHITE : self::BLACK;
    }

    private function findKing(array $pieces, string $color): ?string
    {
        foreach ($pieces as $sq => $p) if ($p['type'] === self::KING && $p['color'] === $color) return $sq;
        return null;
    }

    private function buildInitialPieces(): array
    {
        $p = [];
        for ($i = 0; $i < 8; $i++) {
            $p[self::FILES[$i].'1'] = ['type' => self::BACK_RANK_TYPES[$i], 'color' => self::WHITE];
            $p[self::FILES[$i].'2'] = ['type' => self::PAWN, 'color' => self::WHITE];
            $p[self::FILES[$i].'7'] = ['type' => self::PAWN, 'color' => self::BLACK];
            $p[self::FILES[$i].'8'] = ['type' => self::BACK_RANK_TYPES[$i], 'color' => self::BLACK];
        }
        return $p;
    }
}
