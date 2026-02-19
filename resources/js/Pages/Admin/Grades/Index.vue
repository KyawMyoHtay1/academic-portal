<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { computed, onBeforeUnmount, ref, watch } from "vue";

const props = defineProps({
    subjects: {
        type: Array,
        required: true,
    },
});

const initialSearch =
    typeof window !== "undefined"
        ? new URLSearchParams(window.location.search).get("search") ?? ""
        : "";

const searchInput = ref(initialSearch);
const query = ref(initialSearch);

const applySearch = debounce(() => {
    query.value = searchInput.value.trim();
}, 250);

const syncUrlQuery = debounce(() => {
    if (typeof window === "undefined") return;

    const params = new URLSearchParams(window.location.search);

    if (query.value) {
        params.set("search", query.value);
    } else {
        params.delete("search");
    }

    const nextQuery = params.toString();
    const nextUrl = nextQuery
        ? `${window.location.pathname}?${nextQuery}`
        : window.location.pathname;

    window.history.replaceState({}, "", nextUrl);
}, 200);

watch(searchInput, () => {
    applySearch();
});

watch(
    query,
    () => {
        syncUrlQuery();
    },
    { immediate: true }
);

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();

    if (!q) {
        return props.subjects;
    }

    return (props.subjects || []).filter((s) => {
        const hay = `${s.subject_code} ${s.title} ${s.course_code ?? ""} ${s.course_title ?? ""}`.toLowerCase();

        return hay.includes(q);
    });
});

const hasActiveFilters = computed(() => query.value.trim() !== "");

const activeFilterChips = computed(() => {
    const chips = [];

    if (query.value.trim() !== "") {
        chips.push({
            key: "search",
            label: `Search: ${query.value.trim()}`,
        });
    }

    return chips;
});

const clearAllFilters = () => {
    searchInput.value = "";
    query.value = "";
};

const removeFilterChip = (key) => {
    if (key === "search") {
        searchInput.value = "";
        query.value = "";
    }
};

onBeforeUnmount(() => {
    applySearch.cancel();
    syncUrlQuery.cancel();
});
</script>

<template>
    <Head title="Grade Reviews" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Grade Review & Approval
                </h2>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Admin' }, { label: 'Grade Reviews' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Pending submissions
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Review and approve or reject teacher-submitted grades.
                            </p>
                        </div>
                        <div class="w-full sm:w-80">
                            <label class="block text-xs font-medium text-slate-600">Search</label>
                            <input
                                v-model="searchInput"
                                type="search"
                                placeholder="Search subject / course..."
                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>
                    </div>

                    <div
                        v-if="hasActiveFilters"
                        class="mb-4 flex flex-wrap items-center gap-2"
                    >
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Active filters:
                        </span>
                        <button
                            v-for="chip in activeFilterChips"
                            :key="chip.key"
                            type="button"
                            class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200"
                            @click="removeFilterChip(chip.key)"
                        >
                            {{ chip.label }}
                            <span aria-hidden="true">x</span>
                        </button>
                        <button
                            type="button"
                            class="rounded-md border border-slate-300 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                            @click="clearAllFilters"
                        >
                            Clear all filters
                        </button>
                    </div>

                    <div v-if="filtered.length === 0" class="rounded-md border border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-600">
                        No pending grade submissions found.
                    </div>

                    <div v-else class="overflow-hidden rounded-md border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Subject
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Course
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Pending
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-for="s in filtered" :key="s.id" class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm text-slate-900">
                                        <div class="font-medium">{{ s.subject_code }}</div>
                                        <div class="text-xs text-slate-500">{{ s.title }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        <div class="font-medium">{{ s.course_code }}</div>
                                        <div class="text-xs text-slate-500">{{ s.course_title }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                            {{ s.pending_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <Link
                                            :href="route('admin.grades.show', s.id)"
                                            class="inline-flex items-center rounded-md bg-portal-navy px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-portal-navy-dark"
                                        >
                                            Review
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
