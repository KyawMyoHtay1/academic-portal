<script setup>
const props = defineProps({
    /** 'nav' (default) or 'footer' – affects wrapper size */
    variant: { type: String, default: "nav" },
    /** Extra class for the wrapper */
    class: { type: String, default: "" },
});

// Unique id per instance so gradients/filters don't clash when multiple logos on page
const uid = "pl-" + Math.random().toString(36).slice(2, 10);
</script>

<template>
    <span
        class="portal-logo inline-flex shrink-0 items-center justify-center overflow-hidden rounded-xl"
        :class="[
            props.variant === 'footer' ? 'h-11 w-11' : 'h-10 w-10',
            props.class,
        ]"
        role="img"
        aria-label="University Portal"
    >
        <svg
            class="h-full w-full"
            viewBox="0 0 48 48"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <defs>
                <linearGradient
                    :id="`portal-bg-${uid}`"
                    x1="0"
                    y1="0"
                    x2="1"
                    y2="1"
                    gradientUnits="objectBoundingBox"
                >
                    <stop offset="0%" stop-color="#0b1f3a" />
                    <stop offset="100%" stop-color="#162d4d" />
                </linearGradient>
                <linearGradient
                    :id="`portal-gold-${uid}`"
                    x1="0"
                    y1="0"
                    x2="0"
                    y2="1"
                    gradientUnits="objectBoundingBox"
                >
                    <stop offset="0%" stop-color="#ffd54f" />
                    <stop offset="50%" stop-color="#f4b400" />
                    <stop offset="100%" stop-color="#e0a000" />
                </linearGradient>
                <filter
                    :id="`portal-shadow-${uid}`"
                    x="-20%"
                    y="-20%"
                    width="140%"
                    height="140%"
                >
                    <feDropShadow
                        dx="0"
                        dy="2"
                        stdDeviation="1.5"
                        flood-color="#0b1f3a"
                        flood-opacity="0.35"
                    />
                </filter>
            </defs>
            <rect
                width="48"
                height="48"
                rx="12"
                ry="12"
                :fill="`url(#portal-bg-${uid})`"
                :filter="`url(#portal-shadow-${uid})`"
            />
            <path
                d="M24 13v24"
                :stroke="`url(#portal-gold-${uid})`"
                stroke-width="1.6"
                stroke-linecap="round"
            />
            <path
                d="M24 15 Q16 18 16 26 Q16 34 24 34 Q32 34 32 26 Q32 18 24 15Z"
                :fill="`url(#portal-gold-${uid})`"
                fill-opacity="0.92"
            />
            <path
                d="M24 7.5 L25.4 10.2 L28.4 10.6 L26.2 12.8 L26.8 15.8 L24 14.2 L21.2 15.8 L21.8 12.8 L19.6 10.6 L22.6 10.2 Z"
                :fill="`url(#portal-gold-${uid})`"
            />
        </svg>
    </span>
</template>
