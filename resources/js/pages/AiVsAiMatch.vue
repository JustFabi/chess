<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { storeToRefs } from 'pinia';
import { computed, ref, watch } from 'vue';
import ChessMatchBoardCard from '@/components/chess/ChessMatchBoardCard.vue';
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
    gameId,
    result,
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
const isBoardFlipped = ref(false);
const isActionLoading = ref(false);
const isEngineThinking = ref(false);
const isSidebarCollapsed = ref(false);

const boardOrientation = computed(() =>
    isBoardFlipped.value ? 'black' : 'white',
);

const timeControlTitle = computed(() =>
    formatTimeControlTitle(settings.value?.timeControl),
);

const topPlayer = computed(() => ({
    badge: 'AI',
    name: 'Redline Black',
    subtitle: `black pieces`,
    side: 'black',
    clock: blackClock.value,
}));

const bottomPlayer = computed(() => ({
    badge: 'AI',
    name: 'Redline White',
    subtitle: `white pieces`,
    side: 'white',
    clock: whiteClock.value,
}));

const matchStatusLabel = computed(() => {
    if (result.value) return 'Game over';
    return isEngineThinking.value ? 'AI is thinking...' : 'AI vs AI Match';
});

watch(
    () => result.value,
    (nextResult) => {
        showResultModal.value = Boolean(nextResult);
    },
    { immediate: true },
);

const triggerEngineMove = async () => {
    if (isEngineThinking.value || result.value) {
        return;
    }

    isEngineThinking.value = true;
    try {
        const response = await axios.post(
            route('ai-vs-ai.engine-move'),
            {
                gameId: gameId.value,
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
    } catch (error) {
        console.error('Engine move failed:', error);
        moveError.value = 'AI failed to respond.';
    } finally {
        isEngineThinking.value = false;
    }
};

// Automatically trigger moves in AI vs AI mode
watch(
    () => [clock.value?.active, result.value],
    ([activeSide, gameResult]) => {
        if (activeSide && !gameResult && !isEngineThinking.value) {
            // Add a small delay for better visual experience
            setTimeout(() => {
                triggerEngineMove();
            }, 500);
        }
    },
    { immediate: true },
);

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
            route('ai-vs-ai.action'),
            {
                gameId: gameId.value,
                action,
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
            errors?.action ??
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

const restartMatch = () => performAction('restart');
const flipBoard = () => {
    isBoardFlipped.value = !isBoardFlipped.value;
};
const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const exitToLobby = () => {
    router.visit(route('dashboard'));
};
</script>

<template>
    <Head title="AI vs AI Match">
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
                style="background: radial-gradient(circle, var(--glow), transparent 70%)"
            ></div>
            <div
                class="absolute right-[-10%] bottom-[-220px] h-[560px] w-[560px] rounded-full opacity-70 blur-3xl"
                style="background: radial-gradient(circle, var(--glow-2), transparent 65%)"
            ></div>
        </div>

        <ChessMatchResultModal
            :open="showResultModal"
            :result="result"
            primary-href="/ai-vs-ai"
            primary-label="New match"
            @update:open="showResultModal = $event"
        />

        <ChessPieceSprite />

        <div
            class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-6 pt-8 pb-12 sm:pt-12 lg:px-8"
        >
            <header class="flex flex-wrap items-center justify-between gap-4">
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
                            AI vs AI
                        </p>
                        <p class="font-['Space_Grotesk'] text-lg font-semibold">
                            Redline Engine
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
                        href="/ai-vs-ai"
                        class="rounded-xl border border-[color:var(--line)] bg-[var(--panel)] px-4 py-2 text-sm font-semibold text-[color:var(--ink)]"
                    >
                        Back to setup
                    </Link>
                    <button
                        type="button"
                        class="rounded-xl bg-[var(--accent)] px-4 py-2 text-sm font-semibold text-white shadow-lg"
                        @click="exitToLobby"
                    >
                        Exit to lobby
                    </button>
                </div>
            </header>

            <main
                class="mt-10 flex flex-1 flex-col gap-8 lg:flex-row lg:items-start"
            >
                <section class="min-w-0 flex-1 space-y-6">
                    <ChessMatchBoardCard
                        :title="`${timeControlTitle} - AI vs AI`"
                        :status-label="matchStatusLabel"
                        :move-error="moveError"
                        :board-squares="boardSquares"
                        :orientation="boardOrientation"
                        :top-player="topPlayer"
                        :bottom-player="bottomPlayer"
                        :top-captured="capturedPieces.capturedByWhite"
                        :bottom-captured="capturedPieces.capturedByBlack"
                        :sidebar-collapsed="isSidebarCollapsed"
                        sidebar-id="match-sidebar"
                        @toggle-sidebar="toggleSidebar"
                    />
                </section>

                <div
                    id="match-sidebar"
                    class="lg:w-[320px]"
                    v-if="!isSidebarCollapsed"
                >
                    <ChessMatchSidebar
                        :moves="moveList"
                        :ply-count="moves.length"
                        :last-move="lastMove"
                        :action-disabled="isActionLoading"
                        :action-error="actionError"
                        :evaluation="board?.evaluation ?? 0"
                        @restart="restartMatch"
                        @flip-board="flipBoard"
                    />
                </div>
            </main>
        </div>
    </div>
</template>
