<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

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

const activeTab = ref("all");
const query = ref("");
const sortBy = ref("newest");
const priorityFilter = ref("all");
const audienceFilter = ref("all");
const dateRangeFilter = ref("all");

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
    if (activeTab.value === "urgent") list = list.filter((a) => a.priority === "urgent");

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
            const roles = (a.audience?.roles ?? []).join(" ").toLowerCase();
            return title.includes(q) || body.includes(q) || roles.includes(q);
        });
    }

    if (sortBy.value === "title") {
        list.sort((a, b) => String(a.title ?? "").localeCompare(String(b.title ?? "")));
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

const clearFilters = () => {
    activeTab.value = "all";
    query.value = "";
    sortBy.value = "newest";
    priorityFilter.value = "all";
    audienceFilter.value = "all";
    dateRangeFilter.value = "all";
};

const deleteAnnouncement = (id) => {
    if (!confirm("Delete this announcement? This action cannot be undone.")) {
        return;
    }

    router.delete(route("teacher.announcements.destroy", id), {
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
    <Head title="My Announcements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">My Announcements</h2>
                <Link
                    :href="route('teacher.announcements.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-portal-navy-dark"
                >
                    Create Announcement
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'My Announcements' }]" />
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-indigo-700">Teacher updates</p>
                    <h3 class="mt-2 text-2xl font-semibold text-indigo-950">Publish clear updates for your classes</h3>
                    <p class="mt-2 text-sm text-indigo-900/80">
                        Create announcements for students and roles, then manage priority, pinning, and acknowledgment.
                    </p>
                </div>

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
                            <p class="mt-1 text-sm text-slate-600">Filter your posts, edit quickly, and keep important notices visible.</p>
                        </div>
                        <button
                            v-if="hasActiveFilters"
                            type="button"
                            @click="clearFilters"
                            class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Reset filters
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
                                v-model="query"
                                type="text"
                                placeholder="Search title, body, roles"
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
                                    {{ announcement.created_at }}
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
                                        Audience:
                                        <span class="font-semibold text-slate-800">{{
                                            announcement.analytics?.recipient_count ?? 0
                                        }}</span>
                                    </div>
                                    <div class="rounded-md bg-emerald-50 px-2 py-1">
                                        Read:
                                        <span class="font-semibold text-emerald-800">{{
                                            announcement.analytics?.read_count ?? 0
                                        }}</span>
                                        ({{ announcement.analytics?.read_rate ?? 0 }}%)
                                    </div>
                                    <div class="rounded-md bg-fuchsia-50 px-2 py-1">
                                        Ack:
                                        <span class="font-semibold text-fuchsia-800">{{
                                            announcement.analytics?.ack_count ?? 0
                                        }}</span>
                                        ({{ announcement.analytics?.ack_rate ?? 0 }}%)
                                    </div>
                                    <div class="rounded-md bg-slate-50 px-2 py-1">
                                        Pending:
                                        <span class="font-semibold text-slate-800">{{
                                            Math.max(
                                                (announcement.analytics?.recipient_count ?? 0) -
                                                    (announcement.analytics?.read_count ?? 0),
                                                0
                                            )
                                        }}</span>
                                    </div>
                                </div>

                                <p class="mt-3 whitespace-pre-line text-sm text-slate-700">
                                    {{ announcement.body }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <Link
                                    :href="route('teacher.announcements.edit', announcement.id)"
                                    class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                >
                                    Edit
                                </Link>
                                <button
                                    type="button"
                                    @click="deleteAnnouncement(announcement.id)"
                                    class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
