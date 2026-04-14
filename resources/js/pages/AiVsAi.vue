<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    formatTimeControlLabel,
    timeControlPresets,
} from '@/lib/timeControls';

const timeControlOptions = timeControlPresets;
const selectedTimeControl = ref('10+5');

const optionBase =
    'rounded-lg border border-[color:var(--line)] px-3 py-2 text-sm font-semibold';
const optionClass = (isActive) => [
    optionBase,
    isActive
        ? 'bg-[var(--accent-soft)] text-[color:var(--accent-strong)]'
        : 'bg-white/70 text-[color:var(--ink)]',
];

const selectedTimeControlLabel = computed(() =>
    formatTimeControlLabel(selectedTimeControl.value),
);

const cycleTimeControl = (direction) => {
    if (!timeControlOptions.length) {
        return;
    }

    const currentIndex = timeControlOptions.findIndex(
        (option) => option.value === selectedTimeControl.value,
    );
    const nextIndex =
        (currentIndex + direction + timeControlOptions.length) %
        timeControlOptions.length;
    selectedTimeControl.value = timeControlOptions[nextIndex].value;
};

const startMatch = () => {
    router.post(route('ai-vs-ai.match'), {
        timeControl: selectedTimeControl.value,
        variant: 'standard',
    });
};
</script>

<template>
    <Head title="AI vs AI">
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
        </div>

        <div class="relative flex min-h-screen items-center justify-center p-6">
            <div class="w-full max-w-md">
                <div
                    class="overflow-hidden rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] shadow-2xl backdrop-blur-md"
                >
                    <div class="p-8">
                        <div class="mb-8 text-center">
                            <h1
                                class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight"
                            >
                                AI vs AI
                            </h1>
                            <p class="mt-2 text-sm text-[color:var(--muted)]">
                                Watch two engines battle it out
                            </p>
                        </div>

                        <div class="space-y-8">
                            <div>
                                <label
                                    class="mb-3 block text-xs font-bold uppercase tracking-wider text-[color:var(--muted)]"
                                >
                                    Time Control
                                </label>
                                <div class="flex items-center gap-3">
                                    <button
                                        @click="cycleTimeControl(-1)"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-[color:var(--line)] bg-white/50 transition-colors hover:bg-white"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m15 18-6-6 6-6" />
                                        </svg>
                                    </button>
                                    <div
                                        class="flex-1 rounded-xl border border-[color:var(--line)] bg-white/80 py-3 text-center"
                                    >
                                        <span class="text-lg font-bold">
                                            {{ selectedTimeControlLabel }}
                                        </span>
                                    </div>
                                    <button
                                        @click="cycleTimeControl(1)"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-[color:var(--line)] bg-white/50 transition-colors hover:bg-white"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button
                                @click="startMatch"
                                class="relative w-full overflow-hidden rounded-xl bg-[color:var(--accent)] py-4 font-['Space_Grotesk'] font-bold text-white shadow-lg transition-all hover:bg-[color:var(--accent-strong)] hover:shadow-xl active:scale-[0.98]"
                            >
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    Start Match
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M5 12h14" />
                                        <path d="m12 5 7 7-7 7" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
