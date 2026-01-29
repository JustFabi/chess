<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    result: {
        type: Object,
        default: null,
    },
    primaryHref: {
        type: String,
        default: '/dashboard',
    },
    primaryLabel: {
        type: String,
        default: 'Return to lobby',
    },
});

const emit = defineEmits(['update:open']);

const winner = computed(() => props.result?.winner ?? null);
const isDraw = computed(() => {
    if (winner.value === 'draw') {
        return true;
    }
    const reason = props.result?.reason ?? '';
    return reason === 'draw' || reason === 'stalemate';
});

const winnerLabel = computed(() => {
    if (!winner.value || winner.value === 'draw') {
        return '';
    }

    return `${winner.value.charAt(0).toUpperCase()}${winner.value.slice(1)}`;
});

const title = computed(() => {
    if (isDraw.value) {
        return 'Draw';
    }

    if (winnerLabel.value) {
        return `${winnerLabel.value} wins`;
    }

    return 'Game over';
});

const description = computed(() => {
    if (!props.result) {
        return '';
    }

    const reason = props.result?.reason ?? '';
    if (reason === 'checkmate') {
        return 'Checkmate.';
    }
    if (reason === 'stalemate') {
        return 'Stalemate.';
    }
    if (reason === 'resign' || reason === 'abandon') {
        return 'Resignation.';
    }
    if (reason === 'draw') {
        return 'Draw agreed.';
    }

    return 'Game over.';
});

const reasonLabel = computed(() => {
    const reason = props.result?.reason ?? '';
    if (reason === 'checkmate') {
        return 'Checkmate';
    }
    if (reason === 'stalemate') {
        return 'Stalemate';
    }
    if (reason === 'resign' || reason === 'abandon') {
        return 'Resigned';
    }
    if (reason === 'draw') {
        return 'Draw';
    }

    return 'Final';
});
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent
            class="border-0 bg-transparent p-0 shadow-none"
            :show-close-button="false"
            style="
                --panel: rgba(255, 255, 255, 0.78);
                --ink: #0d1117;
                --muted: #5b6370;
                --line: rgba(15, 23, 42, 0.12);
                --accent: #0f766e;
                --accent-strong: #115e59;
                --accent-soft: #d6f1ec;
            "
        >
            <div
                class="relative overflow-hidden rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] text-[color:var(--ink)] shadow-[0_30px_70px_-50px_rgba(15,23,42,0.7)]"
            >
                <div
                    class="pointer-events-none absolute -top-24 right-[-10%] h-56 w-56 rounded-full blur-3xl"
                    style="background: radial-gradient(circle, rgba(15, 118, 110, 0.18), transparent 70%)"
                ></div>
                <div
                    class="pointer-events-none absolute -bottom-24 left-[-10%] h-56 w-56 rounded-full blur-3xl"
                    style="background: radial-gradient(circle, rgba(56, 189, 248, 0.16), transparent 70%)"
                ></div>
                <div
                    class="pointer-events-none absolute inset-0 opacity-[0.06]"
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
                        background-size: 44px 44px;
                    "
                ></div>

                <div class="relative space-y-5 p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="grid size-12 place-items-center rounded-2xl border border-[color:var(--line)] bg-white/80 text-[color:var(--accent-strong)]"
                            >
                                <svg
                                    class="h-6 w-6"
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
                            <div>
                                <p
                                    class="text-[11px] uppercase tracking-[0.35em] text-[color:var(--muted)]"
                                >
                                    Match complete
                                </p>
                                <DialogTitle
                                    class="font-['Space_Grotesk'] text-2xl"
                                >
                                    {{ title }}
                                </DialogTitle>
                            </div>
                        </div>
                        <span
                            class="rounded-full border border-[color:var(--line)] bg-white/70 px-3 py-1 text-xs font-semibold text-[color:var(--ink)]"
                        >
                            {{ reasonLabel }}
                        </span>
                    </div>

                    <DialogDescription
                        class="text-sm text-[color:var(--muted)]"
                    >
                        {{ description }}
                    </DialogDescription>

                    <DialogFooter class="mt-4">
                        <DialogClose as-child>
                            <button
                                type="button"
                                class="rounded-xl border border-[color:var(--line)] bg-white/70 px-4 py-2 text-sm font-semibold text-[color:var(--ink)]"
                            >
                                Close
                            </button>
                        </DialogClose>
                        <Link
                            :href="primaryHref"
                            class="rounded-xl bg-[var(--accent)] px-4 py-2 text-center text-sm font-semibold text-white shadow-[0_12px_30px_-20px_rgba(15,118,110,0.6)]"
                        >
                            {{ primaryLabel }}
                        </Link>
                    </DialogFooter>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
