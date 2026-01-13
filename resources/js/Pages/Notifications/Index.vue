<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";

const props = defineProps({
    notifications: {
        type: Array,
        required: true,
    },
});

const markAsRead = (id) => {
    router.post(route("notifications.read", id), {}, { preserveScroll: true });
};

const markAllAsRead = () => {
    router.post(route("notifications.read-all"), {}, { preserveScroll: true });
};
</script>

<template>
    <Head title="Notifications" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Notifications
                </h2>
                <button
                    v-if="notifications.some((n) => !n.read_at)"
                    @click="markAllAsRead"
                    class="rounded-full bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-portal-navy/60 hover:bg-portal-navy/90"
                >
                    Mark all as read
                </button>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Notifications' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Header Banner -->
                <div
                    class="mb-6 overflow-hidden rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white shadow-lg"
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
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">
                                Notifications Center
                            </h3>
                            <p class="mt-1 text-sm text-white/90">
                                Stay informed about grades, fees, timetable and
                                attendance updates
                            </p>
                        </div>
                    </div>
                </div>

                <div class="portal-card overflow-hidden">
                    <div
                        class="border-b border-slate-200 bg-slate-50 px-6 py-4"
                    >
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Recent notifications
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            System updates about your grades, fees, timetable
                            and attendance.
                        </p>
                    </div>

                    <div
                        v-if="notifications.length === 0"
                        class="p-6 text-center text-sm text-slate-500"
                    >
                        You have no notifications yet.
                    </div>

                    <ul v-else class="divide-y divide-slate-100">
                        <li
                            v-for="notification in notifications"
                            :key="notification.id"
                            class="flex items-start gap-4 px-6 py-5 transition-colors"
                            :class="
                                notification.read_at
                                    ? 'bg-white hover:bg-slate-50'
                                    : 'bg-portal-sky/5 hover:bg-portal-sky/10'
                            "
                        >
                            <!-- Icon -->
                            <div class="mt-1 flex-shrink-0">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full"
                                    :class="{
                                        'bg-emerald-100':
                                            notification.type === 'grade',
                                        'bg-blue-100':
                                            notification.type === 'fee',
                                        'bg-indigo-100':
                                            notification.type === 'timetable',
                                        'bg-amber-100':
                                            notification.type === 'attendance',
                                        'bg-slate-100': ![
                                            'grade',
                                            'fee',
                                            'timetable',
                                            'attendance',
                                        ].includes(notification.type),
                                    }"
                                >
                                    <svg
                                        v-if="notification.type === 'grade'"
                                        class="h-5 w-5 text-emerald-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                        />
                                    </svg>
                                    <svg
                                        v-else-if="notification.type === 'fee'"
                                        class="h-5 w-5 text-blue-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <svg
                                        v-else-if="
                                            notification.type === 'timetable'
                                        "
                                        class="h-5 w-5 text-indigo-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <svg
                                        v-else-if="
                                            notification.type === 'attendance'
                                        "
                                        class="h-5 w-5 text-amber-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="h-5 w-5 text-slate-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p
                                                class="text-sm font-semibold text-slate-900"
                                            >
                                                {{ notification.title }}
                                            </p>
                                            <div
                                                v-if="!notification.read_at"
                                                class="h-2 w-2 rounded-full bg-portal-gold"
                                            ></div>
                                        </div>
                                        <p
                                            class="mt-1.5 text-sm leading-relaxed text-slate-700"
                                        >
                                            {{ notification.message }}
                                        </p>
                                        <div
                                            class="mt-3 flex items-center gap-3"
                                        >
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-semibold capitalize"
                                                :class="{
                                                    'bg-emerald-100 text-emerald-800':
                                                        notification.type ===
                                                        'grade',
                                                    'bg-blue-100 text-blue-800':
                                                        notification.type ===
                                                        'fee',
                                                    'bg-indigo-100 text-indigo-800':
                                                        notification.type ===
                                                        'timetable',
                                                    'bg-amber-100 text-amber-800':
                                                        notification.type ===
                                                        'attendance',
                                                    'bg-slate-100 text-slate-800':
                                                        ![
                                                            'grade',
                                                            'fee',
                                                            'timetable',
                                                            'attendance',
                                                        ].includes(
                                                            notification.type
                                                        ),
                                                }"
                                            >
                                                {{ notification.type }}
                                            </span>
                                            <button
                                                v-if="!notification.read_at"
                                                @click="
                                                    markAsRead(notification.id)
                                                "
                                                class="text-xs font-medium text-portal-navy hover:text-portal-navy-dark hover:underline"
                                            >
                                                Mark as read
                                            </button>
                                        </div>
                                    </div>
                                    <p
                                        class="flex-shrink-0 whitespace-nowrap text-xs text-slate-500"
                                    >
                                        {{ notification.created_at }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
