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

const unreadCount = computed(
    () => (props.announcements ?? []).filter(isUnread).length
);

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
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Announcements
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Announcements' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <!-- Header Banner -->
                <div
                    class="mb-6 overflow-hidden rounded-lg bg-gradient-to-r from-portal-navy to-portal-navy-dark p-8 text-white shadow-lg"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm"
                        >
                            <svg
                                class="h-8 w-8"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">
                                University Announcements
                            </h3>
                            <p class="mt-1 text-sm text-white/90">
                                Stay updated with news, deadlines and urgent
                                notices
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="portal-card mb-4 p-4">
                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex flex-wrap items-center gap-2">
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
                                    v-if="t.key === 'unread' && unreadCount > 0"
                                    class="ml-2 inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold text-white"
                                >
                                    {{ unreadCount }}
                                </span>
                            </button>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="relative w-full sm:w-72">
                                <input
                                    v-model="query"
                                    type="text"
                                    placeholder="Search announcements…"
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                                <button
                                    v-if="query"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="query = ''"
                                >
                                    <span class="sr-only">Clear</span>
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div
                        v-if="filtered.length === 0"
                        class="portal-card p-12 text-center"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                            />
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-slate-900">
                            No announcements found
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Try changing filters or search terms
                        </p>
                    </div>

                    <div
                        v-for="announcement in filtered"
                        :key="announcement.id"
                        class="portal-card overflow-hidden p-0 transition-shadow hover:shadow-lg"
                    >
                        <button
                            type="button"
                            class="flex w-full items-stretch text-left"
                            @click="open(announcement)"
                        >
                            <!-- Priority stripe -->
                            <div
                                class="w-1.5"
                                :class="priorityStripeClass(announcement.priority)"
                            />

                            <div class="flex flex-1 items-start gap-4 p-6">
                                <!-- Author Avatar -->
                                <div class="flex-shrink-0">
                                    <div
                                        class="h-12 w-12 overflow-hidden rounded-full border-2 border-slate-200 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="announcement.author_photo"
                                            :src="`/storage/${announcement.author_photo}`"
                                            :alt="`Photo of ${announcement.author}`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-sm font-semibold text-slate-500"
                                        >
                                            {{
                                                (announcement.author ?? "S")
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="min-w-0 flex-1">
                                    <div
                                        class="flex items-start justify-between gap-4"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <div
                                                class="flex flex-wrap items-center gap-2"
                                            >
                                                <span
                                                    v-if="isUnread(announcement)"
                                                    class="inline-flex h-2 w-2 rounded-full bg-emerald-500"
                                                    title="Unread"
                                                />
                                                <span
                                                    v-if="announcement.pinned"
                                                    class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                                >
                                                    📌 Pinned
                                                </span>
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                                    :class="{
                                                        'bg-red-100 text-red-800':
                                                            announcement.priority ===
                                                            'urgent',
                                                        'bg-amber-100 text-amber-800':
                                                            announcement.priority ===
                                                            'important',
                                                        'bg-blue-100 text-blue-800':
                                                            announcement.priority ===
                                                            'info',
                                                    }"
                                                >
                                                    {{
                                                        priorityLabel(
                                                            announcement.priority
                                                        )
                                                    }}
                                                </span>
                                                <span
                                                    v-if="needsAck(announcement)"
                                                    class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-800"
                                                >
                                                    Acknowledgement required
                                                </span>
                                            </div>

                                            <h3
                                                class="mt-2 truncate text-lg font-semibold text-slate-900"
                                            >
                                                {{ announcement.title }}
                                            </h3>

                                            <div
                                                class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500"
                                            >
                                                <span class="font-medium">{{
                                                    announcement.author
                                                }}</span>
                                                <span>·</span>
                                                <span>{{
                                                    announcement.created_at
                                                }}</span>

                                                <template
                                                    v-if="
                                                        announcement.audience
                                                            ?.roles?.length
                                                    "
                                                >
                                                    <span>·</span>
                                                    <span
                                                        class="inline-flex flex-wrap gap-1"
                                                    >
                                                        <span
                                                            v-for="r in announcement
                                                                .audience.roles"
                                                            :key="r"
                                                            class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                                        >
                                                            {{
                                                                r === "all"
                                                                    ? "All roles"
                                                                    : r
                                                            }}
                                                        </span>
                                                    </span>
                                                </template>
                                            </div>

                                            <p
                                                class="mt-3 text-sm leading-relaxed text-slate-700"
                                            >
                                                {{ announcement.body }}
                                            </p>
                                        </div>

                                        <div class="flex-shrink-0 pt-1">
                                            <div
                                                class="flex h-9 w-9 items-center justify-center rounded-full bg-portal-navy/10"
                                            >
                                                <svg
                                                    class="h-4 w-4 text-portal-navy"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail drawer -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="selected"
                class="fixed inset-0 z-50 flex items-stretch justify-end"
            >
                <div
                    class="absolute inset-0 bg-slate-900/50"
                    @click="close"
                />

                <div
                    class="relative h-full w-full max-w-xl bg-white shadow-2xl"
                >
                    <div class="border-b border-slate-200 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                        :class="{
                                            'bg-red-100 text-red-800':
                                                selected.priority === 'urgent',
                                            'bg-amber-100 text-amber-800':
                                                selected.priority ===
                                                'important',
                                            'bg-blue-100 text-blue-800':
                                                selected.priority === 'info',
                                        }"
                                    >
                                        {{ priorityLabel(selected.priority) }}
                                    </span>
                                    <span
                                        v-if="selected.pinned"
                                        class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                    >
                                        📌 Pinned
                                    </span>
                                    <span
                                        v-if="needsAck(selected)"
                                        class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-800"
                                    >
                                        Acknowledgement required
                                    </span>
                                </div>
                                <h3
                                    class="mt-2 text-lg font-semibold text-slate-900"
                                >
                                    {{ selected.title }}
                                </h3>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ selected.author }} ·
                                    {{ selected.created_at }}
                                </p>
                            </div>

                            <button
                                type="button"
                                class="rounded-md p-2 text-slate-600 hover:bg-slate-100"
                                @click="close"
                            >
                                <span class="sr-only">Close</span>
                                ✕
                            </button>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <button
                                v-if="needsAck(selected)"
                                type="button"
                                class="rounded-md bg-portal-navy px-3 py-2 text-xs font-semibold text-white hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                @click="acknowledge(selected)"
                            >
                                Acknowledge
                            </button>
                            <span
                                v-else-if="selected.acknowledged_at"
                                class="text-xs font-medium text-emerald-700"
                            >
                                ✔ Acknowledged
                            </span>
                            <span
                                v-if="selected.read_at"
                                class="text-xs text-slate-500"
                            >
                                Read
                            </span>
                        </div>
                    </div>

                    <div class="h-[calc(100%-92px)] overflow-y-auto p-5">
                        <p class="whitespace-pre-line text-sm text-slate-700">
                            {{ selected.body }}
                        </p>

                        <div
                            v-if="selected.audience?.roles?.length"
                            class="mt-6"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Audience
                            </p>
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
