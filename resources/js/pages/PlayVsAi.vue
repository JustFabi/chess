<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const sideOptions = [
    { label: 'White', value: 'white' },
    { label: 'Random', value: 'random' },
    { label: 'Black', value: 'black' },
];

const timeControlOptions = [
    { label: '3+2 blitz', value: '3+2' },
    { label: '5+0 blitz', value: '5+0' },
    { label: '10+5 rapid', value: '10+5' },
    { label: '15+10 rapid', value: '15+10' },
];

const selectedSide = ref('random');
const selectedTimeControl = ref('10+5');

const optionBase =
    'rounded-lg border border-[color:var(--line)] px-3 py-2 text-sm font-semibold';
const optionClass = (isActive) => [
    optionBase,
    isActive
        ? 'bg-[var(--accent-soft)] text-[color:var(--accent-strong)]'
        : 'bg-white/70 text-[color:var(--ink)]',
];

const selectedSideLabel = computed(
    () =>
        sideOptions.find((option) => option.value === selectedSide.value)
            ?.label ?? 'Random',
);

const selectedTimeControlLabel = computed(
    () =>
        timeControlOptions.find(
            (option) => option.value === selectedTimeControl.value,
        )?.label ?? '10+5 rapid',
);

const startMatch = () => {
    router.post('/play-vs-ai/match', {
        side: selectedSide.value,
        timeControl: selectedTimeControl.value,
        variant: 'standard',
    });
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
            --glow: rgba(15, 118, 110, 0.22);d
            --glow-2: rgba(56, 189, 248, 0.18);
            --board-dark: #121a1a;
            --board-light: #f0ede6;
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

        <div
            class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col px-6 pb-12 pt-8 sm:pt-12 lg:px-8"
        >
            <header
                class="flex items-center justify-between motion-safe:animate-in motion-safe:fade-in-0 motion-safe:slide-in-from-top-4 motion-safe:duration-700"
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
                            Play vs AI
                        </p>
                        <p
                            class="font-['Space_Grotesk'] text-lg font-semibold"
                        >
                            Echelon
                        </p>
                    </div>
                </div>
                <div class="hidden items-center gap-3 md:flex">
                    <span
                        class="rounded-full border border-[color:var(--line)] bg-[var(--panel)] px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                    >
                        Guest mode
                    </span>
                    <Link
                        href="/dashboard"
                        class="rounded-xl border border-[color:var(--line)] bg-[var(--panel)] px-4 py-2 text-sm font-semibold text-[color:var(--ink)]"
                    >
                        Back to lobby
                    </Link>
                </div>
            </header>

            <main
                class="mt-10 grid flex-1 gap-8 lg:grid-cols-[1.1fr_0.9fr]"
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
                                    Board preview
                                </p>
                                <h2
                                    class="mt-2 font-['Space_Grotesk'] text-2xl font-semibold"
                                >
                                    {{ selectedTimeControlLabel }}
                                </h2>
                            </div>
                            <span
                                class="rounded-full bg-[var(--accent-soft)] px-3 py-1 text-xs font-semibold text-[color:var(--accent-strong)]"
                            >
                                Ready
                            </span>
                        </div>
                        <div class="mt-6 grid gap-5 sm:grid-cols-[1.1fr_0.9fr]">
                            <div
                                class="aspect-square rounded-2xl border border-[color:var(--line)] p-4"
                            >
                                <div
                                    class="relative h-full w-full overflow-hidden rounded-xl"
                                    style="
                                        background-color: var(--board-light);
                                        background-image: linear-gradient(
                                                45deg,
                                                var(--board-dark) 25%,
                                                transparent 25%
                                            ),
                                            linear-gradient(
                                                -45deg,
                                                var(--board-dark) 25%,
                                                transparent 25%
                                            ),
                                            linear-gradient(
                                                45deg,
                                                transparent 75%,
                                                var(--board-dark) 75%
                                            ),
                                            linear-gradient(
                                                -45deg,
                                                transparent 75%,
                                                var(--board-dark) 75%
                                            );
                                        background-size: 56px 56px;
                                        background-position: 0 0, 0 28px,
                                            28px -28px, -28px 0;
                                    "
                                >
                                    <div
                                        class="absolute left-[18%] top-[20%] size-6 rounded-full bg-white/90 shadow-[0_10px_20px_-12px_rgba(15,23,42,0.7)]"
                                    ></div>
                                    <div
                                        class="absolute left-[46%] top-[36%] size-5 rounded-full bg-white/80 shadow-[0_8px_18px_-12px_rgba(15,23,42,0.6)]"
                                    ></div>
                                    <div
                                        class="absolute bottom-[24%] right-[22%] size-5 rounded-full bg-[var(--accent)] shadow-[0_10px_20px_-12px_rgba(15,118,110,0.7)]"
                                    ></div>
                                    <div
                                        class="absolute inset-0 bg-gradient-to-tr from-white/10 via-transparent to-white/30"
                                    ></div>
                                    <div
                                        class="absolute inset-0 ring-1 ring-inset ring-white/30"
                                    ></div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div
                                    class="mt-4 rounded-2xl border border-[color:var(--line)] bg-white/60 p-4"
                                >
                                    <p
                                        class="text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                                    >
                                        You
                                    </p>
                                    <p
                                        class="mt-2 font-['Space_Grotesk'] text-lg font-semibold"
                                    >
                                        Guest
                                    </p>
                                    <p
                                        class="text-xs text-[color:var(--muted)]"
                                    >
                                        White pieces, bottom side.
                                    </p>
                                </div>
                                <div
                                    class="rounded-2xl border border-[color:var(--line)] bg-white/60 p-4"
                                >
                                    <p
                                        class="text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                                    >
                                        Time control
                                    </p>
                                    <p
                                        class="mt-2 font-['Space_Grotesk'] text-lg font-semibold"
                                    >
                                        {{ selectedTimeControlLabel }}
                                    </p>
                                    <p
                                        class="text-xs text-[color:var(--muted)]"
                                    >
                                        Increment per move.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

                <section
                    class="space-y-6 motion-safe:animate-in motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-6 motion-safe:duration-700"
                    style="animation-delay: 160ms"
                >
                    <div
                        class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6"
                    >
                        <p
                            class="text-[11px] uppercase tracking-[0.3em] text-[color:var(--muted)]"
                        >
                            Game setup
                        </p>
                        <h3
                            class="mt-2 font-['Space_Grotesk'] text-xl font-semibold"
                        >
                            Match settings
                        </h3>
                        <div class="mt-5 space-y-5 text-sm">
                            <div>
                                <p
                                    class="text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                                >
                                    Side
                                </p>
                                <div class="mt-2 grid grid-cols-3 gap-2">
                                    <button
                                        v-for="option in sideOptions"
                                        :key="option.value"
                                        type="button"
                                        :class="
                                            optionClass(
                                                selectedSide === option.value,
                                            )
                                        "
                                        @click="selectedSide = option.value"
                                    >
                                        {{ option.label }}
                                    </button>
                                </div>
                            </div>

                            <div>
                                <p
                                    class="text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                                >
                                    Time control
                                </p>
                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    <button
                                        v-for="option in timeControlOptions"
                                        :key="option.value"
                                        type="button"
                                        :class="
                                            optionClass(
                                                selectedTimeControl ===
                                                    option.value,
                                            )
                                        "
                                        @click="
                                            selectedTimeControl = option.value
                                        "
                                    >
                                        {{ option.label }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6"
                    >
                        <p
                            class="text-[11px] uppercase tracking-[0.3em] text-[color:var(--muted)]"
                        >
                            Match summary
                        </p>
                        <h3
                            class="mt-2 font-['Space_Grotesk'] text-xl font-semibold"
                        >
                            Ready to start
                        </h3>
                        <div class="mt-4 grid gap-3 text-sm">
                            <div
                                class="flex items-center justify-between rounded-2xl border border-[color:var(--line)] bg-white/70 px-4 py-3"
                            >
                                <span>Variant</span>
                                <span class="font-semibold text-[color:var(--ink)]"
                                    >Standard</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between rounded-2xl border border-[color:var(--line)] bg-white/70 px-4 py-3"
                            >
                                <span>Side</span>
                                <span class="font-semibold text-[color:var(--ink)]"
                                    >{{ selectedSideLabel }}</span
                                >
                            </div>
                        </div>
                        <button
                            type="button"
                            class="mt-6 block w-full rounded-xl bg-[var(--accent)] px-5 py-3 text-center text-sm font-semibold text-white shadow-[0_16px_35px_-24px_rgba(15,118,110,0.7)]"
                            @click="startMatch"
                        >
                            Start match
                        </button>
                        <div
                            class="mt-3 flex items-center justify-between text-xs text-[color:var(--muted)]"
                        >
                            <span>Estimated length</span>
                            <span>18-24 min</span>
                        </div>
                    </div>
                </section>
            </main>

            <footer
                class="mt-10 flex flex-wrap items-center justify-between gap-3 text-xs text-[color:var(--muted)]"
            >
                <p>Guest session. Results are not saved.</p>
                <div class="flex flex-wrap items-center gap-4">
                    <span>Standard rules</span>
                    <span>{{ selectedTimeControlLabel }}</span>
                    <span>Balanced AI</span>
                </div>
            </footer>
        </div>
    </div>
</template>
