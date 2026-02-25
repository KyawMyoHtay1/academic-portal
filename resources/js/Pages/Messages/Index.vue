<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";

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
    { key: "unread", label: "Unread" },
];

const queryParam = (key) => {
    if (typeof window === "undefined") return null;
    return new URLSearchParams(window.location.search).get(key);
};

const allowedTabs = new Set(tabs.map((tab) => tab.key));

const activeTab = ref(
    allowedTabs.has(queryParam("tab") || "") ? queryParam("tab") : "all"
);
const expandedMessages = ref(new Set());
const queryInput = ref(queryParam("search") ?? "");
const query = ref(queryInput.value.trim());
const conversationQueryInput = ref(queryParam("thread_search") ?? "");
const conversationQuery = ref(conversationQueryInput.value.trim());
const unreadOnly = ref(queryParam("unread_only") === "1");
const replyBodyInput = ref(null);
const replyForm = useForm({
    receiver_id: "",
    body: "",
});
const page = usePage();
const authUserId = computed(() => Number(page.props.auth?.user?.id ?? 0));
const POLL_INTERVAL_MS = 15000;
const USER_ACTIVE_GRACE_MS = 2500;
const AUTO_SCROLL_BOTTOM_THRESHOLD_PX = 120;
const isPolling = ref(false);
const lastInteractionAt = ref(Date.now());
const threadScrollContainer = ref(null);
const threadIsNearBottom = ref(true);
const pendingThreadAutoScroll = ref(false);
let pollingTimer = null;
let echoConnection = null;
let echoConnectionStateHandler = null;

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
        if (activeTab.value === "unread") list = list.filter((message) => !message.is_sent && !message.read);
    }

    if (unreadOnly.value && !activeConversationData.value) {
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

const hasActiveFilters = computed(
    () =>
        activeTab.value !== "all" ||
        query.value.trim() !== "" ||
        conversationQuery.value.trim() !== "" ||
        unreadOnly.value
);

const activeFilterChips = computed(() => {
    const chips = [];

    if (activeTab.value !== "all") {
        const tabLabel =
            tabs.find((tab) => tab.key === activeTab.value)?.label ??
            activeTab.value;
        chips.push({
            key: "tab",
            label: `Tab: ${tabLabel}`,
        });
    }

    if (query.value.trim() !== "") {
        chips.push({
            key: "search",
            label: `Search: ${query.value.trim()}`,
        });
    }

    if (conversationQuery.value.trim() !== "") {
        chips.push({
            key: "thread_search",
            label: `Thread search: ${conversationQuery.value.trim()}`,
        });
    }

    if (unreadOnly.value) {
        chips.push({
            key: "unread_only",
            label: "Unread only",
        });
    }

    return chips;
});

const parseTimestamp = (value) => {
    if (!value) return 0;
    if (typeof value === "number") return value;

    const raw = String(value).trim();
    if (!raw) return 0;

    const normalized = raw.includes("T") ? raw : raw.replace(" ", "T");
    const parsed = new Date(normalized);
    const millis = parsed.getTime();

    return Number.isFinite(millis) ? millis : 0;
};

const threadMessages = computed(() =>
    [...filteredMessages.value].sort(
        (a, b) => parseTimestamp(a.created_at) - parseTimestamp(b.created_at)
    )
);

const formatThreadDateLabel = (value) => {
    const date = new Date(parseTimestamp(value));
    if (Number.isNaN(date.getTime())) return "Unknown date";

    const now = new Date();
    const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const startOfInput = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const dayDiff = Math.round((startOfInput.getTime() - startOfToday.getTime()) / 86400000);

    if (dayDiff === 0) return "Today";
    if (dayDiff === -1) return "Yesterday";

    return date.toLocaleDateString(undefined, {
        weekday: "short",
        month: "short",
        day: "numeric",
        year: date.getFullYear() === now.getFullYear() ? undefined : "numeric",
    });
};

const threadFeedItems = computed(() => {
    const items = [];
    let lastDayKey = null;
    let unreadDividerInserted = false;

    threadMessages.value.forEach((message) => {
        const timestamp = parseTimestamp(message.created_at);
        const date = new Date(timestamp);
        const dayKey = Number.isNaN(date.getTime()) ? "unknown" : `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;

        if (dayKey !== lastDayKey) {
            lastDayKey = dayKey;
            items.push({
                type: "date",
                key: `date-${dayKey}-${message.id}`,
                label: formatThreadDateLabel(message.created_at),
            });
        }

        if (!unreadDividerInserted && !message.is_sent && !message.read) {
            unreadDividerInserted = true;
            items.push({
                type: "unread",
                key: `unread-${message.id}`,
            });
        }

        items.push({
            type: "message",
            key: `message-${message.id}`,
            message,
        });
    });

    return items;
});

const markAsRead = (id) => {
    router.post(route("messages.read", id), {}, { preserveScroll: true });
};

const selectConversation = (userId) => {
    pendingThreadAutoScroll.value = true;
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
    pendingThreadAutoScroll.value = false;
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
    queryInput.value = "";
    query.value = "";
};

const clearConversationSearch = () => {
    conversationQueryInput.value = "";
    conversationQuery.value = "";
};

const clearAllFilters = () => {
    activeTab.value = "all";
    clearSearch();
    clearConversationSearch();
    unreadOnly.value = false;
};

const getThreadDistanceFromBottom = () => {
    const container = threadScrollContainer.value;
    if (!container) return 0;

    return Math.max(
        0,
        container.scrollHeight - container.scrollTop - container.clientHeight
    );
};

const handleThreadScroll = () => {
    threadIsNearBottom.value =
        getThreadDistanceFromBottom() <= AUTO_SCROLL_BOTTOM_THRESHOLD_PX;
};

const scrollThreadToBottom = (behavior = "smooth") => {
    nextTick(() => {
        const container = threadScrollContainer.value;
        if (!container) return;

        if (typeof container.scrollTo === "function") {
            container.scrollTo({
                top: container.scrollHeight,
                behavior,
            });
        } else {
            container.scrollTop = container.scrollHeight;
        }

        threadIsNearBottom.value = true;
    });
};

const trackInteraction = () => {
    lastInteractionAt.value = Date.now();
};

const removeFilterChip = (key) => {
    if (key === "tab") {
        activeTab.value = "all";
        return;
    }
    if (key === "search") {
        clearSearch();
        return;
    }
    if (key === "thread_search") {
        clearConversationSearch();
        return;
    }
    if (key === "unread_only") {
        unreadOnly.value = false;
    }
};

const applyMessageSearch = debounce(() => {
    query.value = queryInput.value.trim();
}, 250);

const applyConversationSearch = debounce(() => {
    conversationQuery.value = conversationQueryInput.value.trim();
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

    if (conversationQuery.value.trim() !== "") {
        params.set("thread_search", conversationQuery.value.trim());
    } else {
        params.delete("thread_search");
    }

    if (unreadOnly.value) {
        params.set("unread_only", "1");
    } else {
        params.delete("unread_only");
    }

    const queryString = params.toString();
    window.history.replaceState(
        {},
        "",
        queryString ? `${url.pathname}?${queryString}` : url.pathname
    );
}, 200);

watch(queryInput, () => {
    applyMessageSearch();
});

watch(conversationQueryInput, () => {
    applyConversationSearch();
});

watch([query, conversationQuery, activeTab, unreadOnly], () => {
    persistFiltersToUrl();
});

watch(activeConversationData, (conversation, previousConversation) => {
    if (conversation && unreadOnly.value) {
        unreadOnly.value = false;
    }

    const previousUserId = previousConversation
        ? String(previousConversation.user_id)
        : "";
    const currentUserId = conversation ? String(conversation.user_id) : "";
    replyForm.receiver_id = currentUserId;

    // Inertia reloads replace objects, so avoid clearing draft text unless user switched threads.
    if (currentUserId === previousUserId) {
        return;
    }

    replyForm.reset("body");
    replyForm.clearErrors();

    if (currentUserId && currentUserId !== previousUserId) {
        pendingThreadAutoScroll.value = true;
        scrollThreadToBottom("auto");
    }
});

watch(
    () => threadMessages.value.at(-1)?.id ?? null,
    (latestMessageId, previousMessageId) => {
        if (!activeConversationData.value) return;
        if (!latestMessageId || latestMessageId === previousMessageId) return;

        const shouldAutoScroll =
            pendingThreadAutoScroll.value || threadIsNearBottom.value;

        if (shouldAutoScroll) {
            scrollThreadToBottom(
                pendingThreadAutoScroll.value ? "smooth" : "auto"
            );
        }

        pendingThreadAutoScroll.value = false;
    }
);

watch(
    () => props.activeConversation,
    (activeConversation) => {
        if (activeConversation) {
            pendingThreadAutoScroll.value = true;
        }
    }
);

watch(replyForm.processing, (processing) => {
    if (!processing && pendingThreadAutoScroll.value) {
        scrollThreadToBottom("smooth");
    }
});

onBeforeUnmount(() => {
    stopRealtime();
    stopRealtimeConnectionFallback();
    stopPolling();
    if (typeof document !== "undefined") {
        document.removeEventListener("visibilitychange", handleVisibilityChange);
    }

    applyMessageSearch.cancel();
    applyConversationSearch.cancel();
    persistFiltersToUrl.cancel();
});

const focusInlineReply = () => {
    if (!activeConversationData.value) return;
    replyBodyInput.value?.focus?.();
};

const sendInlineReply = () => {
    if (!activeConversationData.value) return;

    trackInteraction();
    pendingThreadAutoScroll.value = true;
    replyForm.receiver_id = String(activeConversationData.value.user_id);
    replyForm.post(route("messages.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            replyForm.reset("body");
            replyForm.clearErrors();
            replyBodyInput.value?.focus?.();
        },
    });
};

const shouldPollNow = () => {
    if (isPolling.value) return false;
    if (replyForm.processing) return false;
    if (typeof document !== "undefined" && document.hidden) return false;
    return Date.now() - lastInteractionAt.value >= USER_ACTIVE_GRACE_MS;
};

const refreshMessages = (force = false) => {
    if (isPolling.value) return;
    if (!force && !shouldPollNow()) return;
    if (replyForm.processing) return;
    if (typeof document !== "undefined" && document.hidden) return;

    isPolling.value = true;
    router.reload({
        only: ["messages", "conversations", "activeConversation", "unread"],
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isPolling.value = false;
        },
    });
};

const startPolling = () => {
    if (pollingTimer !== null || typeof window === "undefined") return;
    pollingTimer = window.setInterval(refreshMessages, POLL_INTERVAL_MS);
};

const stopPolling = () => {
    if (pollingTimer === null || typeof window === "undefined") return;
    window.clearInterval(pollingTimer);
    pollingTimer = null;
};

const handleVisibilityChange = () => {
    if (typeof document !== "undefined" && !document.hidden) {
        refreshMessages(true);
    }
};

const startRealtime = () => {
    if (typeof window === "undefined" || !window.Echo || !authUserId.value) return false;

    window.Echo.private(`messages.${authUserId.value}`).listen(".message.sent", () => {
        refreshMessages(true);
    });

    return true;
};

const stopRealtime = () => {
    if (typeof window === "undefined" || !window.Echo || !authUserId.value) return;
    window.Echo.leave(`messages.${authUserId.value}`);
};

const startRealtimeConnectionFallback = () => {
    const connection = window.Echo?.connector?.pusher?.connection;

    if (!connection) {
        startPolling();
        return;
    }

    echoConnection = connection;

    if (echoConnectionStateHandler) {
        echoConnection.unbind("state_change", echoConnectionStateHandler);
    }

    echoConnectionStateHandler = (state) => {
        const current = state?.current;
        if (current === "connected") {
            stopPolling();
            return;
        }

        if (current === "disconnected" || current === "failed" || current === "unavailable") {
            startPolling();
        }
    };

    echoConnection.bind("state_change", echoConnectionStateHandler);

    if (connection.state === "connected") {
        stopPolling();
    } else {
        startPolling();
    }
};

const stopRealtimeConnectionFallback = () => {
    if (echoConnection && echoConnectionStateHandler) {
        echoConnection.unbind("state_change", echoConnectionStateHandler);
    }

    echoConnection = null;
    echoConnectionStateHandler = null;
};

onMounted(() => {
    if (startRealtime()) {
        startRealtimeConnectionFallback();
    } else {
        startPolling();
    }

    if (typeof document !== "undefined") {
        document.addEventListener("visibilitychange", handleVisibilityChange);
    }

    if (activeConversationData.value) {
        pendingThreadAutoScroll.value = true;
        scrollThreadToBottom("auto");
    }
});
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

        <div
            class="py-10"
            @input.capture="trackInteraction"
            @keydown.capture="trackInteraction"
            @click.capture="trackInteraction"
        >
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

                <div class="grid gap-6 lg:grid-cols-[320px,1fr] lg:h-[calc(100vh-14rem)] lg:min-h-[42rem] lg:items-start">
                    <div class="portal-card p-4 lg:flex lg:h-full lg:min-h-0 lg:flex-col">
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

                        <div class="mt-3 relative">
                            <input
                                v-model="conversationQueryInput"
                                type="text"
                                placeholder="Search conversations"
                                class="block w-full rounded-md border-slate-300 pr-9 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                            <button
                                v-if="conversationQueryInput"
                                type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                @click="clearConversationSearch"
                            >
                                <span class="sr-only">Clear</span>
                                X
                            </button>
                        </div>

                        <div class="mt-4 space-y-2 lg:min-h-0 lg:flex-1 lg:overflow-y-auto lg:pr-1">
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
                                    <span
                                        v-if="conversation.last_is_sent"
                                        class="font-semibold text-indigo-700"
                                    >
                                        {{ conversation.last_read ? "Seen by recipient" : "Sent" }}
                                    </span>
                                    <span v-if="conversation.last_is_sent"> · </span>
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

                    <div class="space-y-4 lg:flex lg:h-full lg:min-h-0 lg:flex-col">
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
                                        v-model="queryInput"
                                        type="text"
                                        placeholder="Search messages by name, role, or content"
                                        class="block w-full rounded-md border-slate-300 pr-9 text-sm focus:border-portal-navy focus:ring-portal-navy"
                                    />
                                    <button
                                        v-if="queryInput"
                                        type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                        @click="clearSearch"
                                    >
                                        <span class="sr-only">Clear</span>
                                        X
                                    </button>
                                </div>

                                <label
                                    class="inline-flex items-center gap-2 text-sm"
                                    :class="activeConversationData ? 'text-slate-400' : 'text-slate-700'"
                                >
                                    <input
                                        v-model="unreadOnly"
                                        type="checkbox"
                                        class="rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                        :disabled="!!activeConversationData"
                                    />
                                    Unread only
                                </label>
                            </div>

                            <div
                                v-if="activeFilterChips.length > 0"
                                class="mt-3 flex flex-wrap items-center gap-2"
                            >
                                <span
                                    v-for="chip in activeFilterChips"
                                    :key="chip.key"
                                    class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700"
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
                                <button
                                    v-if="hasActiveFilters"
                                    type="button"
                                    class="rounded-md border border-slate-300 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                    @click="clearAllFilters"
                                >
                                    Clear all filters
                                </button>
                            </div>
                        </div>

                        <div
                            v-if="activeConversationData"
                            class="portal-card p-4 lg:flex lg:min-h-0 lg:flex-1 lg:flex-col"
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
                                    @click="focusInlineReply"
                                >
                                    Reply
                                </button>
                            </div>

                            <div
                                ref="threadScrollContainer"
                                class="space-y-3 overflow-x-hidden lg:min-h-0 lg:flex-1 lg:overflow-y-auto lg:pr-1"
                                @scroll.passive="handleThreadScroll"
                            >
                                <div
                                    v-if="threadMessages.length === 0"
                                    class="rounded-lg border border-dashed border-slate-300 bg-white p-6 text-center text-sm text-slate-500"
                                >
                                    No messages found in this thread for current filters.
                                </div>

                                <template v-for="item in threadFeedItems" :key="item.key">
                                    <div v-if="item.type === 'date'" class="flex items-center gap-3 py-1">
                                        <span class="h-px flex-1 bg-slate-200"></span>
                                        <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                            {{ item.label }}
                                        </span>
                                        <span class="h-px flex-1 bg-slate-200"></span>
                                    </div>

                                    <div v-else-if="item.type === 'unread'" class="flex items-center gap-3 py-1">
                                        <span class="h-px flex-1 bg-emerald-200"></span>
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-800">
                                            Unread
                                        </span>
                                        <span class="h-px flex-1 bg-emerald-200"></span>
                                    </div>

                                    <div
                                        v-else
                                        class="flex"
                                        :class="item.message.is_sent ? 'justify-end' : 'justify-start'"
                                    >
                                        <div
                                            class="max-w-[85%] rounded-xl px-4 py-3 text-sm shadow-sm"
                                            :class="
                                                item.message.is_sent
                                                    ? 'bg-portal-navy text-white'
                                                    : 'bg-slate-100 text-slate-800'
                                            "
                                        >
                                            <p class="whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ item.message.body }}</p>
                                            <p
                                                class="mt-2 text-[11px]"
                                                :class="item.message.is_sent ? 'text-blue-100' : 'text-slate-500'"
                                            >
                                                {{ formatDate(item.message.created_at) }}
                                                <span v-if="item.message.is_sent">
                                                    · {{ item.message.read ? "Seen by recipient" : "Sent" }}
                                                </span>
                                            </p>
                                            <button
                                                v-if="!item.message.read && !item.message.is_sent"
                                                type="button"
                                                class="mt-2 rounded-md bg-white/20 px-2 py-1 text-[11px] font-semibold text-current hover:bg-white/30"
                                                @click="markAsRead(item.message.id)"
                                            >
                                                Mark as read
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <form class="mt-4 border-t border-slate-200 pt-4" @submit.prevent="sendInlineReply">
                                <label for="thread-reply" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Reply to {{ activeConversationData.name }}
                                </label>
                                <textarea
                                    id="thread-reply"
                                    ref="replyBodyInput"
                                    v-model="replyForm.body"
                                    rows="3"
                                    maxlength="2000"
                                    placeholder="Write a reply..."
                                    class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500': replyForm.errors.body,
                                    }"
                                ></textarea>
                                <p v-if="replyForm.errors.body" class="mt-1 text-xs text-red-600">
                                    {{ replyForm.errors.body }}
                                </p>
                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <p class="text-xs text-slate-500">
                                        {{ (replyForm.body ?? '').length }}/2000
                                    </p>
                                    <button
                                        type="submit"
                                        :disabled="replyForm.processing || !replyForm.body.trim()"
                                        class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-portal-navy-dark disabled:opacity-50"
                                    >
                                        <span v-if="replyForm.processing">Sending...</span>
                                        <span v-else>Send Reply</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div v-else class="space-y-4 lg:min-h-0 lg:flex-1 lg:overflow-y-auto lg:pr-1">
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

                                            <p class="mt-3 whitespace-pre-wrap break-words [overflow-wrap:anywhere] text-sm text-slate-700">
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
                                    <span
                                        v-else-if="message.is_sent"
                                        class="rounded-md px-3 py-1.5 text-xs font-semibold"
                                        :class="
                                            message.read
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-indigo-100 text-indigo-700'
                                        "
                                    >
                                        {{ message.read ? "Seen by recipient" : "Sent" }}
                                    </span>
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
