<script setup>
import { computed } from 'vue';

const props = defineProps({
    piece: {
        type: Object,
        required: true,
    },
    className: {
        type: String,
        default: '',
    },
});

const iconType = computed(() => {
    const rawType = props.piece?.type ?? '';
    const normalized = String(rawType).toLowerCase();
    const map = {
        p: 'pawn',
        n: 'knight',
        b: 'bishop',
        r: 'rook',
        q: 'queen',
        k: 'king',
    };

    return map[normalized] ?? normalized;
});

const pieceStyle = computed(() => ({
    color: props.piece?.color === 'white' ? '#f8f4ec' : '#1b232a',
    '--piece-stroke':
        props.piece?.color === 'white'
            ? 'rgba(15, 23, 42, 0.25)'
            : 'rgba(255, 255, 255, 0.18)',
}));

const pieceShadowClass = computed(() =>
    props.piece?.color === 'white'
        ? 'drop-shadow-[0_6px_8px_rgba(15,23,42,0.25)]'
        : 'drop-shadow-[0_6px_10px_rgba(15,23,42,0.45)]',
);
</script>

<template>
    <svg
        viewBox="0 0 64 64"
        :class="[pieceShadowClass, className]"
        :style="pieceStyle"
    >
        <use :href="`#piece-${iconType}`" />
    </svg>
</template>
