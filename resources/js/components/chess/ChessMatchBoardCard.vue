<script setup>
import ChessBoard from './ChessBoard.vue';
import ChessPieceIcon from './ChessPieceIcon.vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    statusLabel: {
        type: String,
        required: true,
    },
    moveError: {
        type: String,
        default: null,
    },
    boardSquares: {
        type: Array,
        required: true,
    },
    selectedSquare: {
        type: String,
        default: null,
    },
    highlightSquares: {
        type: Array,
        default: () => [],
    },
    orientation: {
        type: String,
        default: 'white',
    },
    topPlayer: {
        type: Object,
        required: true,
    },
    bottomPlayer: {
        type: Object,
        required: true,
    },
    topCaptured: {
        type: Array,
        default: () => [],
    },
    bottomCaptured: {
        type: Array,
        default: () => [],
    },
    sidebarCollapsed: {
        type: Boolean,
        default: false,
    },
    sidebarId: {
        type: String,
        default: 'match-sidebar',
    },
});

const emit = defineEmits(['select-square', 'toggle-sidebar']);

const defaultTopBadgeClass =
    'grid size-11 place-items-center rounded-full border border-[color:var(--line)] bg-white/70 text-sm font-semibold text-[color:var(--ink)]';
const defaultBottomBadgeClass =
    'grid size-11 place-items-center rounded-full border border-[color:var(--line)] bg-[var(--accent-soft)] text-sm font-semibold text-[color:var(--accent-strong)]';

const selectSquare = (square) => {
    emit('select-square', square);
};
</script>

<template>
    <div
        class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6 shadow-[0_30px_80px_-60px_rgba(15,23,42,0.6)]"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p
                    class="text-[11px] tracking-[0.3em] text-[color:var(--muted)] uppercase"
                >
                    Match board
                </p>
                <h2
                    class="mt-2 font-['Space_Grotesk'] text-2xl font-semibold"
                >
                    {{ title }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="group inline-flex items-center gap-2 rounded-full border border-[color:var(--line)] bg-white/70 px-3 py-1.5 text-xs font-semibold text-[color:var(--ink)] shadow-[0_10px_25px_-18px_rgba(15,23,42,0.45)] transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-0.5 hover:shadow-[0_14px_34px_-18px_rgba(15,23,42,0.4)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[color:var(--accent)]"
                    :class="
                        sidebarCollapsed
                            ? 'border-transparent bg-[var(--accent)] text-white shadow-[0_12px_30px_-18px_rgba(15,118,110,0.55)]'
                            : ''
                    "
                    :aria-expanded="!sidebarCollapsed"
                    :aria-controls="sidebarId"
                    :title="
                        sidebarCollapsed
                            ? 'Open match panel'
                            : 'Collapse match panel'
                    "
                    @click="emit('toggle-sidebar')"
                >
                    <span class="sr-only">
                        {{
                            sidebarCollapsed
                                ? 'Open match panel'
                                : 'Collapse match panel'
                        }}
                    </span>
                    <svg
                        class="h-4 w-4 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-0.5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <rect x="3" y="4" width="18" height="16" rx="2" />
                        <path d="M9 4v16" />
                    </svg>
                    <span
                        class="overflow-hidden whitespace-nowrap text-xs font-semibold transition-[max-width,opacity,transform] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
                        :class="
                            sidebarCollapsed
                                ? 'max-w-[140px] opacity-100 translate-x-0'
                                : 'max-w-0 opacity-0 -translate-x-1'
                        "
                    >
                        Match menu
                    </span>
                </button>
                <span
                    class="rounded-full bg-[var(--accent-soft)] px-3 py-1 text-xs font-semibold text-[color:var(--accent-strong)]"
                >
                    {{ statusLabel }}
                </span>
            </div>
        </div>
        <div
            v-if="moveError"
            class="mt-4 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700"
        >
            {{ moveError }}
        </div>

        <div class="mt-5 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div :class="topPlayer.badgeClass || defaultTopBadgeClass">
                        {{ topPlayer.badge }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold">
                            {{ topPlayer.name }}
                        </p>
                        <p class="text-xs text-[color:var(--muted)]">
                            {{ topPlayer.subtitle }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        v-if="topPlayer.side"
                        class="rounded-full border border-[color:var(--line)] bg-white/70 px-2 py-1 text-[11px] tracking-[0.2em] text-[color:var(--muted)] uppercase"
                    >
                        {{ topPlayer.side }}
                    </span>
                    <span
                        v-if="topPlayer.clock"
                        class="rounded-xl bg-[#111827] px-3 py-2 font-['Space_Grotesk'] text-sm font-semibold text-white"
                    >
                        {{ topPlayer.clock }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span
                    class="tracking-[0.25em] text-[color:var(--muted)] uppercase"
                >
                    Captured
                </span>
                <div class="flex items-center gap-1">
                    <ChessPieceIcon
                        v-for="(piece, index) in topCaptured"
                        :key="`top-${piece.type}-${index}`"
                        :piece="piece"
                        class-name="h-4 w-4"
                    />
                </div>
            </div>

            <ChessBoard
                :board-squares="boardSquares"
                :selected-square="selectedSquare"
                :highlight-squares="highlightSquares"
                :orientation="orientation"
                @select-square="selectSquare"
            />

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        :class="bottomPlayer.badgeClass || defaultBottomBadgeClass"
                    >
                        {{ bottomPlayer.badge }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold">
                            {{ bottomPlayer.name }}
                        </p>
                        <p class="text-xs text-[color:var(--muted)]">
                            {{ bottomPlayer.subtitle }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        v-if="bottomPlayer.side"
                        class="rounded-full border border-[color:var(--line)] bg-white/70 px-2 py-1 text-[11px] tracking-[0.2em] text-[color:var(--muted)] uppercase"
                    >
                        {{ bottomPlayer.side }}
                    </span>
                    <span
                        v-if="bottomPlayer.clock"
                        class="rounded-xl bg-[#111827] px-3 py-2 font-['Space_Grotesk'] text-sm font-semibold text-white"
                    >
                        {{ bottomPlayer.clock }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span
                    class="tracking-[0.25em] text-[color:var(--muted)] uppercase"
                >
                    Captured
                </span>
                <div class="flex items-center gap-1">
                    <ChessPieceIcon
                        v-for="(piece, index) in bottomCaptured"
                        :key="`bottom-${piece.type}-${index}`"
                        :piece="piece"
                        class-name="h-4 w-4"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
