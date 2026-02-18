<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";
import { router } from "@inertiajs/vue3";

const isLoading = ref(false);

const SHOW_DELAY_MS = 120;
let showTimer = null;
let activeRequests = 0;
let removeListeners = [];

const beginLoading = () => {
    activeRequests += 1;

    if (showTimer) {
        clearTimeout(showTimer);
    }

    showTimer = setTimeout(() => {
        if (activeRequests > 0) {
            isLoading.value = true;
        }
    }, SHOW_DELAY_MS);
};

const endLoading = () => {
    activeRequests = Math.max(0, activeRequests - 1);

    if (activeRequests > 0) {
        return;
    }

    if (showTimer) {
        clearTimeout(showTimer);
        showTimer = null;
    }

    isLoading.value = false;
};

onMounted(() => {
    removeListeners = [
        router.on("start", beginLoading),
        router.on("finish", endLoading),
        router.on("error", endLoading),
        router.on("cancel", endLoading),
        router.on("invalid", endLoading),
    ];
});

onBeforeUnmount(() => {
    if (showTimer) {
        clearTimeout(showTimer);
    }

    removeListeners.forEach((removeListener) => {
        if (typeof removeListener === "function") {
            removeListener();
        }
    });
});
</script>

<template>
    <transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isLoading"
            class="pointer-events-none fixed inset-x-0 top-0 z-[120]"
            role="status"
            aria-live="polite"
            aria-label="Loading"
        >
            <div class="h-0.5 w-full overflow-hidden bg-slate-200/80">
                <div class="h-full w-1/3 bg-portal-navy loading-bar"></div>
            </div>

            <div
                class="absolute right-4 top-3 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/95 px-3 py-1 text-xs font-semibold text-slate-700 shadow-md"
            >
                <svg
                    class="h-3.5 w-3.5 animate-spin text-portal-navy"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    />
                </svg>
                Loading...
            </div>
        </div>
    </transition>
</template>

<style scoped>
.loading-bar {
    animation: portal-loading-slide 1.1s ease-in-out infinite;
}

@keyframes portal-loading-slide {
    0% {
        transform: translateX(-120%);
    }
    100% {
        transform: translateX(360%);
    }
}
</style>
