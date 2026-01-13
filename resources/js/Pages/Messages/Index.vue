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

const statusBadgeClass = (read) =>
    read
        ? "inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600"
        : "inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700";
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

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Header Banner -->
                <div class="mb-6 overflow-hidden rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 p-8 text-white shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Messages</h3>
                            <p class="mt-1 text-sm text-white/90">Communicate with students, teachers, and staff members</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div
                        v-if="messages.length === 0"
                        class="portal-card p-12 text-center"
                    >
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-slate-900">No messages in your inbox</h3>
                        <p class="mt-1 text-sm text-slate-500">Start a conversation by sending a new message</p>
                    </div>

                    <div
                        v-for="message in messages"
                        :key="message.id"
                        class="portal-card overflow-hidden p-5 transition-shadow"
                        :class="{
                            'bg-slate-50 hover:bg-slate-100': message.read,
                            'bg-white hover:shadow-md border-l-4 border-portal-navy': !message.read,
                        }"
                    >
                        <div class="flex items-start gap-4">
                            <!-- Sender Avatar -->
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 overflow-hidden rounded-full border-2 border-slate-200 bg-slate-100 flex items-center justify-center"
                                    :class="{
                                        'border-emerald-300': !message.read,
                                        'border-slate-300': message.read,
                                    }"
                                >
                                    <img
                                        v-if="message.sender_photo"
                                        :src="`/storage/${message.sender_photo}`"
                                        :alt="`Photo of ${message.sender}`"
                                        class="h-full w-full object-cover"
                                    />
                                    <span
                                        v-else
                                        class="text-sm font-semibold text-slate-500"
                                    >
                                        {{ message.sender.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Message Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-semibold text-slate-900">
                                                {{ message.sender }}
                                            </span>
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-slate-700"
                                            >
                                                {{ message.sender_role }}
                                            </span>
                                            <span
                                                v-if="!message.read"
                                                class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"
                                            >
                                                New
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ message.created_at }}
                                        </p>
                                        <p
                                            class="mt-3 text-sm leading-relaxed text-slate-800 whitespace-pre-line"
                                            :class="{ 'font-medium': !message.read }"
                                        >
                                            {{ message.body }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button
                                            v-if="!message.read"
                                            @click="markAsRead(message.id)"
                                            class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 transition-colors"
                                        >
                                            Mark as read
                                        </button>
                                        <span
                                            v-else
                                            class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600"
                                        >
                                            Read
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


