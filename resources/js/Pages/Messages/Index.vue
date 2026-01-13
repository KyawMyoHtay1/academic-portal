<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    messages: {
        type: Array,
        required: true,
    },
});

const activeTab = ref("all"); // 'all', 'inbox', 'sent'
const expandedMessages = ref(new Set());

const markAsRead = (id) => {
    router.post(route("messages.read", id), {}, { preserveScroll: true });
};

const toggleExpand = (id) => {
    if (expandedMessages.value.has(id)) {
        expandedMessages.value.delete(id);
    } else {
        expandedMessages.value.add(id);
    }
};

// Filter messages based on active tab
const filteredMessages = computed(() => {
    if (activeTab.value === "inbox") {
        return props.messages.filter((m) => !m.is_sent);
    } else if (activeTab.value === "sent") {
        return props.messages.filter((m) => m.is_sent);
    }
    return props.messages;
});

// Statistics
const stats = computed(() => {
    const inbox = props.messages.filter((m) => !m.is_sent);
    const unread = inbox.filter((m) => !m.read);
    return {
        total: props.messages.length,
        inbox: inbox.length,
        sent: props.messages.filter((m) => m.is_sent).length,
        unread: unread.length,
    };
});

// Format date to relative time
const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return "Just now";
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays === 1) return "Yesterday";
    if (diffDays < 7) return `${diffDays}d ago`;

    return date.toLocaleDateString("en-GB", {
        day: "numeric",
        month: "short",
        year: date.getFullYear() !== now.getFullYear() ? "numeric" : undefined,
    });
};

// Truncate message body
const truncateMessage = (text, maxLength = 150) => {
    if (!text) return "";
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + "...";
};
</script>

