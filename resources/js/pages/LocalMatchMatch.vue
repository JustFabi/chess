<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { storeToRefs } from 'pinia';
import { computed, ref, watch } from 'vue';
import ChessMatchBoardCard from '@/components/chess/ChessMatchBoardCard.vue';
import ChessMatchDrawOfferModal from '@/components/chess/ChessMatchDrawOfferModal.vue';
import ChessMatchResultModal from '@/components/chess/ChessMatchResultModal.vue';
import ChessMatchSidebar from '@/components/chess/ChessMatchSidebar.vue';
import ChessPieceSprite from '@/components/chess/ChessPieceSprite.vue';
import { useChessClock } from '@/composables/useChessClock';
import { formatTimeControlTitle } from '@/lib/timeControls';
import { useGameStateStore } from '../stores/gameStateStore';

const props = defineProps({
    gameState: {
        type: Object,
        required: true,
    },
    gameId: {
        type: [Number, String],
        required: true,
    },
});

const gameStateStore = useGameStateStore();

watch(
    () => [props.gameState, props.gameId],
    ([nextState, nextGameId]) => {
        if (nextState) {
            gameStateStore.setGameState(nextState, nextGameId);
        }
    },
    { immediate: true },
);

const {
    board,
    boardSquares,
    capturedPieces,
    clock,
    settings,
    moves,
    moveList,
    lastMove,
    highlightSquares,
    selectedSquare,
    gameId,
    result,
    drawOffer,
} = storeToRefs(gameStateStore);

const { whiteClock, blackClock } = useChessClock({
    clock,
    result,
    settings,
    lastMove,
});

const moveError = ref(null);
const actionError = ref(null);
const showResultModal = ref(false);
const showDrawOfferModal = ref(false);
const isBoardFlipped = ref(false);
const isActionLoading = ref(false);
const isSidebarCollapsed = ref(false);

const baseOrientation = computed(() =>
    settings.value?.side === 'black' ? 'black' : 'white',
);
const boardOrientation = computed(() =>
    isBoardFlipped.value
        ? baseOrientation.value === 'white'
            ? 'black'
            : 'white'
        : baseOrientation.value,
);
const playerSide = computed(() => baseOrientation.value);
const opponentSide = computed(() =>
    playerSide.value === 'white' ? 'black' : 'white',
);
const playerCaptured = computed(() =>
    playerSide.value === 'white'
        ? capturedPieces.value.capturedByWhite
        : capturedPieces.value.capturedByBlack,
);
const opponentCaptured = computed(() =>
    opponentSide.value === 'white'
        ? capturedPieces.value.capturedByWhite
        : capturedPieces.value.capturedByBlack,
);
const evaluation = computed(() => board.value?.evaluation ?? 0);
const isDrawOfferPending = computed(
    () => drawOffer.value?.status === 'pending',
);
const isDrawOfferFromMe = computed(
    () => drawOffer.value?.from === playerSide.value,
);
const shouldShowDrawOfferModal = computed(() => isDrawOfferPending.value);
const timeControlTitle = computed(() =>
    formatTimeControlTitle(settings.value?.timeControl),
);

const topPlayer = computed(() => ({
    badge: 'P2',
    name: 'Local player',
    subtitle: `${opponentSide.value} pieces`,
    side: opponentSide.value,
    clock:
        opponentSide.value === 'white' ? whiteClock.value : blackClock.value,
}));

const bottomPlayer = computed(() => ({
    badge: 'P1',
    name: 'Local player',
    subtitle: `${playerSide.value} pieces`,
    side: playerSide.value,
    clock: playerSide.value === 'white' ? whiteClock.value : blackClock.value,
}));
const matchStatusLabel = computed(() =>
    result.value ? 'Game over' : 'In progress',
);

watch(
    () => result.value,
    (nextResult) => {
        showResultModal.value = Boolean(nextResult);
    },
    { immediate: true },
);

watch(
    () => [shouldShowDrawOfferModal.value, result.value],
    ([shouldShow, nextResult]) => {
        if (nextResult) {
            showDrawOfferModal.value = false;
            return;
        }

        showDrawOfferModal.value = shouldShow;
    },
    { immediate: true },
);

const selectSquare = (square) => {
    moveError.value = null;
    const squareKey = square?.key;

    if (!squareKey) {
        return;
    }

    if (selectedSquare.value && highlightSquares.value.includes(squareKey)) {
        const nextMove = gameStateStore.getMove(selectedSquare.value, squareKey);
        if (nextMove) {
            move(nextMove);
            return;
        }
    }

    if (square?.piece) {
        gameStateStore.selectSquare(squareKey);
    } else {
        gameStateStore.clearSelection();
    }
};

