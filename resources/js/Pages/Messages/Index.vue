<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    messages: {
        type: Object,
        required: true,
    },
    conversations: {
        type: Array,
        default: () => [],
    },
    activeConversation: {
        type: Number,
        default: null,
    },
});

const tabs = [
    { key: "all", label: "All" },
    { key: "inbox", label: "Inbox" },
    { key: "sent", label: "Sent" },
];

const activeTab = ref("all");
const expandedMessages = ref(new Set());
const query = ref("");
const conversationQuery = ref("");
const unreadOnly = ref(false);

const messagesData = computed(() => props.messages?.data ?? []);
const messageLinks = computed(() => props.messages?.links ?? []);

const activeConversationData = computed(() =>
    (props.conversations ?? []).find(
        (conversation) =>
            String(conversation.user_id) === String(props.activeConversation ?? "")
    ) || null
);

const stats = computed(() => {
    const conversationList = props.conversations ?? [];
    const unreadMessages = conversationList.reduce(
        (sum, conversation) => sum + Number(conversation.unread_count || 0),
        0
    );

    return {
        conversations: conversationList.length,
        unreadConversations: conversationList.filter(
            (conversation) => Number(conversation.unread_count || 0) > 0
        ).length,
        unreadMessages,
        threadMessages: messagesData.value.length,
    };
});

const filteredConversations = computed(() => {
    const q = conversationQuery.value.trim().toLowerCase();
    if (!q) return props.conversations ?? [];

    return (props.conversations ?? []).filter((conversation) => {
        const name = String(conversation.name ?? "").toLowerCase();
        const role = String(conversation.role ?? "").toLowerCase();
        const preview = String(conversation.last_message ?? "").toLowerCase();
        return name.includes(q) || role.includes(q) || preview.includes(q);
    });
});

const filteredMessages = computed(() => {
    const q = query.value.trim().toLowerCase();

    let list = messagesData.value;

    // In conversation thread mode, show all messages in that thread by default.
    if (!activeConversationData.value) {
        if (activeTab.value === "inbox") list = list.filter((message) => !message.is_sent);
        if (activeTab.value === "sent") list = list.filter((message) => message.is_sent);
    }

    if (unreadOnly.value) {
        list = list.filter((message) => !message.is_sent && !message.read);
    }

    if (q) {
        list = list.filter((message) => {
            const body = String(message.body ?? "").toLowerCase();
            const sender = String(message.sender ?? "").toLowerCase();
            const receiver = String(message.receiver ?? "").toLowerCase();
            const senderRole = String(message.sender_role ?? "").toLowerCase();
            const receiverRole = String(message.receiver_role ?? "").toLowerCase();

            return (
                body.includes(q) ||
                sender.includes(q) ||
                receiver.includes(q) ||
                senderRole.includes(q) ||
                receiverRole.includes(q)
            );
        });
    }

    return list;
});

const threadMessages = computed(() =>
    [...filteredMessages.value].sort(
        (a, b) => Number(a.created_at || 0) - Number(b.created_at || 0)
    )
);

const markAsRead = (id) => {
    router.post(route("messages.read", id), {}, { preserveScroll: true });
};

const selectConversation = (userId) => {
    router.get(
        route("messages.index"),
        { with_user: userId },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    );
};

const clearConversation = () => {
    router.get(
        route("messages.index"),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    );
};

const toggleExpand = (id) => {
    if (expandedMessages.value.has(id)) {
        expandedMessages.value.delete(id);
        return;
    }
    expandedMessages.value.add(id);
};

const formatDate = (dateValue) => {
    if (!dateValue) return "Unknown";

    let date;
    if (typeof dateValue === "number") {
        date = new Date(dateValue);
    } else if (String(dateValue).includes("T")) {
        date = new Date(dateValue);
    } else if (String(dateValue).match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?/)) {
        date = new Date(
            String(dateValue).replace(" ", "T") +
                (String(dateValue).includes(":") && String(dateValue).split(":").length === 2
                    ? ":00"
                    : "")
        );
    } else {
        date = new Date(dateValue);
    }

    if (Number.isNaN(date.getTime())) return "Invalid date";

    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    if (diffMs < 0) return "Just now";

    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    const diffWeeks = Math.floor(diffDays / 7);
    const diffMonths = Math.floor(diffDays / 30);
    const diffYears = Math.floor(diffDays / 365);

    if (diffMins < 1) return "Just now";
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays === 1) return "Yesterday";
    if (diffDays < 7) return `${diffDays}d ago`;
    if (diffDays < 30) return diffWeeks === 1 ? "1 week ago" : `${diffWeeks} weeks ago`;
    if (diffDays < 365) return diffMonths === 1 ? "1 month ago" : `${diffMonths} months ago`;
    return diffYears === 1 ? "1 year ago" : `${diffYears} years ago`;
};

