import { defineStore } from 'pinia';

export const useGameStateStore = defineStore('gameState', {
    state: () => ({
        settings: {
            side: 'random',
            timeControl: '10+5',
            variant: 'standard',
        },
        board: {
            files: [],
            ranks: [],
            squares: [],
        },
        lastMove: null,
        highlightSquares: [],
        selectedPiece: null,
        capturedByBlack: [],
        capturedByWhite: [],
        moves: [],
    }),
    actions: {
        setGameState(gameState) {
            this.settings = gameState.settings;
            this.board = gameState.board;
            this.lastMove = gameState.lastMove;
            this.capturedByBlack = gameState.capturedByBlack;
            this.capturedByWhite = gameState.capturedByWhite;
            this.moves = gameState.moves;
        },
        selectPiece(piece) {
            this.selectedPiece = piece;
        }
    },
});
