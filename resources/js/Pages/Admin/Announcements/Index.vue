<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { computed, onBeforeUnmount, ref, watch } from "vue";

const props = defineProps({
    announcements: {
        type: Array,
        required: true,
    },
});

const tabs = [
    { key: "all", label: "All" },
    { key: "pinned", label: "Pinned" },
    { key: "urgent", label: "Urgent" },
];

const queryParam = (key) => {
    if (typeof window === "undefined") return null;
    return new URLSearchParams(window.location.search).get(key);
};

const allowedTabs = new Set(tabs.map((tab) => tab.key));
const allowedSorts = new Set(["newest", "title", "engagement"]);
const allowedPriorities = new Set(["all", "urgent", "important", "info"]);
const allowedAudiences = new Set(["all", "student", "teacher", "staff"]);
const allowedDateRanges = new Set(["all", "7", "30", "90"]);

const activeTab = ref(
    allowedTabs.has(queryParam("tab") || "") ? queryParam("tab") : "all"
);
const searchInput = ref(queryParam("search") ?? "");
const query = ref(searchInput.value.trim());
const sortBy = ref(
    allowedSorts.has(queryParam("sort") || "") ? queryParam("sort") : "newest"
);
const priorityFilter = ref(
    allowedPriorities.has(queryParam("priority") || "")
        ? queryParam("priority")
        : "all"
);
const audienceFilter = ref(
    allowedAudiences.has(queryParam("audience") || "")
        ? queryParam("audience")
        : "all"
);
const dateRangeFilter = ref(
    allowedDateRanges.has(queryParam("range") || "") ? queryParam("range") : "all"
);

const stats = computed(() => {
    const list = props.announcements ?? [];
    const recipientTotal = list.reduce(
        (sum, announcement) =>
            sum + (announcement.analytics?.recipient_count ?? 0),
        0
    );
    const readTotal = list.reduce(
        (sum, announcement) => sum + (announcement.analytics?.read_count ?? 0),
        0
    );
    const ackTotal = list.reduce(
        (sum, announcement) => sum + (announcement.analytics?.ack_count ?? 0),
        0
    );

    return {
        total: list.length,
        pinned: list.filter((a) => !!a.pinned).length,
        urgent: list.filter((a) => a.priority === "urgent").length,
        ackRequired: list.filter((a) => !!a.require_ack).length,
        averageReadRate:
            recipientTotal > 0
                ? Math.round((readTotal / recipientTotal) * 100)
                : 0,
        averageAckRate:
            recipientTotal > 0
                ? Math.round((ackTotal / recipientTotal) * 100)
                : 0,
    };
});

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = [...(props.announcements ?? [])];

    if (activeTab.value === "pinned") list = list.filter((a) => !!a.pinned);
    if (activeTab.value === "urgent") {
        list = list.filter((a) => a.priority === "urgent");
    }

    if (priorityFilter.value !== "all") {
        list = list.filter((a) => a.priority === priorityFilter.value);
    }

    if (audienceFilter.value !== "all") {
        list = list.filter((a) =>
            (a.audience?.roles ?? []).includes(audienceFilter.value) ||
            (a.audience?.roles ?? []).includes("all")
        );
    }

    if (dateRangeFilter.value !== "all") {
        const days = Number(dateRangeFilter.value);
        const cutoff = new Date();
        cutoff.setDate(cutoff.getDate() - days);
        list = list.filter((a) => {
            const createdAt = new Date(a.created_at_iso || a.created_at);
            return !Number.isNaN(createdAt.getTime()) && createdAt >= cutoff;
        });
    }

    if (q) {
        list = list.filter((a) => {
            const title = (a.title ?? "").toLowerCase();
            const body = (a.body ?? "").toLowerCase();
            const author = (a.author ?? "").toLowerCase();
            const roles = (a.audience?.roles ?? []).join(" ").toLowerCase();
            return (
                title.includes(q) ||
                body.includes(q) ||
                author.includes(q) ||
                roles.includes(q)
            );
        });
    }

    if (sortBy.value === "title") {
        list.sort((a, b) =>
            String(a.title ?? "").localeCompare(String(b.title ?? ""))
        );
    } else if (sortBy.value === "engagement") {
        list.sort(
            (a, b) =>
                (b.analytics?.read_rate ?? 0) - (a.analytics?.read_rate ?? 0)
        );
    }

    return list;
});

const hasActiveFilters = computed(
    () =>
        activeTab.value !== "all" ||
        query.value.trim() !== "" ||
        sortBy.value !== "newest" ||
        priorityFilter.value !== "all" ||
        audienceFilter.value !== "all" ||
        dateRangeFilter.value !== "all"
);

