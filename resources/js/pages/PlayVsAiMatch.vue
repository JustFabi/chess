<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import { watch } from 'vue';
import { useGameStateStore } from '../stores/gameStateStore';

const props = defineProps({
    gameState: {
        type: Object,
        required: true,
    },
});

const gameStateStore = useGameStateStore();

watch(
    () => props.gameState,
    (nextState) => {
        if (nextState) {
            gameStateStore.setGameState(nextState);
        }
    },
    { immediate: true },
);

const {
    board,
    capturedByBlack,
    capturedByWhite,
    moves,
    lastMove,
} = storeToRefs(gameStateStore);

const pieceStyle = (color) => ({
    color: color === 'white' ? '#f8f4ec' : '#1b232a',
    '--piece-stroke':
        color === 'white'
            ? 'rgba(15, 23, 42, 0.25)'
            : 'rgba(255, 255, 255, 0.18)',
});

const pieceShadowClass = (color) =>
    color === 'white'
        ? 'drop-shadow-[0_6px_8px_rgba(15,23,42,0.25)]'
        : 'drop-shadow-[0_6px_10px_rgba(15,23,42,0.45)]';

const selectSquare = (square) => {
    if(!square?.piece) {
        return
    }
    gameStateStore.selectPiece(square.piece);
};

</script>

