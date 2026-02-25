<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { computed, onBeforeUnmount, ref, watch } from "vue";

const props = defineProps({
    notifications: {
        type: Array,
        required: true,
    },
});

const tabs = [
    { key: "all", label: "All" },
    { key: "unread", label: "Unread" },
    { key: "message", label: "Messages" },
    { key: "assignment", label: "Assignments" },
    { key: "announcement", label: "Announcements" },
    { key: "contact", label: "Contact" },
    { key: "feedback", label: "Feedback" },
    { key: "grade", label: "Grades" },
    { key: "grade_review", label: "Grade Review" },
    { key: "fee", label: "Fees" },
    { key: "attendance", label: "Attendance" },
    { key: "timetable", label: "Timetable" },
];

const queryParam = (key) => {
    if (typeof window === "undefined") return null;
    return new URLSearchParams(window.location.search).get(key);
};

const allowedTabs = new Set(tabs.map((tab) => tab.key));

const activeTab = ref(
    allowedTabs.has(queryParam("tab") || "") ? queryParam("tab") : "all"
);
const searchInput = ref(queryParam("search") ?? "");
const query = ref(searchInput.value.trim());

const isUnread = (n) => !n.read_at;

const stats = computed(() => {
    const list = props.notifications ?? [];
    return {
        total: list.length,
        unread: list.filter(isUnread).length,
        message: list.filter((n) => n.type === "message").length,
        grade: list.filter((n) => n.type === "grade").length,
        gradeReview: list.filter((n) => n.type === "grade_review").length,
        fee: list.filter((n) => n.type === "fee").length,
        attendance: list.filter((n) => n.type === "attendance").length,
        timetable: list.filter((n) => n.type === "timetable").length,
    };
});

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = [...(props.notifications ?? [])];

    if (activeTab.value === "unread") list = list.filter(isUnread);
    if (
        [
            "message",
            "assignment",
            "announcement",
            "contact",
            "feedback",
            "grade",
            "grade_review",
            "fee",
            "attendance",
            "timetable",
        ].includes(
            activeTab.value
        )
    ) {
        list = list.filter((n) => n.type === activeTab.value);
    }

    if (q) {
        list = list.filter((n) => {
            const title = (n.title ?? "").toLowerCase();
            const message = (n.message ?? "").toLowerCase();
            const type = (n.type ?? "").toLowerCase();
            return title.includes(q) || message.includes(q) || type.includes(q);
        });
    }

    return list;
});

const hasUnread = computed(() =>
    (props.notifications ?? []).some((n) => !n.read_at)
);

const hasActiveFilters = computed(
    () => activeTab.value !== "all" || query.value.trim() !== ""
);

const activeFilterChips = computed(() => {
    const chips = [];

    if (activeTab.value !== "all") {
        chips.push({
            key: "tab",
            label: `Tab: ${
                tabs.find((tab) => tab.key === activeTab.value)?.label ??
                activeTab.value
            }`,
        });
    }

    if (query.value.trim() !== "") {
        chips.push({
            key: "search",
            label: `Search: ${query.value.trim()}`,
        });
    }

    return chips;
});

const applySearch = debounce(() => {
    query.value = searchInput.value.trim();
}, 250);

const persistFiltersToUrl = debounce(() => {
    if (typeof window === "undefined") return;

    const url = new URL(window.location.href);
    const params = url.searchParams;

    if (activeTab.value !== "all") {
        params.set("tab", activeTab.value);
    } else {
        params.delete("tab");
    }

    if (query.value.trim() !== "") {
        params.set("search", query.value.trim());
    } else {
        params.delete("search");
    }

    const queryString = params.toString();
    window.history.replaceState(
        {},
        "",
        queryString ? `${url.pathname}?${queryString}` : url.pathname
    );
}, 200);

watch(searchInput, () => {
    applySearch();
});

watch([query, activeTab], () => {
    persistFiltersToUrl();
});

onBeforeUnmount(() => {
    applySearch.cancel();
    persistFiltersToUrl.cancel();
});

const markAsRead = (id) => {
    router.post(route("notifications.read", id), {}, { preserveScroll: true });
};

const markAllAsRead = () => {
    router.post(route("notifications.read-all"), {}, { preserveScroll: true });
};

const clearSearch = () => {
    searchInput.value = "";
    query.value = "";
};

const clearAllFilters = () => {
    activeTab.value = "all";
    clearSearch();
};

const removeFilterChip = (key) => {
    if (key === "tab") {
        activeTab.value = "all";
        return;
    }

    if (key === "search") {
        clearSearch();
    }
};

const typeBadgeClass = (type) => {
    if (type === "message") return "bg-cyan-100 text-cyan-800";
    if (type === "assignment") return "bg-teal-100 text-teal-800";
    if (type === "announcement") return "bg-fuchsia-100 text-fuchsia-800";
    if (type === "contact") return "bg-orange-100 text-orange-800";
    if (type === "feedback") return "bg-rose-100 text-rose-800";
    if (type === "grade") return "bg-emerald-100 text-emerald-800";
    if (type === "grade_review") return "bg-violet-100 text-violet-800";
    if (type === "fee") return "bg-blue-100 text-blue-800";
    if (type === "attendance") return "bg-amber-100 text-amber-800";
    if (type === "timetable") return "bg-indigo-100 text-indigo-800";
    return "bg-slate-100 text-slate-700";
};