const activeFilterChips = computed(() => {
    const chips = [];

    if (activeTab.value !== "all") {
        const tabLabel = tabs.find((tab) => tab.key === activeTab.value)?.label;
        chips.push({
            key: "tab",
            label: `Tab: ${tabLabel ?? activeTab.value}`,
        });
    }

    if (query.value.trim() !== "") {
        chips.push({ key: "search", label: `Search: ${query.value.trim()}` });
    }

    if (priorityFilter.value !== "all") {
        chips.push({
            key: "priority",
            label: `Priority: ${priorityFilter.value}`,
        });
    }

    if (audienceFilter.value !== "all") {
        chips.push({
            key: "audience",
            label: `Audience: ${audienceFilter.value}`,
        });
    }

    if (dateRangeFilter.value !== "all") {
        chips.push({
            key: "range",
            label: `Date: last ${dateRangeFilter.value} days`,
        });
    }

    if (sortBy.value !== "newest") {
        const sortLabel =
            sortBy.value === "title" ? "Title (A-Z)" : "Highest read rate";
        chips.push({
            key: "sort",
            label: `Sort: ${sortLabel}`,
        });
    }

    return chips;
});

const clearFilters = () => {
    activeTab.value = "all";
    searchInput.value = "";
    query.value = "";
    sortBy.value = "newest";
    priorityFilter.value = "all";
    audienceFilter.value = "all";
    dateRangeFilter.value = "all";
};

