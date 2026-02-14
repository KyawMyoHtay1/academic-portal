<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps({
    announcements: {
        type: Array,
        required: true,
    },
});

const tabs = [
    { key: "all", label: "All" },
    { key: "pinned", label: "Pinned" },
    { key: "unread", label: "Unread" },
    { key: "urgent", label: "Urgent" },
];

const activeTab = ref("all");
const query = ref("");
const selected = ref(null);

const isUnread = (a) => !a.read_at;
const isUrgent = (a) => a.priority === "urgent";
const isPinned = (a) => !!a.pinned;
const needsAck = (a) => !!a.require_ack && !a.acknowledged_at;

const stats = computed(() => {
    const list = props.announcements ?? [];
    return {
        total: list.length,
        unread: list.filter(isUnread).length,
        pinned: list.filter(isPinned).length,
        ackRequired: list.filter(needsAck).length,
    };
});

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.announcements ?? [];

    if (activeTab.value === "pinned") list = list.filter(isPinned);
    if (activeTab.value === "unread") list = list.filter(isUnread);
    if (activeTab.value === "urgent") list = list.filter(isUrgent);

    if (q) {
        list = list.filter((a) => {
            const t = (a.title ?? "").toLowerCase();
            const b = (a.body ?? "").toLowerCase();
            return t.includes(q) || b.includes(q);
        });
    }

    return list;
});

const open = (a) => {
    selected.value = a;
};

const close = () => {
    selected.value = null;
};

const markRead = (a) => {
    if (!a || a.read_at) return;
    router.post(route("announcements.read", a.id), {}, { preserveScroll: true });
};

const acknowledge = (a) => {
    if (!a || !a.require_ack || a.acknowledged_at) return;
    router.post(route("announcements.ack", a.id), {}, { preserveScroll: true });
};

watch(
    () => selected.value?.id,
    () => {
        if (selected.value) markRead(selected.value);
    }
);

const clearSearch = () => {
    query.value = "";
};

const priorityLabel = (p) => {
    if (p === "urgent") return "Urgent";
    if (p === "important") return "Important";
    return "Info";
};

const priorityStripeClass = (p) => {
    if (p === "urgent") return "bg-red-500";
    if (p === "important") return "bg-amber-500";
    return "bg-blue-500";
};
</script>