const iconWrapperClass = (type) => {
    if (type === "message") return "bg-cyan-100";
    if (type === "assignment") return "bg-teal-100";
    if (type === "announcement") return "bg-fuchsia-100";
    if (type === "contact") return "bg-orange-100";
    if (type === "feedback") return "bg-rose-100";
    if (type === "grade") return "bg-emerald-100";
    if (type === "grade_review") return "bg-violet-100";
    if (type === "fee") return "bg-blue-100";
    if (type === "attendance") return "bg-amber-100";
    if (type === "timetable") return "bg-indigo-100";
    return "bg-slate-100";
};

const iconClass = (type) => {
    if (type === "message") return "text-cyan-600";
    if (type === "assignment") return "text-teal-600";
    if (type === "announcement") return "text-fuchsia-600";
    if (type === "contact") return "text-orange-600";
    if (type === "feedback") return "text-rose-600";
    if (type === "grade") return "text-emerald-600";
    if (type === "grade_review") return "text-violet-600";
    if (type === "fee") return "text-blue-600";
    if (type === "attendance") return "text-amber-600";
    if (type === "timetable") return "text-indigo-600";
    return "text-slate-600";
};
</script>

<template>
    <Head title="Notifications" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">Notifications</h2>
                <button
                    v-if="hasUnread"
                    @click="markAllAsRead"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-portal-navy-dark"
                >
                    Mark all as read
                </button>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Notifications' }]" />
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
                <div class="rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-indigo-700">Notification center</p>
                    <h3 class="mt-2 text-2xl font-semibold text-indigo-950">Track academic updates in one place</h3>
                    <p class="mt-2 text-sm text-indigo-900/80">Review unread alerts and quickly clear items after checking grades, fees, and attendance notices.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-8">
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Total</p>
                        <p class="mt-2 text-2xl font-bold text-blue-900">{{ stats.total }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Unread</p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">{{ stats.unread }}</p>
                    </div>
                    <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Messages</p>
                        <p class="mt-2 text-2xl font-bold text-cyan-900">{{ stats.message }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Grades</p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">{{ stats.grade }}</p>
                    </div>
                    <div class="rounded-xl border border-violet-200 bg-violet-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Grade Review</p>
                        <p class="mt-2 text-2xl font-bold text-violet-900">{{ stats.gradeReview }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-200 bg-blue-50/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Fees</p>
                        <p class="mt-2 text-2xl font-bold text-blue-900">{{ stats.fee }}</p>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Attendance</p>
                        <p class="mt-2 text-2xl font-bold text-amber-900">{{ stats.attendance }}</p>
                    </div>
                    <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Timetable</p>
                        <p class="mt-2 text-2xl font-bold text-indigo-900">{{ stats.timetable }}</p>
                    </div>
                </div>

                <div class="portal-card p-4">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="t in tabs"
                                :key="t.key"
                                type="button"
                                class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-slate-200 transition"
                                :class="[
                                    activeTab === t.key
                                        ? 'bg-portal-navy text-white ring-portal-navy'
                                        : 'bg-white text-slate-700 hover:bg-slate-50',
                                ]"
                                @click="activeTab = t.key"
                            >
                                {{ t.label }}
                            </button>
                        </div>

                        <div class="flex w-full items-center gap-2 md:w-auto">
                            <div class="relative w-full md:w-80">
                                <input
                                    v-model="searchInput"
                                    type="text"
                                    placeholder="Search notifications"
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                                <button
                                    v-if="searchInput"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="clearSearch"
                                >
                                    <span class="sr-only">Clear</span>
                                    X
                                </button>
                            </div>
                            <button
                                v-if="hasActiveFilters"
                                type="button"
                                class="whitespace-nowrap rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                @click="clearAllFilters"
                            >
                                Clear all filters
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="activeFilterChips.length > 0"
                        class="mt-3 flex flex-wrap gap-2"
                    >
                        <span
                            v-for="chip in activeFilterChips"
                            :key="chip.key"
                            class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"
                        >
                            {{ chip.label }}
                            <button
                                type="button"
                                class="rounded px-1 text-slate-500 hover:bg-slate-200"
                                @click="removeFilterChip(chip.key)"
                            >
                                x
                            </button>
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div v-if="filtered.length === 0" class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
                        <h3 class="text-sm font-semibold text-slate-900">No notifications found</h3>
                        <p class="mt-1 text-sm text-slate-500">Try changing your tab or search query.</p>
                    </div>

                    <div
                        v-for="notification in filtered"
                        :key="notification.id"
                        class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md"
                        :class="{ 'ring-1 ring-emerald-200': !notification.read_at }"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 flex-1 items-start gap-3">
                                <div class="mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full" :class="iconWrapperClass(notification.type)">
                                    <svg class="h-5 w-5" :class="iconClass(notification.type)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold text-slate-900">{{ notification.title }}</p>
                                        <span v-if="!notification.read_at" class="inline-flex h-2 w-2 rounded-full bg-emerald-500" title="Unread" />
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize" :class="typeBadgeClass(notification.type)">
                                            {{ notification.type }}
                                        </span>
                                    </div>

                                    <p class="mt-1 text-sm text-slate-700">{{ notification.message }}</p>
                                    <p class="mt-2 text-xs text-slate-500">{{ notification.created_at }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a
                                    v-if="notification.url"
                                    :href="notification.url"
                                    class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                >
                                    Open
                                </a>
                                <button
                                    v-if="!notification.read_at"
                                    type="button"
                                    @click="markAsRead(notification.id)"
                                    class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                                >
                                    Mark as read
                                </button>
                                <span v-else class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">Read</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