const move = async (executedMove) => {
    if (!executedMove?.from || !executedMove?.to) {
        return;
    }

    moveError.value = null;
    if (!gameId.value) {
        moveError.value = 'Missing game id.';
        return;
    }

    try {
        const response = await axios.post(
            '/local-match/move',
            {
                gameId: gameId.value,
                move: {
                    from: executedMove.from,
                    to: executedMove.to,
                },
            },
            {
                headers: {
                    Accept: 'application/json',
                },
            },
        );

        const payload = response?.data ?? {};

        if (payload?.gameState) {
            gameStateStore.setGameState(payload.gameState, payload.gameId);
        }

        gameStateStore.clearSelection();
        moveError.value = null;
    } catch (error) {
        const errors = error?.response?.data?.errors ?? null;

        if (Array.isArray(errors?.move)) {
            moveError.value = errors.move.join(' ');
        } else {
            moveError.value =
                errors?.move ?? error?.response?.data?.message ?? 'Illegal move.';
        }
    }
};

const performAction = async (action, options = {}) => {
    if (!gameId.value || isActionLoading.value) {
        return false;
    }

    isActionLoading.value = true;
    if (!options.silent) {
        actionError.value = null;
    }

    try {
        const response = await axios.post(
            '/local-match/action',
            {
                gameId: gameId.value,
                action,
                side: playerSide.value,
            },
            {
                headers: {
                    Accept: 'application/json',
                },
            },
        );

        const payload = response?.data ?? {};
        if (payload?.gameState) {
            gameStateStore.setGameState(payload.gameState, payload.gameId);
        }

        gameStateStore.clearSelection();
        moveError.value = null;
        actionError.value = null;
        return true;
    } catch (error) {
        const errors = error?.response?.data?.errors ?? null;
        const message =
            (Array.isArray(errors?.action)
                ? errors.action.join(' ')
                : errors?.action) ??
            (Array.isArray(errors?.side) ? errors.side.join(' ') : errors?.side) ??
            error?.response?.data?.message ??
            error?.message ??
            'Unable to update game.';
        if (!options.silent) {
            actionError.value = message;
        }
        return false;
    } finally {
        isActionLoading.value = false;
    }
};

const offerDraw = () => performAction('offer-draw');
const acceptDraw = () => performAction('accept-draw');
const declineDraw = () => performAction('decline-draw');
const surrenderMatch = () => performAction('resign');
const restartMatch = () => performAction('restart');
const flipBoard = () => {
    isBoardFlipped.value = !isBoardFlipped.value;
};
const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const handleDrawOfferOpen = (nextOpen) => {
    if (!nextOpen && isDrawOfferPending.value) {
        declineDraw();
    }
    showDrawOfferModal.value = nextOpen;
};

const exitToLobby = async () => {
    if (!result.value) {
        await performAction('resign', { silent: true });
    }

    router.visit('/dashboard');
};
</script>

