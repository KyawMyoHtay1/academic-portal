<script setup>
import { computed, ref, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";

const props = defineProps({
    messages: { type: Object, required: true },
    unreadCount: { type: Number, required: true },
    filters: { type: Object, default: () => ({}) },
});

const items = computed(() => [
    { label: "Feedback Messages" },
]);

const search = ref(props.filters?.q ?? "");
let searchTimer = null;

watch(
    search,
    (value) => {
        if (searchTimer) window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => {
            router.get(
                route("admin.feedback-messages.index"),
                { q: value || undefined },
                { preserveState: true, replace: true, preserveScroll: true }
            );
        }, 250);
    },
    { flush: "post" }
);

function markRead(messageId) {
    router.post(route("admin.feedback-messages.read", messageId), {}, { preserveScroll: true });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Feedback Messages" />

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Feedback Messages
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
                <div
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200"
                >
                    <div class="border-b border-slate-200 px-6 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-slate-600">
                                Feedback submitted from the public Feedback & Suggestions page.
                            </p>

                            <div class="w-full sm:max-w-sm">
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
                                        placeholder="Search name, email, type, message..."
                                    />
                                    <button
                                        v-if="search"
                                        type="button"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-700"
                                        @click="search = ''"
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
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"
                                    >
                                        From
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"
                                    >
                                        Type
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"
                                    >
                                        Message
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600"
                                    >
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
                                                :class="
                                                    m.is_read
                                                        ? 'bg-slate-300'
                                                        : 'bg-amber-500'
                                                "
                                                :title="m.is_read ? 'Read' : 'Unread'"
                                            />
                                            <div>
                                                <div class="font-semibold text-slate-900">
                                                    {{ m.name }}
                                                </div>
                                                <div class="text-sm text-slate-600">
                                                    {{ m.email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold capitalize text-slate-700">
                                            {{ m.type || "other" }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xl text-sm text-slate-700 whitespace-pre-line">
                                            {{ m.message }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ new Date(m.created_at).toLocaleString() }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                            :disabled="m.is_read"
                                            @click="markRead(m.id)"
                                        >
                                            Mark read
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="messages.data.length === 0">
                                    <td
                                        class="px-6 py-6 text-center text-sm text-slate-600"
                                        colspan="5"
                                    >
                                        <span v-if="search">No results for "{{ search }}".</span>
                                        <span v-else>No feedback messages yet.</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="messages.links?.length"
                        class="border-t border-slate-200 px-6 py-4"
                    >
                        <div class="flex flex-wrap gap-2">
                            <a
                                v-for="(link, idx) in messages.links"
                                :key="idx"
                                :href="link.url || '#'"
                                class="rounded-lg px-3 py-2 text-sm font-semibold"
                                :class="[
                                    link.active
                                        ? 'bg-slate-900 text-white'
                                        : 'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50',
                                    !link.url
                                        ? 'pointer-events-none opacity-50'
                                        : '',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

