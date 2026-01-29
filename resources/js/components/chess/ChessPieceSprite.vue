<template>
    <svg
        aria-hidden="true"
        focusable="false"
        class="absolute h-0 w-0 overflow-hidden"
    >
        <symbol id="piece-king" viewBox="0 0 45 45">
            <g
                fill="currentColor"
                stroke="var(--piece-stroke)"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M22.5 6V11M20 8.5h5" stroke-width="2" fill="none" />

                <path
                    d="M22.5 13c-2 0-3 1-3 2.5 0 2 2.5 6.5 2.5 9.5-6 0-9.5 2-10.5 5-1 3 1.5 5.5 3 7.5-3 1.5-3 4.5-3 4.5h22s0-3-3-4.5c1.5-2 4-4.5 3-7.5-1-3-4.5-5-10.5-5 0-3 2.5-7.5 2.5-9.5 0-1.5-1-2.5-3-2.5z"
                />

                <path
                    d="M11.5 33c5.5-3 15.5-3 21 0m-21 3.5c5.5-3 15.5-3 21 0"
                    fill="none"
                />
            </g>
        </symbol>

        <symbol id="piece-queen" viewBox="0 0 45 45">
            <g
                fill="currentColor"
                stroke="var(--piece-stroke)"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <circle cx="6" cy="12" r="2.75" />
                <circle cx="14" cy="9" r="2.75" />
                <circle cx="22.5" cy="8" r="2.75" />
                <circle cx="31" cy="9" r="2.75" />
                <circle cx="39" cy="12" r="2.75" />
                <path
                    d="M9 26c8.5-1.5 21-1.5 27 0l2-12-7 11V11l-5.5 13.5-3-15-3 15-5.5-13.5V25l-7-11 2 12z"
                    stroke-linecap="butt"
                />
                <path
                    d="M9 26c0 2 1.5 2 2.5 4 1 2.5 3 4.5 3 4.5s0-3 3-3c3 0 5 3 5 3s2-3 5-3c3 0 3 3 3 3s2-2 3-4.5c1-2 2.5-2 2.5-4"
                    stroke-linecap="butt"
                />
                <path
                    d="M11.5 30c3.5-1 18.5-1 22 0M12 33.5c6-1 15-1 21 0"
                    fill="none"
                />
                <path
                    d="M11 37c3.5 1 21.5 1 23 0v-3.5c-3.5-1-19.5-1-23 0V37z"
                />
            </g>
        </symbol>

        <symbol id="piece-rook" viewBox="0 0 45 45">
            <g
                fill="currentColor"
                stroke="var(--piece-stroke)"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path
                    d="M9 39h27v-3H9v3zM12.5 32l1.5-2.5h17l1.5 2.5h-20zM12 36v-4h21v4H12z"
                    stroke-linecap="butt"
                />
                <path
                    d="M14 29.5v-13h17v13"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                />
                <path d="M14 16.5L11 14h23l-3 2.5H14z" stroke-linecap="butt" />
                <path
                    d="M11 14V9h4v2h5V9h5v2h5V9h4v5"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                />
                <path
                    d="M12 35.5h21M13 31.5h19M14 29.5h17M14 16.5h17M11 14h23"
                    fill="none"
                    stroke="var(--piece-stroke)"
                    stroke-width="1"
                    stroke-linejoin="miter"
                />
            </g>
        </symbol>

        <symbol id="piece-bishop" viewBox="0 0 45 45">
            <g
                fill="currentColor"
                stroke="var(--piece-stroke)"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <g fill="currentColor" stroke-linecap="butt">
                    <path
                        d="M9 36c3.39-.97 9.11-1.45 13.5-1.45 4.38 0 10.11.48 13.5 1.45"
                    />
                    <path
                        d="M15 32c2.5 2.5 12.5 2.5 15 0 .5-1.5 0-2 0-2 0-2.5-2.5-4-2.5-4 5.5-1.5 6-11.5-5-15.5-11 4-10.5 14-5 15.5 0 0-2.5 1.5-2.5 4 0 0-.5.5 0 2z"
                    />
                    <path d="M25 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                </g>
                <path
                    d="M17.5 26h10M15 30h15m-7.5-14.5v5M20 18l5-3"
                    fill="none"
                    stroke="var(--piece-stroke)"
                    stroke-linejoin="miter"
                />
            </g>
        </symbol>

        <symbol id="piece-knight" viewBox="0 0 45 45">
            <g
                fill="currentColor"
                stroke="var(--piece-stroke)"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path
                    d="M 22,10 C 32.5,11 38.5,18 38,39 L 15,39 C 15,30 25,32.5 23,18"
                />
                <path
                    d="M 24,18 C 24.38,20.91 18.45,25.37 16,27 C 13,29 13.18,31.34 11,31 C 9.958,30.06 12.41,27.96 11,28 C 10,28 11.19,25.06 10,25 C 9,25 5.979,28.26 6,24 C 6.5,21.5 9.5,22.5 9,20 C 8,19 7.5,19.5 7.5,14.5 C 7.5,12 10,6 16,6 C 18,6 23,4 24,3 C 25,2 25.5,2 25.5,2 L 23,6 C 24,6 27.25,8 24,8 C 23.25,8 24,9 24,10 V 11 C 24.25,12 24.5,13 25,13 C 26,14 28,14.5 30,14 C 32,13.5 33,14.5 33,18 C 33,21.5 31.5,24 32.5,24.5 C 33.5,25 35.5,25.5 36,27.5 C 36.5,29.5 36,32.5 36,32.5 C 36,32.5 37.5,32 38,39"
                />
                <path
                    d="M 9.5 25.5 A 0.5 0.5 0 1 1 8.5,25.5 A 0.5 0.5 0 1 1 9.5 25.5 z"
                    fill="var(--piece-stroke)"
                    stroke="none"
                />
                <path
                    d="M 15 15.5 A 0.5 1.5 0 1 1 14,15.5 A 0.5 1.5 0 1 1 15 15.5 z"
                    transform="matrix(0.866,0.5,-0.5,0.866,9.693,-5.173)"
                    fill="var(--piece-stroke)"
                    stroke="none"
                />
            </g>
        </symbol>

        <symbol id="piece-pawn" viewBox="0 0 45 45">
            <g
                fill="none"
                fill-rule="evenodd"
                stroke="var(--piece-stroke)"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path
                    d="M22.5 9c-2.21 0-4 1.79-4 4 0 .89.29 1.71.78 2.38C17.33 16.5 16 18.59 16 21c0 2.03.94 3.84 2.41 5.03-3 1.06-7.41 5.55-7.41 13.47h23c0-7.92-4.41-12.41-7.41-13.47 1.47-1.19 2.41-3 2.41-5.03 0-2.41-1.33-4.5-3.28-5.62.49-.67.78-1.49.78-2.38 0-2.21-1.79-4-4-4z"
                    fill="currentColor"
                />
            </g>
        </symbol>
    </svg>
</template>
