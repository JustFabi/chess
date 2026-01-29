<template>
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
                <path d="M24 40c0-7 5-11 8-17 3 6 8 10 8 17l-2 8H26z" />
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
                <path d="M22 50c0-12 5-22 16-28l8-5 8 8-6 8 6 6-6 11H22z" />
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
</template>
