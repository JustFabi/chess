<script setup>
import { computed } from 'vue';

const props = defineProps({
    moves: {
        type: Array,
        default: () => [],
    },
    plyCount: {
        type: Number,
        default: 0,
    },
    lastMove: {
        type: Object,
        default: null,
    },
    evaluation: {
        type: [Number, String],
        default: 0,
    },
    actionDisabled: {
        type: Boolean,
        default: false,
    },
    actionError: {
        type: String,
        default: null,
    },
    drawOfferPending: {
        type: Boolean,
        default: false,
    },
    drawOfferFromMe: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['offer-draw', 'surrender', 'restart', 'flip-board']);

const evaluationScore = computed(() => {
    const value = Number(props.evaluation ?? 0);
    return Number.isFinite(value) ? value : 0;
});

const evaluationValueLabel = computed(() => {
    const pawns = Math.abs(evaluationScore.value) / 100;
    const side = evaluationScore.value >= 0 ? 'White' : 'Black';
    return `${side} +${pawns.toFixed(1)}`;
});

const evaluationStatus = computed(() => {
    const score = evaluationScore.value;
    const absScore = Math.abs(score);

    if (absScore < 30) {
        return 'Balanced';
    }

    if (absScore < 120) {
        return score > 0 ? 'White edge' : 'Black edge';
    }

    if (absScore < 300) {
        return score > 0 ? 'White better' : 'Black better';
    }

    return score > 0 ? 'White winning' : 'Black winning';
});

const evaluationBarStyle = computed(() => {
    const range = 800;
    const clamped = Math.max(-range, Math.min(range, evaluationScore.value));
    const percent = ((clamped + range) / (2 * range)) * 100;

    return {
        width: `${percent}%`,
    };
});

const evaluationBarClass = computed(() =>
    evaluationScore.value >= 0 ? 'bg-[var(--accent)]' : 'bg-[#111827]',
);

const plyLabel = computed(() => {
    const total = Number(props.plyCount ?? 0);
    const safeTotal = Number.isFinite(total) ? total : 0;
    return `${safeTotal} ply`;
});

const lastMoveLabel = computed(() => {
    if (!props.lastMove?.from || !props.lastMove?.to) {
        return '--';
    }

    return `${props.lastMove.from} to ${props.lastMove.to}`;
});

const offerDrawLabel = computed(() => {
    if (!props.drawOfferPending) {
        return 'Offer draw';
    }

    return props.drawOfferFromMe ? 'Offer sent' : 'Offer pending';
});
</script>

<template>
    <aside
        class="space-y-6 motion-safe:animate-in motion-safe:duration-700 motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-6"
        style="animation-delay: 160ms"
    >
        <div class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p
                        class="text-[11px] tracking-[0.3em] text-[color:var(--muted)] uppercase"
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
                    {{ plyLabel }}
                </span>
            </div>
            <div class="mt-4 grid gap-2 text-sm">
                <div
                    v-for="(move, index) in moves"
                    :key="move.move ?? index"
                    class="grid grid-cols-[32px_1fr_1fr] items-center gap-2 rounded-xl border border-transparent px-3 py-2"
                    :class="
                        index === moves.length - 1
                            ? 'border-[color:var(--line)] bg-[var(--accent-soft)]'
                            : 'bg-white/70'
                    "
                >
                    <span class="text-xs font-semibold text-[color:var(--muted)]">
                        {{ move.move }}
                    </span>
                    <span class="font-semibold text-[color:var(--ink)]">
                        {{ move.white }}
                    </span>
                    <span class="font-semibold text-[color:var(--ink)]">
                        {{ move.black }}
                    </span>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-[color:var(--muted)]">
                <span>Last move: {{ lastMoveLabel }}</span>
                <span>Opening: Sicilian</span>
            </div>
        </div>

        <div class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6">
            <p
                class="text-[11px] tracking-[0.3em] text-[color:var(--muted)] uppercase"
            >
                Game controls
            </p>
            <h3 class="mt-2 font-['Space_Grotesk'] text-xl font-semibold">
                Actions
            </h3>
            <div v-if="actionError" class="mt-3 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">
                {{ actionError }}
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <button
                    type="button"
                    class="rounded-xl border border-[color:var(--line)] bg-white/70 px-3 py-2 font-semibold text-[color:var(--ink)] disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="actionDisabled || drawOfferPending"
                    @click="emit('offer-draw')"
                >
                    {{ offerDrawLabel }}
                </button>
                <button
                    type="button"
                    class="rounded-xl border border-[color:var(--line)] bg-white/70 px-3 py-2 font-semibold text-[color:var(--ink)] disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="actionDisabled"
                    @click="emit('surrender')"
                >
                    Resign
                </button>
                <button
                    type="button"
                    class="rounded-xl border border-[color:var(--line)] bg-white/70 px-3 py-2 font-semibold text-[color:var(--ink)] disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="actionDisabled"
                    @click="emit('restart')"
                >
                    Restart
                </button>
                <button
                    type="button"
                    class="rounded-xl border border-[color:var(--line)] bg-white/70 px-3 py-2 font-semibold text-[color:var(--ink)] disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="actionDisabled"
                    @click="emit('flip-board')"
                >
                    Flip board
                </button>
            </div>
            <div
                v-if="drawOfferPending && drawOfferFromMe"
                class="mt-3 rounded-xl border border-[color:var(--line)] bg-white/70 px-3 py-2 text-xs font-semibold text-[color:var(--muted)]"
            >
                Draw offer sent. Waiting for a response.
            </div>
        </div>

        <div class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6">
            <p
                class="text-[11px] tracking-[0.3em] text-[color:var(--muted)] uppercase"
            >
                Analysis
            </p>
            <h3 class="mt-2 font-['Space_Grotesk'] text-xl font-semibold">
                Evaluation
            </h3>
            <div class="mt-4">
                <div class="relative h-2 overflow-hidden rounded-full bg-white/70">
                    <div
                        class="absolute left-1/2 top-0 h-full w-px bg-[color:var(--line)]"
                    ></div>
                    <div
                        class="h-full rounded-full motion-safe:transition-all motion-safe:duration-500 motion-safe:ease-out"
                        :class="evaluationBarClass"
                        :style="evaluationBarStyle"
                    ></div>
                </div>
                <div class="mt-2 flex items-center justify-between text-xs text-[color:var(--muted)]">
                    <span>{{ evaluationValueLabel }}</span>
                    <span>{{ evaluationStatus }}</span>
                </div>
            </div>
            <div
                class="mt-4 rounded-2xl border border-[color:var(--line)] bg-white/70 px-4 py-3 text-xs text-[color:var(--muted)]"
            >
                Engine line: 3. d4 cxd4 4. Nxd4
            </div>
        </div>
    </aside>
</template>