const truncate = (value, max = 90) => {
    const text = String(value ?? "");
    return text.length > max ? `${text.slice(0, max)}...` : text;
};

const truncateMessage = (text, maxLength = 150) => {
    if (!text) return "";
    if (text.length <= maxLength) return text;
    return `${text.substring(0, maxLength)}...`;
};

const clearSearch = () => {
    query.value = "";
};

const openReply = () => {
    if (!activeConversationData.value) return;
    router.get(route("messages.create"), {
        to: activeConversationData.value.user_id,
    });
};
</script>

<template>
    <Head title="Messages" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">Messages</h2>
                <a
                    :href="route('messages.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-portal-navy-dark"
                >
                    New Message
                </a>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Messages' }]" />
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Conversations</p>
                        <p class="mt-2 text-2xl font-bold text-blue-900">{{ stats.conversations }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Unread messages</p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">{{ stats.unreadMessages }}</p>
                    </div>
                    <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Unread conversations</p>
                        <p class="mt-2 text-2xl font-bold text-indigo-900">{{ stats.unreadConversations }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Current thread items</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ stats.threadMessages }}</p>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-[320px,1fr]">
                    <div class="portal-card p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Threads</p>
                            <button
                                v-if="activeConversationData"
                                type="button"
                                class="rounded-md border border-slate-300 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-50"
                                @click="clearConversation"
                            >
                                Show all
                            </button>
                        </div>

                        <div class="mt-3">
                            <input
                                v-model="conversationQuery"
                                type="text"
                                placeholder="Search conversations"
                                class="block w-full rounded-md border-slate-300 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>

                        <div class="mt-4 space-y-2">
                            <button
                                v-for="conversation in filteredConversations"
                                :key="conversation.user_id"
                                type="button"
                                class="w-full rounded-lg border px-3 py-2 text-left"
                                :class="
                                    String(conversation.user_id) === String(props.activeConversation || '')
                                        ? 'border-portal-navy bg-blue-50'
                                        : 'border-slate-200 bg-white hover:bg-slate-50'
                                "
                                @click="selectConversation(conversation.user_id)"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm font-semibold text-slate-900">
                                        {{ conversation.name }}
                                    </p>
                                    <span
                                        v-if="Number(conversation.unread_count || 0) > 0"
                                        class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                    >
                                        {{ conversation.unread_count }}
                                    </span>
                                </div>
                                <p class="mt-0.5 text-[11px] uppercase text-slate-500">
                                    {{ conversation.role }}
                                </p>
                                <p class="mt-1 truncate text-xs text-slate-600">
                                    {{ truncate(conversation.last_message) }}
                                </p>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    {{ formatDate(conversation.last_at) }}
                                </p>
                            </button>

                            <div
                                v-if="filteredConversations.length === 0"
                                class="rounded-lg border border-dashed border-slate-300 bg-white p-4 text-center text-xs text-slate-500"
                            >
                                No conversation matches your search.
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="portal-card p-4">
                            <div class="grid gap-3 lg:grid-cols-[auto,1fr,auto] lg:items-center">
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="tab in tabs"
                                        :key="tab.key"
                                        type="button"
                                        class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-slate-200 transition"
                                        :class="[
                                            activeTab === tab.key
                                                ? 'bg-portal-navy text-white ring-portal-navy'
                                                : 'bg-white text-slate-700 hover:bg-slate-50',
                                        ]"
                                        @click="activeTab = tab.key"
                                        :disabled="!!activeConversationData"
                                    >
                                        {{ tab.label }}
                                    </button>
                                </div>

                                <div class="relative">
                                    <input
                                        v-model="query"
                                        type="text"
                                        placeholder="Search messages by name, role, or content"
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

                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input
                                        v-model="unreadOnly"
                                        type="checkbox"
                                        class="rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                    Unread only
                                </label>
                            </div>
                        </div>

                        <div
                            v-if="activeConversationData"
                            class="portal-card p-4"
                        >
                            <div class="mb-4 flex items-center justify-between gap-3 border-b border-slate-200 pb-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">
                                        Conversation with {{ activeConversationData.name }}
                                    </p>
                                    <p class="text-xs uppercase text-slate-500">
                                        {{ activeConversationData.role }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                                    @click="openReply"
                                >
                                    Reply
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-if="threadMessages.length === 0"
                                    class="rounded-lg border border-dashed border-slate-300 bg-white p-6 text-center text-sm text-slate-500"
                                >
                                    No messages found in this thread for current filters.
                                </div>

                                <div
                                    v-for="message in threadMessages"
                                    :key="message.id"
                                    class="flex"
                                    :class="message.is_sent ? 'justify-end' : 'justify-start'"
                                >
                                    <div
                                        class="max-w-[85%] rounded-xl px-4 py-3 text-sm shadow-sm"
                                        :class="
                                            message.is_sent
                                                ? 'bg-portal-navy text-white'
                                                : 'bg-slate-100 text-slate-800'
                                        "
                                    >
                                        <p class="whitespace-pre-line">{{ message.body }}</p>
                                        <p
                                            class="mt-2 text-[11px]"
                                            :class="message.is_sent ? 'text-blue-100' : 'text-slate-500'"
                                        >
                                            {{ formatDate(message.created_at) }}
                                        </p>
                                        <button
                                            v-if="!message.read && !message.is_sent"
                                            type="button"
                                            class="mt-2 rounded-md bg-white/20 px-2 py-1 text-[11px] font-semibold text-current hover:bg-white/30"
                                            @click="markAsRead(message.id)"
                                        >
                                            Mark as read
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-if="filteredMessages.length === 0" class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
                                <h3 class="text-sm font-semibold text-slate-900">No messages found</h3>
                                <p class="mt-1 text-sm text-slate-500">Try changing your tab or search query.</p>
                            </div>

                            <div
                                v-for="message in filteredMessages"
                                :key="message.id"
                                class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md"
                                :class="{
                                    'ring-1 ring-emerald-200': !message.read && !message.is_sent,
                                }"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex min-w-0 flex-1 items-start gap-3">
                                        <div class="h-11 w-11 flex-shrink-0 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                                            <img
                                                v-if="message.is_sent ? message.receiver_photo : message.sender_photo"
                                                :src="`/storage/${message.is_sent ? message.receiver_photo : message.sender_photo}`"
                                                :alt="`Photo of ${message.is_sent ? message.receiver : message.sender}`"
                                                class="h-full w-full object-cover"
                                            />
                                            <div v-else class="flex h-full w-full items-center justify-center text-sm font-semibold text-slate-500">
                                                {{ (message.is_sent ? message.receiver : message.sender).charAt(0).toUpperCase() }}
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-semibold text-slate-900">
                                                    {{ message.is_sent ? `To: ${message.receiver}` : message.sender }}
                                                </p>
                                                <span
                                                    class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase"
                                                    :class="message.is_sent ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700'"
                                                >
                                                    {{ message.is_sent ? message.receiver_role : message.sender_role }}
                                                </span>
                                                <span v-if="!message.read && !message.is_sent" class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800">
                                                    New
                                                </span>
                                            </div>

                                            <p class="mt-1 text-xs text-slate-500">{{ formatDate(message.created_at) }}</p>

                                            <p class="mt-3 whitespace-pre-line text-sm text-slate-700">
                                                <span v-if="!expandedMessages.has(message.id) && (message.body ?? '').length > 150">
                                                    {{ truncateMessage(message.body) }}
                                                </span>
                                                <span v-else>{{ message.body }}</span>
                                            </p>

                                            <button
                                                v-if="(message.body ?? '').length > 150"
                                                @click="toggleExpand(message.id)"
                                                class="mt-2 text-xs font-semibold text-portal-navy hover:text-portal-navy-dark"
                                            >
                                                {{ expandedMessages.has(message.id) ? "Show less" : "Read more" }}
                                            </button>
                                        </div>
                                    </div>

                                    <button
                                        v-if="!message.read && !message.is_sent"
                                        @click="markAsRead(message.id)"
                                        class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                                    >
                                        Mark as read
                                    </button>
                                    <span v-else-if="message.is_sent" class="rounded-md bg-indigo-100 px-3 py-1.5 text-xs font-semibold text-indigo-700">Sent</span>
                                    <span v-else class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">Read</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center pt-2" v-if="messageLinks.length">
                            <Pagination :links="messageLinks" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