<template>
    <Head title="Local Match">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div
        class="relative min-h-screen overflow-hidden bg-[var(--paper)] font-['Manrope'] text-[color:var(--ink)]"
        style="
            --paper: #f6f4ef;
            --ink: #0d1117;
            --muted: #5b6370;
            --line: rgba(15, 23, 42, 0.12);
            --panel: rgba(255, 255, 255, 0.78);
            --accent: #0f766e;
            --accent-strong: #115e59;
            --accent-soft: #d6f1ec;
            --glow: rgba(15, 118, 110, 0.22);
            --glow-2: rgba(56, 189, 248, 0.18);
            --board-dark: #1b1f24;
            --board-light: #f3efe6;
        "
    >
        <div class="pointer-events-none absolute inset-0">
            <div
                class="absolute -top-32 left-[-12%] h-[520px] w-[520px] rounded-full opacity-70 blur-3xl"
                style="
                    background: radial-gradient(
                        circle,
                        var(--glow),
                        transparent 70%
                    );
                "
            ></div>
            <div
                class="absolute right-[-10%] bottom-[-220px] h-[560px] w-[560px] rounded-full opacity-70 blur-3xl"
                style="
                    background: radial-gradient(
                        circle,
                        var(--glow-2),
                        transparent 65%
                    );
                "
            ></div>
            <div
                class="absolute inset-0 opacity-[0.08]"
                style="
                    background-image:
                        linear-gradient(
                            90deg,
                            var(--line) 1px,
                            transparent 1px
                        ),
                        linear-gradient(
                            180deg,
                            var(--line) 1px,
                            transparent 1px
                        );
                    background-size: 48px 48px;
                "
            ></div>
        </div>

        <ChessMatchResultModal
            :open="showResultModal"
            :result="result"
            primary-href="/local-match"
            primary-label="New match"
            @update:open="showResultModal = $event"
        />
        <ChessMatchDrawOfferModal
            :open="showDrawOfferModal"
            :from-side="drawOffer?.from"
            :action-disabled="isActionLoading"
            @accept="acceptDraw"
            @decline="declineDraw"
            @update:open="handleDrawOfferOpen"
        />

        <ChessPieceSprite />

        <div
            class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-6 pt-8 pb-12 sm:pt-12 lg:px-8"
        >
            <header
                class="flex flex-wrap items-center justify-between gap-4 motion-safe:animate-in motion-safe:duration-700 motion-safe:fade-in-0 motion-safe:slide-in-from-top-4"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="grid size-11 place-items-center rounded-2xl border border-[color:var(--line)] bg-[var(--panel)] shadow-sm"
                    >
                        <svg
                            class="h-6 w-6 text-[color:var(--accent-strong)]"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="square"
                            stroke-linejoin="round"
                        >
                            <path d="M4 20h16" />
                            <path d="M6 20V9h12v11" />
                            <path d="M6 9V6h3V4h2v2h2V4h2v2h3v3" />
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p
                            class="text-[11px] tracking-[0.35em] text-[color:var(--muted)] uppercase"
                        >
                            Local match
                        </p>
                        <p class="font-['Space_Grotesk'] text-lg font-semibold">
                            Echelon
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="rounded-full border border-[color:var(--line)] bg-[var(--panel)] px-3 py-1 text-[11px] tracking-[0.25em] text-[color:var(--muted)] uppercase"
                    >
                        Move {{ moves.length }}
                    </span>
                    <Link
                        href="/local-match"
                        class="rounded-xl border border-[color:var(--line)] bg-[var(--panel)] px-4 py-2 text-sm font-semibold text-[color:var(--ink)]"
                    >
                        Back to setup
                    </Link>
                    <button
                        type="button"
                        class="rounded-xl bg-[var(--accent)] px-4 py-2 text-sm font-semibold text-white shadow-[0_12px_30px_-20px_rgba(15,118,110,0.6)]"
                        @click="exitToLobby"
                    >
                        Exit to lobby
                    </button>
                </div>
            </header>

            <main
                class="mt-10 flex flex-1 flex-col transition-[gap] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] lg:flex-row lg:items-start"
                :class="isSidebarCollapsed ? 'gap-0' : 'gap-8'"
            >
                <section
                    class="flex-1 min-w-0 space-y-6 motion-safe:animate-in motion-safe:duration-700 motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-4"
                    style="animation-delay: 80ms"
                >
                    <ChessMatchBoardCard
                        :title="`${timeControlTitle} - Standard`"
                        :status-label="matchStatusLabel"
                        :move-error="moveError"
                        :board-squares="boardSquares"
                        :selected-square="selectedSquare"
                        :highlight-squares="highlightSquares"
                        :orientation="boardOrientation"
                        :top-player="topPlayer"
                        :bottom-player="bottomPlayer"
                        :top-captured="opponentCaptured"
                        :bottom-captured="playerCaptured"
                        :sidebar-collapsed="isSidebarCollapsed"
                        sidebar-id="match-sidebar"
                        @select-square="selectSquare"
                        @toggle-sidebar="toggleSidebar"
                    />
                </section>

                <div
                    id="match-sidebar"
                    class="relative overflow-hidden transition-[max-height,opacity,transform] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] lg:transition-[max-width,opacity,transform]"
                    :aria-hidden="isSidebarCollapsed"
                    :class="
                        isSidebarCollapsed
                            ? 'max-h-0 opacity-0 -translate-y-4 scale-[0.98] pointer-events-none lg:max-w-0 lg:translate-x-6 lg:translate-y-0'
                            : 'max-h-[2000px] opacity-100 translate-y-0 scale-100 lg:max-w-[320px]'
                    "
                >
                    <div class="lg:w-[320px]">
                        <ChessMatchSidebar
                            :moves="moveList"
                            :ply-count="moves.length"
                            :last-move="lastMove"
                            :evaluation="evaluation"
                            :action-disabled="isActionLoading"
                            :action-error="actionError"
                            :draw-offer-pending="isDrawOfferPending"
                            :draw-offer-from-me="isDrawOfferFromMe"
                            @offer-draw="offerDraw"
                            @surrender="surrenderMatch"
                            @restart="restartMatch"
                            @flip-board="flipBoard"
                        />
                    </div>
                </div>
            </main>

            <footer
                class="mt-10 flex flex-wrap items-center justify-between gap-3 text-xs text-[color:var(--muted)]"
            >
                <p>Local session. UI only.</p>
                <div class="flex flex-wrap items-center gap-4">
                    <span>{{ timeControlTitle }}</span>
                    <span>Standard rules</span>
                    <span>Orion Level 4</span>
                </div>
            </footer>
        </div>
    </div>
</template>