<template>
    <Head title="Messages" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Messages
                </h2>
                <a
                    :href="route('messages.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
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

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <!-- Header Banner with Stats -->
                <div
                    class="mb-6 overflow-hidden rounded-xl bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 p-8 text-white shadow-xl"
                >
                    <div
                        class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg"
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
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Messages</h3>
                                <p class="mt-1 text-sm text-white/90">
                                    Communicate with students, teachers, and
                                    staff members
                                </p>
                            </div>
                        </div>
                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                            <div
                                class="rounded-lg bg-white/10 backdrop-blur-sm p-3 text-center"
                            >
                                <div class="text-2xl font-bold">
                                    {{ stats.total }}
                                </div>
                                <div class="text-xs text-white/80">Total</div>
                            </div>
                            <div
                                class="rounded-lg bg-white/10 backdrop-blur-sm p-3 text-center"
                            >
                                <div class="text-2xl font-bold">
                                    {{ stats.inbox }}
                                </div>
                                <div class="text-xs text-white/80">Inbox</div>
                            </div>
                            <div
                                class="rounded-lg bg-white/10 backdrop-blur-sm p-3 text-center"
                            >
                                <div class="text-2xl font-bold">
                                    {{ stats.sent }}
                                </div>
                                <div class="text-xs text-white/80">Sent</div>
                            </div>
                            <div
                                class="rounded-lg bg-amber-500/30 backdrop-blur-sm p-3 text-center"
                                :class="{
                                    'bg-emerald-500/30': stats.unread === 0,
                                }"
                            >
                                <div class="text-2xl font-bold">
                                    {{ stats.unread }}
                                </div>
                                <div class="text-xs text-white/80">Unread</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mb-6 flex gap-2 border-b border-slate-200">
                    <button
                        @click="activeTab = 'all'"
                        class="px-4 py-2 text-sm font-medium transition-colors"
                        :class="
                            activeTab === 'all'
                                ? 'border-b-2 border-portal-navy text-portal-navy'
                                : 'text-slate-600 hover:text-slate-900'
                        "
                    >
                        All Messages
                        <span
                            class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs"
                            :class="{
                                'bg-portal-navy text-white':
                                    activeTab === 'all',
                            }"
                        >
                            {{ stats.total }}
                        </span>
                    </button>
                    <button
                        @click="activeTab = 'inbox'"
                        class="px-4 py-2 text-sm font-medium transition-colors"
                        :class="
                            activeTab === 'inbox'
                                ? 'border-b-2 border-portal-navy text-portal-navy'
                                : 'text-slate-600 hover:text-slate-900'
                        "
                    >
                        Inbox
                        <span
                            v-if="stats.unread > 0"
                            class="ml-2 rounded-full bg-emerald-500 px-2 py-0.5 text-xs font-semibold text-white"
                        >
                            {{ stats.unread }}
                        </span>
                    </button>
                    <button
                        @click="activeTab = 'sent'"
                        class="px-4 py-2 text-sm font-medium transition-colors"
                        :class="
                            activeTab === 'sent'
                                ? 'border-b-2 border-portal-navy text-portal-navy'
                                : 'text-slate-600 hover:text-slate-900'
                        "
                    >
                        Sent
                        <span
                            class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs"
                            :class="{
                                'bg-portal-navy text-white':
                                    activeTab === 'sent',
                            }"
                        >
                            {{ stats.sent }}
                        </span>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Empty State -->
                    <div
                        v-if="filteredMessages.length === 0"
                        class="portal-card p-16 text-center"
                    >
                        <div
                            class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-100"
                        >
                            <svg
                                class="h-10 w-10 text-slate-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-slate-900">
                            {{
                                activeTab === "inbox"
                                    ? "No messages in your inbox"
                                    : activeTab === "sent"
                                    ? "No sent messages"
                                    : "No messages"
                            }}
                        </h3>
                        <p class="mt-2 text-sm text-slate-500">
                            {{
                                activeTab === "sent"
                                    ? "Messages you send will appear here"
                                    : "Start a conversation by sending a new message"
                            }}
                        </p>
                        <a
                            v-if="activeTab !== 'sent'"
                            :href="route('messages.create')"
                            class="mt-6 inline-flex items-center gap-2 rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            New Message
                        </a>
                    </div>

                    <!-- Message Cards -->
                    <transition-group
                        name="message-list"
                        tag="div"
                        class="space-y-4"
                    >
                        <div
                            v-for="message in filteredMessages"
                            :key="message.id"
                            class="group portal-card overflow-hidden p-6 transition-all duration-200 hover:shadow-lg"
                            :class="{
                                'bg-gradient-to-r from-slate-50 to-white border-l-4 border-slate-300':
                                    message.read && !message.is_sent,
                                'bg-gradient-to-r from-emerald-50 to-white border-l-4 border-emerald-500 shadow-md':
                                    !message.read && !message.is_sent,
                                'bg-gradient-to-r from-blue-50 to-white border-l-4 border-blue-500':
                                    message.is_sent,
                            }"
                        >
                            <div class="flex items-start gap-4">
                                <!-- Avatar with Status Indicator -->
                                <div class="relative flex-shrink-0">
                                    <div
                                        class="h-14 w-14 overflow-hidden rounded-full border-2 bg-slate-100 flex items-center justify-center shadow-md transition-transform group-hover:scale-110"
                                        :class="{
                                            'border-emerald-400 ring-2 ring-emerald-200':
                                                !message.read &&
                                                !message.is_sent,
                                            'border-slate-300':
                                                message.read &&
                                                !message.is_sent,
                                            'border-blue-400 ring-2 ring-blue-200':
                                                message.is_sent,
                                        }"
                                    >
                                        <img
                                            v-if="
                                                message.is_sent
                                                    ? message.receiver_photo
                                                    : message.sender_photo
                                            "
                                            :src="`/storage/${
                                                message.is_sent
                                                    ? message.receiver_photo
                                                    : message.sender_photo
                                            }`"
                                            :alt="`Photo of ${
                                                message.is_sent
                                                    ? message.receiver
                                                    : message.sender
                                            }`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-base font-bold"
                                            :class="{
                                                'text-emerald-600':
                                                    !message.read &&
                                                    !message.is_sent,
                                                'text-slate-500':
                                                    message.read &&
                                                    !message.is_sent,
                                                'text-blue-600':
                                                    message.is_sent,
                                            }"
                                        >
                                            {{
                                                (message.is_sent
                                                    ? message.receiver
                                                    : message.sender
                                                )
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </div>
                                    <!-- Unread Indicator -->
                                    <div
                                        v-if="!message.read && !message.is_sent"
                                        class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-emerald-500 border-2 border-white"
                                    ></div>
                                    <!-- Sent Icon -->
                                    <div
                                        v-if="message.is_sent"
                                        class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-blue-500 border-2 border-white"
                                    >
                                        <svg
                                            class="h-3 w-3 text-white"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Message Content -->
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-start justify-between gap-4"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <!-- Header with Name, Role, and Badges -->
                                            <div
                                                class="flex items-center gap-2 flex-wrap"
                                            >
                                                <span
                                                    class="text-base font-bold"
                                                    :class="{
                                                        'text-slate-900':
                                                            message.read ||
                                                            message.is_sent,
                                                        'text-emerald-700':
                                                            !message.read &&
                                                            !message.is_sent,
                                                    }"
                                                >
                                                    {{
                                                        message.is_sent
                                                            ? `To: ${message.receiver}`
                                                            : message.sender
                                                    }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide"
                                                    :class="{
                                                        'bg-blue-100 text-blue-700':
                                                            message.is_sent,
                                                        'bg-slate-100 text-slate-700':
                                                            !message.is_sent,
                                                    }"
                                                >
                                                    {{
                                                        message.is_sent
                                                            ? message.receiver_role
                                                            : message.sender_role
                                                    }}
                                                </span>
                                                <span
                                                    v-if="
                                                        !message.read &&
                                                        !message.is_sent
                                                    "
                                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-500 px-2.5 py-1 text-[10px] font-bold text-white shadow-sm"
                                                >
                                                    <svg
                                                        class="h-3 w-3"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                    New
                                                </span>
                                            </div>

                                            <!-- Timestamp -->
                                            <div
                                                class="mt-1.5 flex items-center gap-2"
                                            >
                                                <svg
                                                    class="h-3.5 w-3.5 text-slate-400"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    />
                                                </svg>
                                                <p
                                                    class="text-xs font-medium text-slate-500"
                                                >
                                                    {{
                                                        formatDate(
                                                            message.created_at
                                                        )
                                                    }}
                                                </p>
                                            </div>

                                            <!-- Message Body -->
                                            <div class="mt-4">
                                                <p
                                                    class="text-sm leading-relaxed text-slate-700 whitespace-pre-line"
                                                    :class="{
                                                        'font-semibold':
                                                            !message.read &&
                                                            !message.is_sent,
                                                        'font-normal':
                                                            message.read ||
                                                            message.is_sent,
                                                    }"
                                                >
                                                    <span
                                                        v-if="
                                                            !expandedMessages.has(
                                                                message.id
                                                            ) &&
                                                            message.body
                                                                .length > 150
                                                        "
                                                    >
                                                        {{
                                                            truncateMessage(
                                                                message.body
                                                            )
                                                        }}
                                                    </span>
                                                    <span v-else>
                                                        {{ message.body }}
                                                    </span>
                                                </p>
                                                <button
                                                    v-if="
                                                        message.body.length >
                                                        150
                                                    "
                                                    @click="
                                                        toggleExpand(message.id)
                                                    "
                                                    class="mt-2 text-xs font-medium text-portal-navy hover:text-portal-navy-dark"
                                                >
                                                    {{
                                                        expandedMessages.has(
                                                            message.id
                                                        )
                                                            ? "Show less"
                                                            : "Read more"
                                                    }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="flex-shrink-0">
                                            <button
                                                v-if="
                                                    !message.read &&
                                                    !message.is_sent
                                                "
                                                @click="markAsRead(message.id)"
                                                class="flex items-center gap-1.5 rounded-lg bg-portal-navy px-4 py-2 text-xs font-semibold text-white shadow-md hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 transition-all hover:shadow-lg"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M5 13l4 4L19 7"
                                                    />
                                                </svg>
                                                Mark as read
                                            </button>
                                            <div
                                                v-else-if="message.is_sent"
                                                class="flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-2 text-xs font-semibold text-blue-700"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                                Sent
                                            </div>
                                            <div
                                                v-else
                                                class="flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                                Read
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition-group>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.message-list-enter-active,
.message-list-leave-active {
    transition: all 0.3s ease;
}

.message-list-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.message-list-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>
