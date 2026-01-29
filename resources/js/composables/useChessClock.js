import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { formatClockTime, parseTimeControl } from '@/lib/timeControls';

const sideToMove = (lastMove) => {
    const lastColor = lastMove?.piece?.color ?? null;
    if (!lastColor) {
        return 'white';
    }
    return lastColor === 'white' ? 'black' : 'white';
};

const normalizeClockValue = (value, fallback) => {
    const numeric = Number(value);
    if (Number.isFinite(numeric)) {
        return Math.max(0, Math.floor(numeric));
    }
    return fallback;
};

export const useChessClock = ({ clock, result, settings, lastMove }) => {
    const now = ref(Date.now());
    let intervalId = null;

    const fallbackControl = computed(() =>
        parseTimeControl(settings.value?.timeControl),
    );

    const resolvedClock = computed(() => {
        const fallback = fallbackControl.value;
        const value = clock.value ?? {};
        const active =
            value.active === 'white' || value.active === 'black'
                ? value.active
                : sideToMove(lastMove.value);
        const lastTickAtSeconds = normalizeClockValue(
            value.lastTickAt,
            Math.floor(Date.now() / 1000),
        );

        return {
            white: normalizeClockValue(value.white, fallback.baseSeconds),
            black: normalizeClockValue(value.black, fallback.baseSeconds),
            active,
            lastTickAt: lastTickAtSeconds * 1000,
        };
    });

    const elapsedSeconds = computed(() => {
        if (result.value) {
            return 0;
        }
        const elapsed = Math.floor(
            (now.value - resolvedClock.value.lastTickAt) / 1000,
        );
        return Math.max(0, elapsed);
    });

    const whiteSeconds = computed(() => {
        const base = resolvedClock.value.white;
        if (result.value) {
            return base;
        }
        if (resolvedClock.value.active !== 'white') {
            return base;
        }
        return Math.max(0, base - elapsedSeconds.value);
    });

    const blackSeconds = computed(() => {
        const base = resolvedClock.value.black;
        if (result.value) {
            return base;
        }
        if (resolvedClock.value.active !== 'black') {
            return base;
        }
        return Math.max(0, base - elapsedSeconds.value);
    });

    const tick = () => {
        if (!result.value) {
            now.value = Date.now();
        }
    };

    watch(
        () => [clock.value?.lastTickAt, clock.value?.active, result.value],
        () => {
            now.value = Date.now();
        },
        { immediate: true },
    );

    onMounted(() => {
        intervalId = setInterval(tick, 1000);
    });

    onBeforeUnmount(() => {
        if (intervalId) {
            clearInterval(intervalId);
        }
    });

    const whiteClock = computed(() => formatClockTime(whiteSeconds.value));
    const blackClock = computed(() => formatClockTime(blackSeconds.value));

    return {
        whiteClock,
        blackClock,
        whiteSeconds,
        blackSeconds,
    };
};
