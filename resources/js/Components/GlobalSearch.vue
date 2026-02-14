<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from "vue";
import { router } from "@inertiajs/vue3";

const query = ref("");
const results = ref([]);
const isLoading = ref(false);
const isOpen = ref(false);
const highlightIndex = ref(-1);
const debounceTimer = ref(null);
const debounceMs = 300;
const minQueryLength = 2;
const inputRef = ref(null);
const dropdownRef = ref(null);

const hasResults = computed(() => results.value.length > 0);
const showDropdown = computed(
    () => isOpen.value && (query.value.length >= minQueryLength || results.value.length > 0)
);
const highlightedResult = computed(() => {
    if (highlightIndex.value < 0 || highlightIndex.value >= results.value.length) {
        return null;
    }
    return results.value[highlightIndex.value];
});

const typeLabels = {
    student: "Student",
    user: "User",
    course: "Course",
    subject: "Subject",
    announcement: "Announcement",
    assignment: "Assignment",
    page: "Page",
};

function fetchResults() {
    const q = query.value.trim();
    if (q.length < minQueryLength) {
        results.value = [];
        return;
    }
    isLoading.value = true;
    fetch(`${route("search")}?q=${encodeURIComponent(q)}`, {
        method: "GET",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
    })
        .then((res) => res.json())
        .then((data) => {
            results.value = data.results ?? [];
            highlightIndex.value = -1;
        })
        .catch(() => {
            results.value = [];
        })
        .finally(() => {
            isLoading.value = false;
        });
}

function debouncedSearch() {
    if (debounceTimer.value) {
        clearTimeout(debounceTimer.value);
    }
    debounceTimer.value = setTimeout(() => {
        fetchResults();
    }, debounceMs);
}

watch(query, () => {
    if (query.value.trim().length < minQueryLength) {
        results.value = [];
        highlightIndex.value = -1;
        return;
    }
    debouncedSearch();
});

function open() {
    isOpen.value = true;
    highlightIndex.value = -1;
    nextTick(() => inputRef.value?.focus());
}

function close() {
    isOpen.value = false;
    highlightIndex.value = -1;
}

function selectResult(result) {
    if (!result?.url) return;
    router.visit(result.url);
    close();
    query.value = "";
    results.value = [];
}

function handleKeydown(e) {
    if (!showDropdown.value) return;
    if (e.key === "Escape") {
        e.preventDefault();
        close();
        inputRef.value?.blur();
        return;
    }
    if (e.key === "ArrowDown") {
        e.preventDefault();
        highlightIndex.value = Math.min(
            highlightIndex.value + 1,
            results.value.length - 1
        );
        return;
    }
    if (e.key === "ArrowUp") {
        e.preventDefault();
        highlightIndex.value = Math.max(highlightIndex.value - 1, 0);
        return;
    }
    if (e.key === "Enter" && highlightedResult.value) {
        e.preventDefault();
        selectResult(highlightedResult.value);
        return;
    }
}

function handleClickOutside(e) {
    if (
        dropdownRef.value &&
        !dropdownRef.value.contains(e.target) &&
        !inputRef.value?.contains(e.target)
    ) {
        close();
    }
}

function handleGlobalKeydown(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === "k") {
        e.preventDefault();
        open();
    }
}

onMounted(() => {
    document.addEventListener("keydown", handleGlobalKeydown);
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleGlobalKeydown);
    document.removeEventListener("click", handleClickOutside);
    if (debounceTimer.value) clearTimeout(debounceTimer.value);
});
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <div
            class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-600 transition focus-within:border-portal-navy focus-within:bg-white focus-within:ring-2 focus-within:ring-portal-navy/20 sm:w-64"
        >
            <svg
                class="h-4 w-4 shrink-0 text-slate-400"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
            </svg>
            <input
                ref="inputRef"
                v-model="query"
                type="search"
                placeholder="Search everything..."
                autocomplete="off"
                class="w-full bg-transparent text-sm outline-none placeholder:text-slate-400"
                @focus="open"
                @keydown="handleKeydown"
            />
            <kbd
                class="hidden shrink-0 rounded bg-slate-200 px-1.5 py-0.5 font-mono text-[10px] text-slate-600 sm:inline-block"
                >⌘K</kbd
            >
        </div>

        <!-- Dropdown -->
        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div
                v-show="showDropdown"
                class="absolute left-0 right-0 top-full z-50 mt-1 max-h-80 overflow-auto rounded-lg border border-slate-200 bg-white py-2 shadow-lg"
            >
                <div
                    v-if="isLoading"
                    class="px-4 py-8 text-center text-sm text-slate-500"
                >
                    Searching...
                </div>
                <div
                    v-else-if="
                        query.trim().length >= minQueryLength && !hasResults
                    "
                    class="px-4 py-8 text-center text-sm text-slate-500"
                >
                    No results found for "{{ query }}"
                </div>
                <div
                    v-else-if="hasResults"
                    class="space-y-1"
                >
                    <button
                        v-for="(result, idx) in results"
                        :key="`${result.type}-${result.id}`"
                        type="button"
                        class="flex w-full flex-col items-start gap-0.5 px-4 py-2.5 text-left transition hover:bg-slate-50"
                        :class="{
                            'bg-portal-navy/5': idx === highlightIndex,
                        }"
                        @click="selectResult(result)"
                    >
                        <div class="flex w-full items-center justify-between gap-2">
                            <span class="truncate text-sm font-medium text-slate-900">
                                {{ result.title }}
                            </span>
                            <span
                                class="shrink-0 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600"
                            >
                                {{ typeLabels[result.type] ?? result.type }}
                            </span>
                        </div>
                        <span
                            v-if="result.subtitle"
                            class="truncate text-xs text-slate-500"
                        >
                            {{ result.subtitle }}
                        </span>
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>
