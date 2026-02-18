<script setup>
import { computed, onBeforeUnmount, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const toasts = ref([]);
let nextToastId = 1;

const flash = computed(() => page.props.flash ?? {});

const durationsByType = {
    success: 5000,
    error: 7000,
    warning: 6500,
    info: 5500,
};

const toneByType = {
    success: {
        container:
            "border-emerald-200 bg-emerald-50 text-emerald-900 shadow-emerald-200/50",
        icon: "text-emerald-600",
    },
    error: {
        container: "border-red-200 bg-red-50 text-red-900 shadow-red-200/50",
        icon: "text-red-600",
    },
    warning: {
        container:
            "border-amber-200 bg-amber-50 text-amber-900 shadow-amber-200/50",
        icon: "text-amber-600",
    },
    info: {
        container:
            "border-sky-200 bg-sky-50 text-sky-900 shadow-sky-200/50",
        icon: "text-sky-600",
    },
};

const normalizeMessage = (value) => {
    if (Array.isArray(value)) {
        return value
            .map((item) => normalizeMessage(item))
            .filter(Boolean)
            .join(" ");
    }

    if (value && typeof value === "object" && "message" in value) {
        return normalizeMessage(value.message);
    }

    if (value === null || value === undefined) {
        return "";
    }

    return String(value).trim();
};

const dismissToast = (id) => {
    const index = toasts.value.findIndex((toast) => toast.id === id);
    if (index === -1) {
        return;
    }

    const [toast] = toasts.value.splice(index, 1);
    if (toast?.timer) {
        clearTimeout(toast.timer);
    }
};

const enqueueToast = (type, rawMessage) => {
    const message = normalizeMessage(rawMessage);
    if (!message) {
        return;
    }

    const signature = `${type}:${message}`;
    const alreadyVisible = toasts.value.some(
        (toast) => toast.signature === signature,
    );
    if (alreadyVisible) {
        return;
    }

    const id = nextToastId++;
    const timer = setTimeout(
        () => dismissToast(id),
        durationsByType[type] ?? 5500,
    );

    toasts.value.push({
        id,
        type,
        message,
        signature,
        timer,
    });
};

watch(
    () => [
        flash.value.success,
        flash.value.error,
        flash.value.warning,
        flash.value.info,
    ],
    ([success, error, warning, info], previousValues = []) => {
        if (success && success !== previousValues[0]) {
            enqueueToast("success", success);
        }
        if (error && error !== previousValues[1]) {
            enqueueToast("error", error);
        }
        if (warning && warning !== previousValues[2]) {
            enqueueToast("warning", warning);
        }
        if (info && info !== previousValues[3]) {
            enqueueToast("info", info);
        }
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    toasts.value.forEach((toast) => {
        if (toast.timer) {
            clearTimeout(toast.timer);
        }
    });
});
</script>

<template>
    <div
        class="pointer-events-none fixed right-4 top-4 z-[130] w-[min(92vw,28rem)] space-y-2"
        aria-live="polite"
        aria-atomic="true"
    >
        <transition-group
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
            move-class="transition duration-150 ease-in-out"
            tag="div"
            class="space-y-2"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto rounded-xl border px-4 py-3 text-sm font-medium shadow-lg"
                :class="toneByType[toast.type]?.container"
                role="status"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-start gap-2">
                        <svg
                            v-if="toast.type === 'success'"
                            class="mt-0.5 h-5 w-5 shrink-0"
                            :class="toneByType[toast.type]?.icon"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <svg
                            v-else-if="toast.type === 'error'"
                            class="mt-0.5 h-5 w-5 shrink-0"
                            :class="toneByType[toast.type]?.icon"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <svg
                            v-else-if="toast.type === 'warning'"
                            class="mt-0.5 h-5 w-5 shrink-0"
                            :class="toneByType[toast.type]?.icon"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l5.58 9.918c.75 1.334-.213 2.983-1.742 2.983H4.419c-1.53 0-2.492-1.65-1.743-2.983L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-7a1 1 0 00-1 1v4a1 1 0 102 0V7a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <svg
                            v-else
                            class="mt-0.5 h-5 w-5 shrink-0"
                            :class="toneByType[toast.type]?.icon"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-11a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1zm0 8a1.25 1.25 0 100-2.5A1.25 1.25 0 0010 15z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="min-w-0 break-words">{{ toast.message }}</span>
                    </div>

                    <button
                        type="button"
                        class="rounded-md px-1 text-slate-500 transition hover:text-slate-800"
                        @click="dismissToast(toast.id)"
                    >
                        <span class="sr-only">Dismiss notification</span>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </transition-group>
    </div>
</template>
