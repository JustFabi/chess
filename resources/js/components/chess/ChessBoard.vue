<script setup>
import { computed } from 'vue';
import ChessPieceIcon from './ChessPieceIcon.vue';

const props = defineProps({
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
});

const emit = defineEmits(['select-square']);

const highlightSet = computed(() => new Set(props.highlightSquares));
const isFlipped = computed(() => props.orientation === 'black');
const displaySquares = computed(() =>
    isFlipped.value ? [...props.boardSquares].reverse() : props.boardSquares,
);
const leftFile = computed(() => (isFlipped.value ? 'h' : 'a'));
const bottomRank = computed(() => (isFlipped.value ? 8 : 1));

const selectSquare = (square) => {
    emit('select-square', square);
};
</script>

<template>
    <div class="rounded-2xl border border-[color:var(--line)] bg-white/70 p-3">
        <div
            class="grid aspect-square grid-cols-8 grid-rows-8 overflow-hidden rounded-xl ring-1 ring-[color:var(--line)]"
        >
            <div
                v-for="square in displaySquares"
                :key="square.key"
                class="relative flex items-center justify-center"
                @click="selectSquare(square)"
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
                    v-if="selectedSquare === square.key"
                    class="absolute inset-0 ring-2 ring-[var(--accent)] ring-inset"
                ></div>
                <div
                    v-if="highlightSet.has(square.key)"
                    class="absolute h-8 w-8 rounded-full bg-white"
                ></div>
                <span
                    v-if="square.file === leftFile"
                    class="absolute top-1 left-1 text-[10px] font-semibold"
                    :class="
                        square.isDark
                            ? 'text-white/70'
                            : 'text-[color:var(--muted)]'
                    "
                >
                    {{ square.rank }}
                </span>
                <span
                    v-if="square.rank === bottomRank"
                    class="absolute right-1 bottom-1 text-[10px] font-semibold"
                    :class="
                        square.isDark
                            ? 'text-white/70'
                            : 'text-[color:var(--muted)]'
                    "
                >
                    {{ square.file }}
                </span>
                <ChessPieceIcon
                    v-if="square.piece"
                    :piece="square.piece"
                    class-name="relative z-10 h-full w-full cursor-pointer p-1.5"
                />
            </div>
        </div>
    </div>
</template>
