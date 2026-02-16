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
    student: "Student record",
    user: "User account",
    course: "Course",
    subject: "Subject",
    announcement: "Announcement",
    assignment: "Assignment",
    page: "Portal page",
};

const typeIcons = {
    student: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z",
    user: "M5.121 17.804A9 9 0 1119 10.5a4.5 4.5 0 01-7.879 3.181A5.5 5.5 0 015.12 17.804z",
    course:
        "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
    subject:
        "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
    announcement:
        "M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z",
    assignment:
        "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
    page: "M8 4h8a2 2 0 012 2v12l-4-3-4 3-4-3-4 3V6a2 2 0 012-2h4z",
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
                        class="flex w-full items-start gap-3 px-4 py-2.5 text-left transition hover:bg-slate-50"
                        :class="{
                            'bg-portal-navy/5': idx === highlightIndex,
                        }"
                        @click="selectResult(result)"
                    >
                        <div class="mt-0.5">
                            <svg
                                class="h-4 w-4 text-slate-400"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    :d="typeIcons[result.type] || 'M4 6h16M4 12h16M4 18h16'"
                                />
                            </svg>
                        </div>
                        <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                            <div class="flex items-center gap-2">
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
                        </div>
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>