<template>
    <Head title="Announcements" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">News and Announcements</h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Announcements' }]" />
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
                <div class="rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-indigo-700">Campus updates</p>
                    <h3 class="mt-2 text-2xl font-semibold text-indigo-950">Stay informed about deadlines and notices</h3>
                    <p class="mt-2 text-sm text-indigo-900/80">
                        Read the latest announcements, review priority notices, and acknowledge items that require confirmation.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Total</p>
                        <p class="mt-2 text-2xl font-bold text-blue-900">{{ stats.total }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Unread</p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">{{ stats.unread }}</p>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Pinned</p>
                        <p class="mt-2 text-2xl font-bold text-amber-900">{{ stats.pinned }}</p>
                    </div>
                    <div class="rounded-xl border border-purple-200 bg-purple-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-purple-700">Needs acknowledgement</p>
                        <p class="mt-2 text-2xl font-bold text-purple-900">{{ stats.ackRequired }}</p>
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
                                <span
                                    v-if="t.key === 'unread' && stats.unread > 0"
                                    class="ml-2 rounded-full bg-white/20 px-1.5 py-0.5 text-[10px]"
                                >
                                    {{ stats.unread }}
                                </span>
                            </button>
                        </div>

                        <div class="relative w-full md:w-80">
                            <input
                                v-model="query"
                                type="text"
                                placeholder="Search announcements"
                                class="block w-full rounded-md border-slate-300 pr-9 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                            <button
                                v-if="query"
                                type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                @click="clearSearch"
                            >
                                <span class="sr-only">Clear</span>
                                X
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div v-if="filtered.length === 0" class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
                        <h3 class="text-sm font-semibold text-slate-900">No announcements found</h3>
                        <p class="mt-1 text-sm text-slate-500">Try adjusting your filters or search.</p>
                    </div>

                    <div
                        v-for="announcement in filtered"
                        :key="announcement.id"
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-md"
                    >
                        <button type="button" class="flex w-full items-stretch text-left" @click="open(announcement)">
                            <div class="w-1.5" :class="priorityStripeClass(announcement.priority)" />

                            <div class="flex flex-1 items-start gap-4 p-5">
                                <div class="h-11 w-11 flex-shrink-0 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                                    <img
                                        v-if="announcement.author_photo"
                                        :src="`/storage/${announcement.author_photo}`"
                                        :alt="`Photo of ${announcement.author}`"
                                        class="h-full w-full object-cover"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center text-sm font-semibold text-slate-500">
                                        {{ (announcement.author ?? "S").charAt(0).toUpperCase() }}
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span v-if="isUnread(announcement)" class="inline-flex h-2 w-2 rounded-full bg-emerald-500" title="Unread" />
                                        <span v-if="announcement.pinned" class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700">Pinned</span>
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
                                            v-if="needsAck(announcement)"
                                            class="rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-800"
                                        >
                                            Ack required
                                        </span>
                                    </div>

                                    <h3 class="mt-2 truncate text-base font-semibold text-slate-900">
                                        {{ announcement.title }}
                                    </h3>

                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                        <span class="font-medium">{{ announcement.author }}</span>
                                        <span>|</span>
                                        <span>{{ announcement.created_at }}</span>
                                    </div>

                                    <p class="mt-3 line-clamp-3 text-sm text-slate-700">
                                        {{ announcement.body }}
                                    </p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="selected" class="fixed inset-0 z-50 flex items-stretch justify-end">
                <div class="absolute inset-0 bg-slate-900/50" @click="close" />

                <div class="relative h-full w-full max-w-xl bg-white shadow-2xl">
                    <div class="border-b border-slate-200 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                        :class="{
                                            'bg-red-100 text-red-800': selected.priority === 'urgent',
                                            'bg-amber-100 text-amber-800': selected.priority === 'important',
                                            'bg-blue-100 text-blue-800': selected.priority === 'info',
                                        }"
                                    >
                                        {{ priorityLabel(selected.priority) }}
                                    </span>
                                    <span
                                        v-if="selected.pinned"
                                        class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                    >
                                        Pinned
                                    </span>
                                    <span
                                        v-if="needsAck(selected)"
                                        class="rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-800"
                                    >
                                        Ack required
                                    </span>
                                </div>
                                <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ selected.title }}</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ selected.author }} | {{ selected.created_at }}</p>
                            </div>

                            <button type="button" class="rounded-md p-2 text-slate-600 hover:bg-slate-100" @click="close">
                                <span class="sr-only">Close</span>
                                X
                            </button>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <button
                                v-if="needsAck(selected)"
                                type="button"
                                class="rounded-md bg-portal-navy px-3 py-2 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                                @click="acknowledge(selected)"
                            >
                                Acknowledge
                            </button>
                            <span v-else-if="selected.acknowledged_at" class="text-xs font-medium text-emerald-700">
                                Acknowledged
                            </span>
                            <span v-if="selected.read_at" class="text-xs text-slate-500">Read</span>
                        </div>
                    </div>

                    <div class="h-[calc(100%-92px)] overflow-y-auto p-5">
                        <p class="whitespace-pre-line text-sm text-slate-700">{{ selected.body }}</p>

                        <div v-if="selected.audience?.roles?.length" class="mt-6">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Audience</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span
                                    v-for="r in selected.audience.roles"
                                    :key="r"
                                    class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700"
                                >
                                    {{ r === "all" ? "All roles" : r }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </AuthenticatedLayout>
</template>