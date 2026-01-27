<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Echelon Chess">
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
                            Game Lobby
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
                    <button
                        type="button"
                        class="rounded-xl bg-[var(--accent)] px-4 py-2 text-sm font-semibold text-white shadow-[0_12px_30px_-20px_rgba(15,118,110,0.6)]"
                    >
                        Create game
                    </button>
                </div>
            </header>

            <main
                class="mt-12 grid flex-1 items-center gap-10 lg:grid-cols-[1.1fr_0.9fr]"
            >
                <section
                    class="space-y-8 motion-safe:animate-in motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-4 motion-safe:duration-700"
                    style="animation-delay: 80ms"
                >
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-[color:var(--line)] bg-[var(--panel)] px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                    >
                        <span
                            class="h-2 w-2 rounded-full bg-[var(--accent)]"
                        ></span>
                        Game lobby
                    </div>

                    <div class="space-y-5">
                        <h1
                            class="font-['Space_Grotesk'] text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl"
                        >
                            Echelon Chess lobby. Choose a mode and start
                            playing.
                        </h1>
                        <p
                            class="max-w-xl text-base text-[color:var(--muted)] sm:text-lg"
                        >
                            Choose time control, mode, and side. Start a quick
                            match, play the AI, or open the analysis board.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            class="rounded-xl bg-[var(--accent)] px-5 py-3 text-sm font-semibold text-white shadow-[0_16px_35px_-24px_rgba(15,118,110,0.7)]"
                        >
                            Quick match
                        </button>
                        <Link
                            href="/play-vs-ai"
                            class="rounded-xl border border-[color:var(--line)] bg-[var(--panel)] px-5 py-3 text-sm font-semibold text-[color:var(--ink)]"
                        >
                            Play vs AI
                        </Link>
                        <span class="text-xs text-[color:var(--muted)]">
                            Guest mode enabled.
                        </span>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div
                            class="rounded-2xl border border-[color:var(--line)] bg-[var(--panel)] p-4"
                        >
                            <p
                                class="text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                            >
                                Players online
                            </p>
                            <p
                                class="mt-2 font-['Space_Grotesk'] text-2xl font-semibold"
                            >
                                128
                            </p>
                            <p class="text-xs text-[color:var(--muted)]">
                                Ready for a match.
                            </p>
                        </div>
                        <div
                            class="rounded-2xl border border-[color:var(--line)] bg-[var(--panel)] p-4"
                        >
                            <p
                                class="text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                            >
                                Games in progress
                            </p>
                            <p
                                class="mt-2 font-['Space_Grotesk'] text-2xl font-semibold"
                            >
                                42
                            </p>
                            <p class="text-xs text-[color:var(--muted)]">
                                Across all rooms.
                            </p>
                        </div>
                        <div
                            class="rounded-2xl border border-[color:var(--line)] bg-[var(--panel)] p-4"
                        >
                            <p
                                class="text-[11px] uppercase tracking-[0.25em] text-[color:var(--muted)]"
                            >
                                Open challenges
                            </p>
                            <p
                                class="mt-2 font-['Space_Grotesk'] text-2xl font-semibold"
                            >
                                16
                            </p>
                            <p class="text-xs text-[color:var(--muted)]">
                                Join or create.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div
                            class="rounded-2xl border border-[color:var(--line)] bg-[var(--panel)] p-4"
                        >
                            <p
                                class="font-['Space_Grotesk'] text-lg font-semibold"
                            >
                                Time controls
                            </p>
                            <p class="mt-1 text-sm text-[color:var(--muted)]">
                                Bullet, blitz, rapid, classical.
                            </p>
                        </div>
                        <div
                            class="rounded-2xl border border-[color:var(--line)] bg-[var(--panel)] p-4"
                        >
                            <p
                                class="font-['Space_Grotesk'] text-lg font-semibold"
                            >
                                Modes
                            </p>
                            <p class="mt-1 text-sm text-[color:var(--muted)]">
                                Rated, casual, analysis, vs AI.
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    class="space-y-6 motion-safe:animate-in motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-6 motion-safe:duration-700"
                    style="animation-delay: 160ms"
                >
                    <div
                        class="rounded-3xl border border-[color:var(--line)] bg-[var(--panel)] p-6 shadow-[0_30px_80px_-60px_rgba(15,23,42,0.6)]"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-[11px] uppercase tracking-[0.3em] text-[color:var(--muted)]"
                                >
                                    Live game
                                </p>
                                <h2
                                    class="mt-2 font-['Space_Grotesk'] text-2xl font-semibold"
                                >
                                    Table 7 - Rapid
                                </h2>
                            </div>
                            <span
                                class="rounded-full bg-[var(--accent-soft)] px-3 py-1 text-xs font-semibold text-[color:var(--accent-strong)]"
                            >
                                10+5
                            </span>
                        </div>
                        <div class="mt-6">
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
                                        class="absolute left-[20%] top-[22%] size-6 rounded-full bg-white/90 shadow-[0_10px_20px_-12px_rgba(15,23,42,0.7)]"
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
                            <div
                                class="mt-5 flex items-center justify-between text-xs text-[color:var(--muted)]"
                            >
                                <span>Current game</span>
                                <span>Move 17 - Sicilian</span>
                            </div>
                        </div>
                    </div>

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
                            Default match settings
                        </h3>
                        <ul class="mt-4 space-y-3 text-sm">
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 size-2 rounded-full bg-[var(--accent)]"
                                ></span>
                                <div>
                                    <p class="font-semibold">
                                        Mode
                                    </p>
                                    <p class="text-[color:var(--muted)]">
                                        Rated match, standard rules.
                                    </p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 size-2 rounded-full bg-[var(--accent)]"
                                ></span>
                                <div>
                                    <p class="font-semibold">
                                        Time control
                                    </p>
                                    <p class="text-[color:var(--muted)]">
                                        10 minutes with 5 second increment.
                                    </p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 size-2 rounded-full bg-[var(--accent)]"
                                ></span>
                                <div>
                                    <p class="font-semibold">Side</p>
                                    <p class="text-[color:var(--muted)]">
                                        Random color assignment.
                                    </p>
                                </div>
                            </li>
                        </ul>
                        <div
                            class="mt-5 flex items-center justify-between rounded-2xl border border-[color:var(--line)] bg-white/70 px-4 py-3 text-xs text-[color:var(--muted)]"
                        >
                            <span>Preset</span>
                            <span class="font-semibold text-[color:var(--ink)]"
                                >Rapid 10+5</span
                            >
                        </div>
                    </div>
                </section>
            </main>

            <footer
                class="mt-10 flex flex-wrap items-center justify-between gap-3 text-xs text-[color:var(--muted)]"
            >
                <p>Pick a mode, set the clock, and start the game.</p>
                <div class="flex flex-wrap items-center gap-4">
                    <span>Bullet 1+0</span>
                    <span>Blitz 5+0</span>
                    <span>Rapid 10+5</span>
                </div>
            </footer>
        </div>
    </div>
</template>
