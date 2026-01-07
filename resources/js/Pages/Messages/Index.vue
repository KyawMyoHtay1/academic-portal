<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";

const props = defineProps({
    messages: {
        type: Array,
        required: true,
    },
});

const markAsRead = (id) => {
    router.post(route("messages.read", id), {}, { preserveScroll: true });
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
                    v-if="$page.props.auth?.user?.role === 'staff'"
                    :href="route('messages.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                >
                    New Message
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="space-y-3">
                    <div
                        v-if="messages.length === 0"
                        class="portal-card p-6 text-center text-sm text-slate-500"
                    >
                        No messages in your inbox.
                    </div>

                    <div
                        v-for="message in messages"
                        :key="message.id"
                        class="portal-card p-4 flex items-start justify-between gap-4"
                        :class="{
                            'bg-slate-50': message.read,
                            'bg-white': !message.read,
                        }"
                    >
                        <div>
                            <p class="text-xs text-slate-500">
                                From: <span class="font-medium">{{ message.sender }}</span>
                                · <span>{{ message.created_at }}</span>
                            </p>
                            <p class="mt-2 text-sm text-slate-800 whitespace-pre-line">
                                {{ message.body }}
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span
                                v-if="!message.read"
                                class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700"
                            >
                                New
                            </span>
                            <button
                                v-if="!message.read"
                                @click="markAsRead(message.id)"
                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                            >
                                Mark as read
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


