<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";

const MAX_MESSAGE_LENGTH = 2000;

const props = defineProps({
    recipients: {
        type: Array,
        required: true,
    },
    prefillRecipient: {
        type: String,
        default: "",
    },
});

const form = useForm({
    receiver_id: props.prefillRecipient || "",
    body: "",
});

const page = usePage();
const recipientQuery = ref("");
const draftSavedAt = ref(null);

const authUserId = computed(() => page.props.auth?.user?.id ?? "guest");
const draftStorageKey = computed(() => `messages.create.draft.${authUserId.value}`);

const selectedRecipient = computed(() =>
    props.recipients.find((r) => String(r.id) === String(form.receiver_id)) || null
);

const filteredRecipients = computed(() => {
    const q = recipientQuery.value.trim().toLowerCase();
    if (!q) return props.recipients;

    return props.recipients.filter((recipient) => {
        const name = String(recipient.name ?? "").toLowerCase();
        const email = String(recipient.email ?? "").toLowerCase();
        const role = String(recipient.role ?? "").toLowerCase();

        return name.includes(q) || email.includes(q) || role.includes(q);
    });
});

const recipientOptions = computed(() => {
    if (!selectedRecipient.value) return filteredRecipients.value;

    const hasSelected = filteredRecipients.value.some(
        (recipient) => String(recipient.id) === String(selectedRecipient.value?.id)
    );

    if (hasSelected) return filteredRecipients.value;

    return [selectedRecipient.value, ...filteredRecipients.value];
});

const bodyLength = computed(() => String(form.body ?? "").length);
const remainingChars = computed(() => MAX_MESSAGE_LENGTH - bodyLength.value);
const counterTone = computed(() =>
    remainingChars.value <= 120 ? "text-amber-600" : "text-slate-500"
);

const persistDraft = debounce(() => {
    if (typeof window === "undefined") return;

    const payload = {
        receiver_id: String(form.receiver_id ?? ""),
        body: String(form.body ?? ""),
        saved_at: Date.now(),
    };

    if (!payload.receiver_id && !payload.body.trim()) {
        window.localStorage.removeItem(draftStorageKey.value);
        draftSavedAt.value = null;
        return;
    }

    window.localStorage.setItem(draftStorageKey.value, JSON.stringify(payload));
    draftSavedAt.value = payload.saved_at;
}, 300);

const restoreDraft = () => {
    if (typeof window === "undefined") return;

    const raw = window.localStorage.getItem(draftStorageKey.value);
    if (!raw) return;

    try {
        const draft = JSON.parse(raw);

        if (!props.prefillRecipient && !form.receiver_id && draft.receiver_id) {
            form.receiver_id = String(draft.receiver_id);
        }

        if (!String(form.body ?? "").trim() && typeof draft.body === "string") {
            form.body = draft.body;
        }

        const savedAt = Number(draft.saved_at ?? 0);
        draftSavedAt.value = Number.isFinite(savedAt) && savedAt > 0 ? savedAt : null;
    } catch {
        window.localStorage.removeItem(draftStorageKey.value);
        draftSavedAt.value = null;
    }
};

const clearDraft = () => {
    if (typeof window !== "undefined") {
        window.localStorage.removeItem(draftStorageKey.value);
    }

    draftSavedAt.value = null;
    form.body = "";
    if (!props.prefillRecipient) {
        form.receiver_id = "";
    }
    form.clearErrors();
};

const formatDraftSavedAt = (value) => {
    if (!value) return "";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "";

    return date.toLocaleTimeString(undefined, {
        hour: "2-digit",
        minute: "2-digit",
    });
};

const submit = () => {
    form.post(route("messages.store"), {
        onSuccess: () => {
            if (typeof window !== "undefined") {
                window.localStorage.removeItem(draftStorageKey.value);
            }
            draftSavedAt.value = null;
        },
    });
};

watch(
    () => [form.receiver_id, form.body],
    () => {
        persistDraft();
    }
);