<template>
    <Head title="Play vs AI">
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
                class="absolute bottom-[-220px] right-[-10%] h-[560px] w-[560px] rounded-full opacity-70 blur-3xl"
                style="background: radial-gradient(circle, var(--glow-2), transparent 65%)"
            ></div>
            <div
                class="absolute inset-0 opacity-[0.08]"
                style="
                    background-image: linear-gradient(
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

        <svg
            aria-hidden="true"
            focusable="false"
            class="absolute h-0 w-0 overflow-hidden"
        >
            <symbol id="piece-king" viewBox="0 0 64 64">
                <g
                    fill="currentColor"
                    stroke="var(--piece-stroke)"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M30 8h4v8h6v4h-6v8h-4v-8h-6v-4h6z" />
                    <path d="M24 26c0-6 5-10 8-10s8 4 8 10l-2 12H26z" />
                    <path d="M22 38h20l-2 8H24z" />
                    <rect x="20" y="46" width="24" height="7" rx="2" />
                    <rect x="14" y="53" width="36" height="7" rx="2" />
                </g>
            </symbol>
            <symbol id="piece-queen" viewBox="0 0 64 64">
                <g
                    fill="currentColor"
                    stroke="var(--piece-stroke)"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="16" cy="18" r="3" />
                    <circle cx="24" cy="12" r="3" />
                    <circle cx="32" cy="10" r="3" />
                    <circle cx="40" cy="12" r="3" />
                    <circle cx="48" cy="18" r="3" />
                    <path d="M16 26h32l-4 14H20z" />
                    <path d="M22 40h20l-2 8H24z" />
                    <rect x="18" y="46" width="28" height="7" rx="2" />
                    <rect x="14" y="53" width="36" height="7" rx="2" />
                </g>
            </symbol>
            <symbol id="piece-rook" viewBox="0 0 64 64">
                <g
                    fill="currentColor"
                    stroke="var(--piece-stroke)"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M16 14h8v6h4v-6h8v6h4v-6h8v14H16z" />
                    <rect x="22" y="30" width="20" height="14" rx="2" />
                    <rect x="18" y="46" width="28" height="7" rx="2" />
                    <rect x="14" y="53" width="36" height="7" rx="2" />
                </g>
            </symbol>
            <symbol id="piece-bishop" viewBox="0 0 64 64">
                <g
                    fill="currentColor"
                    stroke="var(--piece-stroke)"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="32" cy="16" r="6" />
                    <path
                        d="M24 40c0-7 5-11 8-17 3 6 8 10 8 17l-2 8H26z"
                    />
                    <path d="M22 46h20l-2 6H24z" />
                    <rect x="18" y="50" width="28" height="3" rx="1.5" />
                    <rect x="14" y="53" width="36" height="7" rx="2" />
                </g>
                <path
                    d="M30 18l8 12"
                    fill="none"
                    stroke="var(--piece-stroke)"
                    stroke-width="2"
                    stroke-linecap="round"
                />
            </symbol>
            <symbol id="piece-knight" viewBox="0 0 64 64">
                <g
                    fill="currentColor"
                    stroke="var(--piece-stroke)"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path
                        d="M22 50c0-12 5-22 16-28l8-5 8 8-6 8 6 6-6 11H22z"
                    />
                    <path
                        d="M30 40l6-10 10-4"
                        fill="none"
                        stroke="var(--piece-stroke)"
                        stroke-width="1.5"
                        stroke-linecap="round"
                    />
                    <rect x="18" y="46" width="28" height="7" rx="2" />
                    <rect x="14" y="53" width="36" height="7" rx="2" />
                </g>
                <circle cx="42" cy="24" r="1.8" fill="var(--piece-stroke)" />
            </symbol>
            <symbol id="piece-pawn" viewBox="0 0 64 64">
                <g
                    fill="currentColor"
                    stroke="var(--piece-stroke)"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="32" cy="18" r="6.5" />
                    <path d="M24 38c0-6 4-10 8-10s8 4 8 10l-2 8H26z" />
                    <rect x="22" y="40" width="20" height="6" rx="2" />
                    <rect x="18" y="46" width="28" height="7" rx="2" />
                    <rect x="14" y="53" width="36" height="7" rx="2" />
                </g>
            </symbol>
        </svg>

        <div
            class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-6 pb-12 pt-8 sm:pt-12 lg:px-8"
        >
            <header
                class="flex flex-wrap items-center justify-between gap-4 motion-safe:animate-in motion-safe:fade-in-0 motion-safe:slide-in-from-top-4 motion-safe:duration-700"
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
                            class="text-[11px] uppercase tracking-[0.35em] text-[color:var(--muted)]"
                        >
                            Match vs AI
                        </p>
                        <p
                            class="font-['Space_Grotesk'] text-lg font-semibold"
                        >
                            Echelon
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="rounded-full border border-[color:var(--line)] bg-[var(--panel)] px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                    >
                        Move {{ moves.length }}
                    </span>
                    <Link
                        href="/play-vs-ai"
                        class="rounded-xl border border-[color:var(--line)] bg-[var(--panel)] px-4 py-2 text-sm font-semibold text-[color:var(--ink)]"
                    >
                        Back to setup
                    </Link>
                    <Link
                        href="/dashboard"
                        class="rounded-xl bg-[var(--accent)] px-4 py-2 text-sm font-semibold text-white shadow-[0_12px_30px_-20px_rgba(15,118,110,0.6)]"
                    >
                        Exit to lobby
                    </Link>
                </div>
            </header>

            <main
                class="mt-10 grid flex-1 gap-8 lg:grid-cols-[minmax(0,1fr)_320px]"
            >
                <section
                    class="space-y-6 motion-safe:animate-in motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-4 motion-safe:duration-700"
                    style="animation-delay: 80ms"
                >
                    <div
                        class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6 shadow-[0_30px_80px_-60px_rgba(15,23,42,0.6)]"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-[11px] uppercase tracking-[0.3em] text-[color:var(--muted)]"
                                >
                                    Match board
                                </p>
                                <h2
                                    class="mt-2 font-['Space_Grotesk'] text-2xl font-semibold"
                                >
                                    Rapid 10+5 - Standard
                                </h2>
                            </div>
                            <span
                                class="rounded-full bg-[var(--accent-soft)] px-3 py-1 text-xs font-semibold text-[color:var(--accent-strong)]"
                            >
                                Your turn
                            </span>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="grid size-11 place-items-center rounded-full border border-[color:var(--line)] bg-white/70 text-sm font-semibold text-[color:var(--ink)]"
                                    >
                                        AI
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">
                                            Orion Level 4
                                        </p>
                                        <p
                                            class="text-xs text-[color:var(--muted)]"
                                        >
                                            Black pieces
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="rounded-full border border-[color:var(--line)] bg-white/70 px-2 py-1 text-[11px] uppercase tracking-[0.2em] text-[color:var(--muted)]"
                                    >
                                        Black
                                    </span>
                                    <span
                                        class="rounded-xl bg-[#111827] px-3 py-2 font-['Space_Grotesk'] text-sm font-semibold text-white"
                                    >
                                        09:51
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span
                                    class="uppercase tracking-[0.25em] text-[color:var(--muted)]"
                                >
                                    Captured
                                </span>
                                <div class="flex items-center gap-1">
                                    <svg
                                        v-for="(piece, index) in capturedByBlack"
                                        :key="`black-${piece.type}-${index}`"
                                        viewBox="0 0 64 64"
                                        class="h-4 w-4"
                                        :class="pieceShadowClass(piece.color)"
                                        :style="pieceStyle(piece.color)"
                                    >
                                        <use
                                            :href="`#piece-${piece.type}`"
                                        />
                                    </svg>
                                </div>
                            </div>

                            <div
                                class="rounded-2xl border border-[color:var(--line)] bg-white/70 p-3"
                            >
                                <div
                                    class="grid aspect-square grid-cols-8 grid-rows-8 overflow-hidden rounded-xl ring-1 ring-[color:var(--line)]"
                                >
                                    <div
                                        @click="selectSquare(square)"
                                        v-for="square in board.squares"
                                        :key="square.key"
                                        class="relative flex items-center justify-center"
                                    >
                                        <div
                                            class="absolute inset-0"
                                            :class="
                                                square.isDark
                                                    ? 'bg-[var(--board-dark)]'
                                                    : 'bg-[var(--board-light)]'
                                            "
                                        ></div>
                                        <div
                                            v-if="
                                                gameStateStore?.selectedPiece && gameStateStore.selectedPiece.id === square?.piece?.id
                                            "
                                            class="absolute inset-0 ring-2 ring-[var(--accent)] ring-inset"
                                        ></div>
                                        <span
                                            v-if="square.file === 'a'"
                                            class="absolute left-1 top-1 text-[10px] font-semibold"
                                            :class="
                                                square.isDark
                                                    ? 'text-white/70'
                                                    : 'text-[color:var(--muted)]'
                                            "
                                        >
                                            {{ square.rank }}
                                        </span>
                                        <span
                                            v-if="square.rank === 1"
                                            class="absolute bottom-1 right-1 text-[10px] font-semibold"
                                            :class="
                                                square.isDark
                                                    ? 'text-white/70'
                                                    : 'text-[color:var(--muted)]'
                                            "
                                        >
                                            {{ square.file }}
                                        </span>
                                        <svg
                                            v-if="square.piece"
                                            viewBox="0 0 64 64"
                                            class="relative z-10 h-full w-full p-1.5"
                                            :class="
                                                pieceShadowClass(
                                                    square.piece.color,
                                                )
                                            "
                                            :style="
                                                pieceStyle(square.piece.color)
                                            "
                                        >
                                            <use
                                                :href="`#piece-${square.piece.type}`"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="grid size-11 place-items-center rounded-full border border-[color:var(--line)] bg-[var(--accent-soft)] text-sm font-semibold text-[color:var(--accent-strong)]"
                                    >
                                        You
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">
                                            Guest
                                        </p>
                                        <p
                                            class="text-xs text-[color:var(--muted)]"
                                        >
                                            White pieces
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="rounded-full border border-[color:var(--line)] bg-white/70 px-2 py-1 text-[11px] uppercase tracking-[0.2em] text-[color:var(--muted)]"
                                    >
                                        White
                                    </span>
                                    <span
                                        class="rounded-xl bg-[#111827] px-3 py-2 font-['Space_Grotesk'] text-sm font-semibold text-white"
                                    >
                                        09:44
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span
                                    class="uppercase tracking-[0.25em] text-[color:var(--muted)]"
                                >
                                    Captured
                                </span>
                                <div class="flex items-center gap-1">
                                    <svg
                                        v-for="(piece, index) in capturedByWhite"
                                        :key="`white-${piece.type}-${index}`"
                                        viewBox="0 0 64 64"
                                        class="h-4 w-4"
                                        :class="pieceShadowClass(piece.color)"
                                        :style="pieceStyle(piece.color)"
                                    >
                                        <use
                                            :href="`#piece-${piece.type}`"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside
                    class="space-y-6 motion-safe:animate-in motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-6 motion-safe:duration-700"
                    style="animation-delay: 160ms"
                >
                    <div
                        class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-[11px] uppercase tracking-[0.3em] text-[color:var(--muted)]"
                                >
                                    Move list
                                </p>
                                <h3
                                    class="mt-2 font-['Space_Grotesk'] text-xl font-semibold"
                                >
                                    Opening moves
                                </h3>
                            </div>
                            <span
                                class="rounded-full border border-[color:var(--line)] bg-white/70 px-3 py-1 text-xs font-semibold text-[color:var(--ink)]"
                            >
                                2 ply
                            </span>
                        </div>
                        <div class="mt-4 grid gap-2 text-sm">
                            <div
                                v-for="move in moves"
                                :key="move.move"
                                class="grid grid-cols-[32px_1fr_1fr] items-center gap-2 rounded-xl border border-transparent px-3 py-2"
                                :class="
                                    move.move === 2
                                        ? 'border-[color:var(--line)] bg-[var(--accent-soft)]'
                                        : 'bg-white/70'
                                "
                            >
                                <span
                                    class="text-xs font-semibold text-[color:var(--muted)]"
                                >
                                    {{ move.move }}
                                </span>
                                <span
                                    class="font-semibold text-[color:var(--ink)]"
                                >
                                    {{ move.white }}
                                </span>
                                <span
                                    class="font-semibold text-[color:var(--ink)]"
                                >
                                    {{ move.black }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="mt-4 flex items-center justify-between text-xs text-[color:var(--muted)]"
                        >
                            <span>
                                Last move:
                                {{
                                    lastMove
                                        ? `${lastMove.from} to ${lastMove.to}`
                                        : '—'
                                }}
                            </span>
                            <span>Opening: Sicilian</span>
                        </div>
                    </div>

                    <div
                        class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6"
                    >
                        <p
                            class="text-[11px] uppercase tracking-[0.3em] text-[color:var(--muted)]"
                        >
                            Game controls
                        </p>
                        <h3
                            class="mt-2 font-['Space_Grotesk'] text-xl font-semibold"
                        >
                            Actions
                        </h3>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <button
                                type="button"
                                class="rounded-xl border border-[color:var(--line)] bg-white/70 px-3 py-2 font-semibold text-[color:var(--ink)]"
                            >
                                Offer draw
                            </button>
                            <button
                                type="button"
                                class="rounded-xl border border-[color:var(--line)] bg-white/70 px-3 py-2 font-semibold text-[color:var(--ink)]"
                            >
                                Resign
                            </button>
                            <button
                                type="button"
                                class="rounded-xl border border-[color:var(--line)] bg-white/70 px-3 py-2 font-semibold text-[color:var(--ink)]"
                            >
                                Restart
                            </button>
                            <button
                                type="button"
                                class="rounded-xl border border-[color:var(--line)] bg-white/70 px-3 py-2 font-semibold text-[color:var(--ink)]"
                            >
                                Flip board
                            </button>
                        </div>
                        <div
                            class="mt-4 flex items-center justify-between rounded-2xl border border-[color:var(--line)] bg-white/70 px-4 py-3 text-xs text-[color:var(--muted)]"
                        >
                            <span>Hints</span>
                            <span class="font-semibold text-[color:var(--ink)]"
                                >Off</span
                            >
                        </div>
                    </div>

                    <div
                        class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6"
                    >
                        <p
                            class="text-[11px] uppercase tracking-[0.3em] text-[color:var(--muted)]"
                        >
                            Analysis
                        </p>
                        <h3
                            class="mt-2 font-['Space_Grotesk'] text-xl font-semibold"
                        >
                            Evaluation
                        </h3>
                        <div class="mt-4">
                            <div class="h-2 rounded-full bg-white/70">
                                <div
                                    class="h-full w-[54%] rounded-full bg-[var(--accent)]"
                                ></div>
                            </div>
                            <div
                                class="mt-2 flex items-center justify-between text-xs text-[color:var(--muted)]"
                            >
                                <span>White +0.2</span>
                                <span>Balanced</span>
                            </div>
                        </div>
                        <div
                            class="mt-4 rounded-2xl border border-[color:var(--line)] bg-white/70 px-4 py-3 text-xs text-[color:var(--muted)]"
                        >
                            Engine line: 3. d4 cxd4 4. Nxd4
                        </div>
                    </div>
                </aside>
            </main>

            <footer
                class="mt-10 flex flex-wrap items-center justify-between gap-3 text-xs text-[color:var(--muted)]"
            >
                <p>Guest session. UI only.</p>
                <div class="flex flex-wrap items-center gap-4">
                    <span>Rapid 10+5</span>
                    <span>Standard rules</span>
                    <span>Orion Level 4</span>
                </div>
            </footer>
        </div>
    </div>
</template>
