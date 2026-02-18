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
            class="pointer-events-none fixed inset-0 z-[120]"
            role="status"
            aria-live="polite"
            aria-label="Loading"
        >
            <div class="absolute inset-0 bg-slate-900/25 backdrop-blur-[1px]"></div>

            <div class="absolute inset-x-0 top-0 h-1 w-full overflow-hidden bg-slate-200/90">
                <div class="h-full w-1/3 bg-portal-navy loading-bar"></div>
            </div>

            <div
                class="absolute inset-0 flex items-center justify-center px-4"
            >
                <div
                    class="inline-flex items-center gap-3 rounded-2xl border border-slate-300 bg-white/98 px-5 py-3 text-sm font-semibold uppercase tracking-wide text-slate-800 shadow-2xl"
                >
                    <svg
                        class="h-6 w-6 animate-spin text-portal-navy"
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
                            class="opacity-90"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        />
                    </svg>
                    <span>Loading...</span>
                </div>
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
