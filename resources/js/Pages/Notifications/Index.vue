<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
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

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Recent notifications
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            System updates about your grades, fees, timetable and attendance.
                        </p>
                    </div>

                    <div v-if="notifications.length === 0" class="p-6 text-center text-sm text-slate-500">
                        You have no notifications yet.
                    </div>

                    <ul v-else class="divide-y divide-slate-100">
                        <li
                            v-for="notification in notifications"
                            :key="notification.id"
                            class="flex items-start gap-3 px-6 py-4"
                            :class="notification.read_at ? 'bg-white' : 'bg-portal-sky/10'"
                        >
                            <div class="mt-1 h-2 w-2 rounded-full" :class="notification.read_at ? 'bg-slate-200' : 'bg-portal-gold'"></div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ notification.title }}
                                        </p>
                                        <p class="mt-1 text-sm text-slate-700">
                                            {{ notification.message }}
                                        </p>
                                    </div>
                                    <p class="whitespace-nowrap text-xs text-slate-500">
                                        {{ notification.created_at }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <span
                                        class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize"
                                        :class="{
                                            'bg-emerald-100 text-emerald-800': notification.type === 'grade',
                                            'bg-blue-100 text-blue-800': notification.type === 'fee',
                                            'bg-indigo-100 text-indigo-800': notification.type === 'timetable',
                                            'bg-amber-100 text-amber-800': notification.type === 'attendance',
                                            'bg-slate-100 text-slate-800': !['grade', 'fee', 'timetable', 'attendance'].includes(notification.type),
                                        }"
                                    >
                                        {{ notification.type }}
                                    </span>
                                    <button
                                        v-if="!notification.read_at"
                                        @click="markAsRead(notification.id)"
                                        class="text-xs font-medium text-portal-navy hover:underline"
                                    >
                                        Mark as read
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


