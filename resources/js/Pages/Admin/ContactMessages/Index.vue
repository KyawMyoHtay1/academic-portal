<script setup>
import { computed, ref, watch } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";

const props = defineProps({
    messages: { type: Object, required: true },
    unreadCount: { type: Number, required: true },
    statusCounts: {
        type: Object,
        default: () => ({ unread: 0, read: 0, replied: 0 }),
    },
    filters: { type: Object, default: () => ({}) },
});

const items = computed(() => [
    { label: "Contact Messages" },
]);

const search = ref(props.filters?.q ?? "");
const selectedStatus = ref(props.filters?.status ?? "all");
let searchTimer = null;

const replyModalOpen = ref(false);
const replyTargetMessage = ref(null);

const replyForm = useForm({
    reply: "",
});

function applyFilters(qValue = search.value) {
    router.get(
        route("admin.contact-messages.index"),
        {
            q: qValue || undefined,
            status:
                selectedStatus.value !== "all"
                    ? selectedStatus.value
                    : undefined,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    );
}

watch(search, (value) => {
    if (searchTimer) window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => {
        applyFilters(value);
    }, 250);
}, { flush: "post" });

watch(selectedStatus, () => {
    applyFilters();
});

function markRead(messageId) {
    router.post(route("admin.contact-messages.read", messageId), {}, { preserveScroll: true });
}

function openReplyModal(message) {
    replyTargetMessage.value = message;
    replyForm.reply = message.reply || "";
    replyModalOpen.value = true;
}

function closeReplyModal() {
    replyModalOpen.value = false;
    replyTargetMessage.value = null;
    replyForm.reset();
}

function submitReply() {
    if (!replyTargetMessage.value) return;
    replyForm.post(route("admin.contact-messages.reply", replyTargetMessage.value.id), {
        preserveScroll: true,
        onSuccess: () => closeReplyModal(),
    });
}
</script>

<script>
import Pagination from "@/Components/Pagination.vue";

export default {
    components: {
        Pagination,
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Contact Messages" />

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Contact Messages
                </h2>
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700"
                >
                    <span
                        class="h-2 w-2 rounded-full"
                        :class="unreadCount > 0 ? 'bg-amber-500' : 'bg-emerald-500'"
                    ></span>
                    <span>{{ unreadCount }} unread</span>
                </div>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="items" />
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <div class="mb-3 grid gap-2 sm:grid-cols-3">
                            <div class="rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                                Unread: <span class="font-semibold">{{ statusCounts.unread ?? 0 }}</span>
                            </div>
                            <div class="rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-800">
                                Read: <span class="font-semibold">{{ statusCounts.read ?? 0 }}</span>
                            </div>
                            <div class="rounded-lg bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
                                Replied: <span class="font-semibold">{{ statusCounts.replied ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-slate-600">
                                Messages submitted from the public Contact page.
                            </p>

                            <div class="grid w-full gap-2 sm:max-w-2xl sm:grid-cols-2">
                                <select
                                    v-model="selectedStatus"
                                    class="w-full rounded-lg border border-slate-300 bg-white py-2 px-3 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                                >
                                    <option value="all">All statuses</option>
                                    <option value="unread">Unread</option>
                                    <option value="read">Read</option>
                                    <option value="replied">Replied</option>
                                </select>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input
                                        v-model="search"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-9 text-sm text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                                        placeholder="Search name, email, subject, message..."
                                    />
                                    <button
                                        v-if="search"
                                        type="button"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-700"
                                        @click="() => { search = ''; applyFilters(''); }"
                                        aria-label="Clear search"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        From
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Subject
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Message
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Reply
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-for="m in messages.data"
                                    :key="m.id"
                                    class="hover:bg-slate-50"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="inline-flex h-2.5 w-2.5 rounded-full"
                                                :class="m.is_read ? 'bg-slate-300' : 'bg-amber-500'"
                                                :title="m.is_read ? 'Read' : 'Unread'"
                                            />
                                            <div>
                                                <div class="font-semibold text-slate-900">
                                                    {{ m.first_name }} {{ m.last_name }}
                                                </div>
                                                <div class="text-sm text-slate-600">
                                                    {{ m.email }}<span v-if="m.phone"> • {{ m.phone }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-900">
                                            {{ m.subject }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xl text-sm text-slate-700">
                                            {{ m.message }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ new Date(m.created_at).toLocaleString() }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            v-if="m.reply"
                                            class="max-w-xs truncate block text-slate-700"
                                            :title="m.reply"
                                        >
                                            {{ m.reply }}
                                        </span>
                                        <span
                                            v-else-if="m.replied_at"
                                            class="text-slate-500 italic"
                                        >
                                            Replied
                                        </span>
                                        <span v-else class="text-slate-400">—</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100"
                                                @click="openReplyModal(m)"
                                            >
                                                {{ m.reply || m.replied_at ? "Edit reply" : "Reply" }}
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50"
                                                :disabled="m.is_read"
                                                @click="markRead(m.id)"
                                            >
                                                Mark read
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="messages.data.length === 0">
                                    <td class="px-6 py-6 text-center text-sm text-slate-600" colspan="6">
                                        <span v-if="search">No results for "{{ search }}".</span>
                                        <span v-else>No contact messages yet.</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 border-t border-slate-200 px-6 py-4">
                        <Pagination :links="messages.links" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Reply Modal -->
        <Teleport to="body">
            <div
                v-if="replyModalOpen"
                class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true"
            >
                <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div
                        class="fixed inset-0 bg-slate-500/75 transition-opacity"
                        aria-hidden="true"
                        @click="closeReplyModal"
                    />
                    <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                    <div
                        class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                    >
                        <div>
                            <h3
                                id="modal-title"
                                class="text-lg font-semibold leading-6 text-slate-900"
                            >
                                Reply to {{ replyTargetMessage?.first_name }} {{ replyTargetMessage?.last_name }}
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ replyTargetMessage?.email }}
                            </p>
                            <div class="mt-4">
                                <label for="reply-text" class="block text-sm font-medium text-slate-700">Your reply</label>
                                <textarea
                                    id="reply-text"
                                    v-model="replyForm.reply"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{ 'border-red-300': replyForm.errors.reply }"
                                    placeholder="Type your reply here..."
                                />
                                <p v-if="replyForm.errors.reply" class="mt-1 text-sm text-red-600">
                                    {{ replyForm.errors.reply }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                            <button
                                type="button"
                                :disabled="replyForm.processing"
                                class="inline-flex w-full justify-center rounded-md bg-portal-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 disabled:opacity-50 sm:col-start-2"
                                @click="submitReply"
                            >
                                {{ replyForm.processing ? "Saving..." : "Save reply" }}
                            </button>
                            <button
                                type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 sm:col-start-1 sm:mt-0"
                                @click="closeReplyModal"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>
