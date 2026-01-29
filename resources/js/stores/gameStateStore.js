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

const BACK_RANK_TYPES = ['r', 'n', 'b', 'q', 'k', 'b', 'n', 'r'];
const CASTLING_ROOK_MOVES = {
    white: {
        kingSide: { from: 'h1', to: 'f1' },
        queenSide: { from: 'a1', to: 'd1' },
    },
    black: {
        kingSide: { from: 'h8', to: 'f8' },
        queenSide: { from: 'a8', to: 'd8' },
    },
};

const buildInitialPieces = () => {
    const pieces = {};
    FILES.forEach((file, index) => {
        pieces[`${file}1`] = { type: BACK_RANK_TYPES[index], color: 'white' };
        pieces[`${file}2`] = { type: 'p', color: 'white' };
        pieces[`${file}7`] = { type: 'p', color: 'black' };
        pieces[`${file}8`] = { type: BACK_RANK_TYPES[index], color: 'black' };
    });
    return pieces;
};

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

const applyMoveToPieces = (pieces, move) => {
    if (!move?.from || !move?.to) {
        return null;
    }

    const movingPiece = pieces[move.from];
    if (!movingPiece) {
        return null;
    }

    let capturedPiece = null;
    if (move.enPassant) {
        const targetFile = move.to[0];
        const targetRank = Number.parseInt(move.to[1], 10);
        if (!Number.isNaN(targetRank)) {
            const captureRank =
                targetRank + (movingPiece.color === 'white' ? -1 : 1);
            const captureSquare = `${targetFile}${captureRank}`;
            if (pieces[captureSquare]) {
                capturedPiece = pieces[captureSquare];
                delete pieces[captureSquare];
            }
        }
    } else if (pieces[move.to]) {
        capturedPiece = pieces[move.to];
    }

    delete pieces[move.from];
    const finalPiece = { ...movingPiece };
    if (move.promotion) {
        finalPiece.type = move.promotion;
    }
    pieces[move.to] = finalPiece;

    if (move.castle) {
        const rookMove =
            CASTLING_ROOK_MOVES[movingPiece.color]?.[move.castle] ?? null;
        if (rookMove && pieces[rookMove.from]) {
            const rook = pieces[rookMove.from];
            delete pieces[rookMove.from];
            pieces[rookMove.to] = rook;
        }
    }

    return capturedPiece;
};

const buildCapturedPieces = (moves) => {
    const capturedByWhite = [];
    const capturedByBlack = [];

    if (!Array.isArray(moves) || moves.length === 0) {
        return { capturedByWhite, capturedByBlack };
    }

    const pieces = buildInitialPieces();

    moves.forEach((move) => {
        const capturedPiece = applyMoveToPieces(pieces, move);
        if (!capturedPiece) {
            return;
        }

        if (capturedPiece.color === 'white') {
            capturedByBlack.push({ ...capturedPiece });
        } else {
            capturedByWhite.push({ ...capturedPiece });
        }
    });

    return { capturedByWhite, capturedByBlack };
};

const formatMove = (move) => {
    if (!move?.from || !move?.to) {
        return '--';
    }

    if (move.castle) {
        return move.castle === 'queenSide' ? 'O-O-O' : 'O-O';
    }

    const captureMarker = move.capture ? 'x' : '-';
    const promotion = move.promotion
        ? `=${String(move.promotion).toUpperCase()}`
        : '';
    const suffix = move.enPassant ? ' ep' : '';

    return `${move.from}${captureMarker}${move.to}${promotion}${suffix}`;
};

const buildMoveList = (moves) => {
    if (!Array.isArray(moves) || moves.length === 0) {
        return [];
    }

    const rows = [];
    for (let index = 0; index < moves.length; index += 2) {
        rows.push({
            move: Math.floor(index / 2) + 1,
            white: formatMove(moves[index]),
            black: moves[index + 1] ? formatMove(moves[index + 1]) : '...',
        });
    }
    return rows;
};

export const useGameStateStore = defineStore('gameState', {
    state: () => ({
        gameId: null,
        settings: {
            side: 'random',
            timeControl: '10+5',
            variant: 'standard',
        },
        clock: {
            white: 0,
            black: 0,
            increment: 0,
            active: 'white',
            lastTickAt: null,
        },
        board: {
            pieces: {},
            possibleMoves: [],
            evaluation: 0,
        },
        lastMove: null,
        castling: null,
        selectedSquare: null,
        capturedByBlack: [],
        capturedByWhite: [],
        moves: [],
        result: null,
        drawOffer: null,
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
        moveList(state) {
            return buildMoveList(state.moves);
        },
        capturedPieces(state) {
            const computedCaptures = buildCapturedPieces(state.moves);

            return {
                capturedByWhite:
                    Array.isArray(state.capturedByWhite) &&
                    state.capturedByWhite.length > 0
                        ? state.capturedByWhite
                        : computedCaptures.capturedByWhite,
                capturedByBlack:
                    Array.isArray(state.capturedByBlack) &&
                    state.capturedByBlack.length > 0
                        ? state.capturedByBlack
                        : computedCaptures.capturedByBlack,
            };
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
                evaluation: Number.isFinite(board?.evaluation)
                    ? board.evaluation
                    : 0,
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
            this.drawOffer = gameState?.drawOffer ?? null;
            if (
                gameState?.clock &&
                typeof gameState.clock === 'object' &&
                !Array.isArray(gameState.clock)
            ) {
                this.clock = gameState.clock;
            }
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