const removeFilterChip = (key) => {
    if (key === "tab") {
        activeTab.value = "all";
        return;
    }

    if (key === "search") {
        searchInput.value = "";
        query.value = "";
        return;
    }

    if (key === "priority") {
        priorityFilter.value = "all";
        return;
    }

    if (key === "audience") {
        audienceFilter.value = "all";
        return;
    }

    if (key === "range") {
        dateRangeFilter.value = "all";
        return;
    }

    if (key === "sort") {
        sortBy.value = "newest";
    }
};

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

    if (priorityFilter.value !== "all") {
        params.set("priority", priorityFilter.value);
    } else {
        params.delete("priority");
    }

    if (audienceFilter.value !== "all") {
        params.set("audience", audienceFilter.value);
    } else {
        params.delete("audience");
    }

    if (dateRangeFilter.value !== "all") {
        params.set("range", dateRangeFilter.value);
    } else {
        params.delete("range");
    }

    if (sortBy.value !== "newest") {
        params.set("sort", sortBy.value);
    } else {
        params.delete("sort");
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

watch(
    [query, activeTab, sortBy, priorityFilter, audienceFilter, dateRangeFilter],
    () => {
        persistFiltersToUrl();
    }
);

onBeforeUnmount(() => {
    applySearch.cancel();
    persistFiltersToUrl.cancel();
});

const deleteAnnouncement = (id) => {
    if (
        !confirm(
            "Are you sure you want to delete this announcement? This action cannot be undone."
        )
    ) {
        return;
    }

    router.delete(route("admin.announcements.destroy", id), {
        preserveScroll: true,
    });
};

const pendingAudienceCount = (announcement) => {
    const recipientCount = Number(announcement?.analytics?.recipient_count ?? 0);
    const completedCount = announcement?.require_ack
        ? Number(announcement?.analytics?.ack_count ?? 0)
        : Number(announcement?.analytics?.read_count ?? 0);

    return Math.max(recipientCount - completedCount, 0);
};

const sendReminder = (announcement) => {
    const pending = pendingAudienceCount(announcement);
    if (pending <= 0) {
        alert("All recipients have already completed this announcement.");
        return;
    }

    const checkLabel = announcement?.require_ack ? "acknowledgement" : "read";
    if (
        !confirm(
            `Send reminder to ${pending} recipient(s) who have not completed ${checkLabel}?`
        )
    ) {
        return;
    }

    router.post(route("admin.announcements.remind", announcement.id), {}, {
        preserveScroll: true,
    });
};

const priorityLabel = (p) => {
    if (p === "urgent") return "Urgent";
    if (p === "important") return "Important";
    return "Info";
};
</script>

<template>
    <Head title="Announcements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">Announcements</h2>
                <Link
                    :href="route('admin.announcements.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-portal-navy-dark"
                >
                    Create Announcement
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Announcements' }]" />
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Total</p>
                        <p class="mt-2 text-3xl font-bold text-blue-900">{{ stats.total }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Pinned</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.pinned }}</p>
                    </div>
                    <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Urgent</p>
                        <p class="mt-2 text-3xl font-bold text-red-900">{{ stats.urgent }}</p>
                    </div>
                    <div class="rounded-xl border border-purple-200 bg-purple-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-purple-700">Ack required</p>
                        <p class="mt-2 text-3xl font-bold text-purple-900">{{ stats.ackRequired }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Avg read rate</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ stats.averageReadRate }}%</p>
                    </div>
                    <div class="rounded-xl border border-fuchsia-200 bg-fuchsia-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-fuchsia-700">Avg ack rate</p>
                        <p class="mt-2 text-3xl font-bold text-fuchsia-900">{{ stats.averageAckRate }}%</p>
                    </div>
                </div>

                <div class="portal-card p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Manage feed</p>
                            <p class="mt-1 text-sm text-slate-600">Filter, review, edit, and remove published announcements.</p>
                        </div>
                        <button
                            v-if="hasActiveFilters"
                            type="button"
                            @click="clearFilters"
                            class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Clear all filters
                        </button>
                    </div>

                    <div class="mt-4 grid gap-3 lg:grid-cols-6">
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

                        <div>
                            <input
                                v-model="searchInput"
                                type="text"
                                placeholder="Search title, body, author, roles"
                                class="block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>

                        <div>
                            <select
                                v-model="priorityFilter"
                                class="block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All priorities</option>
                                <option value="urgent">Urgent</option>
                                <option value="important">Important</option>
                                <option value="info">Info</option>
                            </select>
                        </div>

                        <div>
                            <select
                                v-model="audienceFilter"
                                class="block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All audiences</option>
                                <option value="student">Students</option>
                                <option value="teacher">Teachers</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>

                        <div>
                            <select
                                v-model="dateRangeFilter"
                                class="block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All dates</option>
                                <option value="7">Last 7 days</option>
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                        </div>

                        <div>
                            <select
                                v-model="sortBy"
                                class="block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="newest">Newest first</option>
                                <option value="title">Title (A-Z)</option>
                                <option value="engagement">Highest read rate</option>
                            </select>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ filtered.length }}</span>
                        of <span class="font-semibold text-slate-700">{{ announcements.length }}</span> announcements
                    </p>

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
                    <div
                        v-if="filtered.length === 0"
                        class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500"
                    >
                        No announcements match your filters.
                    </div>

                    <div
                        v-for="announcement in filtered"
                        :key="announcement.id"
                        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 flex-1 items-start gap-3">
                                <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                                    <img
                                        v-if="announcement.author_photo"
                                        :src="`/storage/${announcement.author_photo}`"
                                        :alt="`Photo of ${announcement.author}`"
                                        class="h-full w-full object-cover"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-500">
                                        {{ announcement.author?.charAt(0)?.toUpperCase() }}
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            v-if="announcement.pinned"
                                            class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                        >
                                            Pinned
                                        </span>
                                        <span
                                            class="rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                            :class="{
                                                'bg-red-100 text-red-800': announcement.priority === 'urgent',
                                                'bg-amber-100 text-amber-800': announcement.priority === 'important',
                                                'bg-blue-100 text-blue-800': announcement.priority === 'info',
                                            }"
                                        >
                                            {{ priorityLabel(announcement.priority) }}
                                        </span>
                                        <span
                                            v-if="announcement.require_ack"
                                            class="rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-800"
                                        >
                                            Ack required
                                        </span>
                                    </div>

                                    <h3 class="mt-2 text-base font-semibold text-slate-900">
                                        {{ announcement.title }}
                                    </h3>
                                    <p class="mt-1 text-xs text-slate-500">
                                        By {{ announcement.author }} | {{ announcement.created_at }}
                                    </p>

                                    <div v-if="announcement.audience?.roles?.length" class="mt-2 flex flex-wrap gap-1">
                                        <span
                                            v-for="r in announcement.audience.roles"
                                            :key="r"
                                            class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                        >
                                            {{ r === "all" ? "All roles" : r }}
                                        </span>
                                    </div>

                                    <div class="mt-3 grid gap-2 text-xs text-slate-600 sm:grid-cols-4">
                                        <div class="rounded-md bg-slate-50 px-2 py-1">
                                            Audience: <span class="font-semibold text-slate-800">{{ announcement.analytics?.recipient_count ?? 0 }}</span>
                                        </div>
                                        <div class="rounded-md bg-emerald-50 px-2 py-1">
                                            Read: <span class="font-semibold text-emerald-800">{{ announcement.analytics?.read_count ?? 0 }}</span>
                                            ({{ announcement.analytics?.read_rate ?? 0 }}%)
                                        </div>
                                        <div class="rounded-md bg-fuchsia-50 px-2 py-1">
                                            Ack: <span class="font-semibold text-fuchsia-800">{{ announcement.analytics?.ack_count ?? 0 }}</span>
                                            ({{ announcement.analytics?.ack_rate ?? 0 }}%)
                                        </div>
                                        <div class="rounded-md bg-slate-50 px-2 py-1">
                                            Pending: <span class="font-semibold text-slate-800">{{
                                                pendingAudienceCount(announcement)
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="rounded-md bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-200 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="pendingAudienceCount(announcement) === 0"
                                    @click="sendReminder(announcement)"
                                >
                                    Remind
                                </button>
                                <Link
                                    :href="route('admin.announcements.edit', announcement.id)"
                                    class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="deleteAnnouncement(announcement.id)"
                                    class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>

                        <p class="mt-3 whitespace-pre-line text-sm text-slate-700">{{ announcement.body }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