onMounted(() => {
    restoreDraft();
});

onBeforeUnmount(() => {
    persistDraft.flush();
    persistDraft.cancel();
});
</script>

<template>
    <Head title="New Message" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    New Message
                </h2>
                <Link
                    :href="route('messages.index')"
                    class="rounded-md bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50"
                >
                    Back to Inbox
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Messages', href: route('messages.index') },
                        { label: 'New Message' },
                    ]"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <div>
                                <label
                                    for="receiver_id"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    To <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mt-1">
                                    <input
                                        v-model="recipientQuery"
                                        type="text"
                                        placeholder="Quick search recipient by name, email, or role"
                                        class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                    />
                                    <button
                                        v-if="recipientQuery"
                                        type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                        @click="recipientQuery = ''"
                                    >
                                        <span class="sr-only">Clear recipient search</span>
                                        X
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">
                                    Showing {{ filteredRecipients.length }} recipient{{ filteredRecipients.length === 1 ? "" : "s" }}
                                </p>
                                <select
                                    id="receiver_id"
                                    v-model="form.receiver_id"
                                    required
                                    class="mt-2 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.receiver_id,
                                    }"
                                >
                                    <option value="">Select recipient</option>
                                    <option
                                        v-for="recipient in recipientOptions"
                                        :key="recipient.id"
                                        :value="recipient.id"
                                    >
                                        {{ recipient.name }} ({{ recipient.email }}) - {{ recipient.role }}
                                    </option>
                                </select>
                                <p
                                    v-if="recipientQuery && filteredRecipients.length === 0"
                                    class="mt-1 text-xs text-amber-700"
                                >
                                    No recipient matches this search.
                                </p>

                                <div
                                    v-if="form.receiver_id"
                                    class="mt-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3"
                                >
                                    <div
                                        class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100"
                                    >
                                        <img
                                            v-if="selectedRecipient?.photo_thumb ?? selectedRecipient?.photo"
                                            :src="`/storage/${selectedRecipient.photo_thumb ?? selectedRecipient.photo}`"
                                            :alt="`Photo of ${selectedRecipient?.name}`"
                                            loading="lazy"
                                            decoding="async"
                                            fetchpriority="low"
                                            width="40"
                                            height="40"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-xs font-semibold text-slate-500"
                                        >
                                            {{ (selectedRecipient?.name?.charAt(0) ?? "?").toUpperCase() }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">
                                            {{ selectedRecipient?.name }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            {{ selectedRecipient?.email }} - {{ selectedRecipient?.role }}
                                        </p>
                                    </div>
                                </div>
                                <p
                                    v-if="form.errors.receiver_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.receiver_id }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="body"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="body"
                                    v-model="form.body"
                                    rows="6"
                                    required
                                    maxlength="2000"
                                    placeholder="Write your message..."
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.body,
                                    }"
                                ></textarea>
                                <div class="mt-2 flex items-center justify-between gap-3">
                                    <p class="text-xs text-slate-500">
                                        Draft autosaves on this device.
                                        <span v-if="draftSavedAt">
                                            Last saved at {{ formatDraftSavedAt(draftSavedAt) }}.
                                        </span>
                                    </p>
                                    <button
                                        type="button"
                                        class="rounded-md border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                                        :disabled="!form.receiver_id && !form.body.trim()"
                                        @click="clearDraft"
                                    >
                                        Discard draft
                                    </button>
                                </div>
                                <p
                                    class="mt-1 text-right text-xs"
                                    :class="counterTone"
                                >
                                    {{ bodyLength }}/{{ MAX_MESSAGE_LENGTH }}
                                </p>
                                <p
                                    v-if="form.errors.body"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.body }}
                                </p>
                            </div>

                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
                                <Link
                                    :href="route('messages.index')"
                                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.receiver_id || !form.body.trim()"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Sending...</span>
                                    <span v-else>Send</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
