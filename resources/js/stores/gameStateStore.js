import { defineStore } from 'pinia';

const FILES = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
const RANKS = [8, 7, 6, 5, 4, 3, 2, 1];

const BOARD_LAYOUT = RANKS.flatMap((rank) =>
    FILES.map((file, fileIndex) => ({
        key: `${file}${rank}`,
        file,
        rank,
        isDark: (rank + fileIndex) % 2 === 1,
    })),
);

const buildPiecesFromSquares = (squares) => {
    const pieces = {};

    if (!Array.isArray(squares)) {
        return pieces;
    }

    squares.forEach((square) => {
        if (!square?.key) {
            return;
        }

        if (square.piece) {
            pieces[square.key] = square.piece;
        }
    });

    return pieces;
};

export const useGameStateStore = defineStore('gameState', {
    state: () => ({
        gameId: null,
        settings: {
            side: 'random',
            timeControl: '10+5',
            variant: 'standard',
        },
        board: {
            pieces: {},
            possibleMoves: [],
        },
        lastMove: null,
        castling: null,
        selectedSquare: null,
        capturedByBlack: [],
        capturedByWhite: [],
        moves: [],
        result: null,
    }),
    getters: {
        boardSquares(state) {
            const pieces = state.board.pieces || {};

            return BOARD_LAYOUT.map((square) => ({
                ...square,
                piece: pieces[square.key] ?? null,
            }));
        },
        moveIndex(state) {
            const index = {};
            const possibleMoves = Array.isArray(state.board.possibleMoves)
                ? state.board.possibleMoves
                : [];

            for (const move of possibleMoves) {
                if (!move?.from || !move?.to) {
                    continue;
                }

                if (!index[move.from]) {
                    index[move.from] = {};
                }

                index[move.from][move.to] = move;
            }

            return index;
        },
        highlightSquares() {
            if (!this.selectedSquare) {
                return [];
            }

            const movesFrom = this.moveIndex[this.selectedSquare];
            return movesFrom ? Object.keys(movesFrom) : [];
        },
    },
    actions: {
        setGameState(gameState, gameId = null) {
            if (gameId !== null && gameId !== undefined) {
                this.gameId = gameId;
            }

            this.settings = gameState?.settings ?? this.settings;

            const board = gameState?.board ?? {};
            let pieces = {};

            if (
                board?.pieces &&
                typeof board.pieces === 'object' &&
                !Array.isArray(board.pieces)
            ) {
                pieces = board.pieces;
            } else if (Array.isArray(board?.squares)) {
                pieces = buildPiecesFromSquares(board.squares);
            }

            this.board = {
                pieces,
                possibleMoves: Array.isArray(board?.possibleMoves)
                    ? board.possibleMoves
                    : [],
            };

            this.lastMove = gameState?.lastMove ?? null;
            this.castling = gameState?.castling ?? this.castling;
            this.capturedByBlack = Array.isArray(gameState?.capturedByBlack)
                ? gameState.capturedByBlack
                : [];
            this.capturedByWhite = Array.isArray(gameState?.capturedByWhite)
                ? gameState.capturedByWhite
                : [];
            this.moves = Array.isArray(gameState?.moves) ? gameState.moves : [];
            this.result = gameState?.result ?? null;
        },
        selectSquare(squareKey) {
            this.selectedSquare = squareKey;
        },
        clearSelection() {
            this.selectedSquare = null;
        },
        getMove(from, to) {
            return this.moveIndex?.[from]?.[to] ?? null;
        },
    },
});
