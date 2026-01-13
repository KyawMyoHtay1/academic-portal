<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineProps({
    announcements: {
        type: Array,
        required: true,
    },
});

const deleteAnnouncement = (id) => {
    if (
        !confirm(
            "Are you sure you want to delete this announcement? This action cannot be undone."
        )
    ) {
        return;
    }

    router.delete(route("admin.announcements.destroy", id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Announcements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Announcements
                </h2>
                <Link
                    :href="route('admin.announcements.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                >
                    Create Announcement
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Announcements' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            All Announcements
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Manage announcements visible to all users
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-if="announcements.length === 0"
                            class="rounded-lg bg-slate-50 p-6 text-center text-sm text-slate-500"
                        >
                            No announcements posted yet.
                        </div>

                        <div
                            v-for="announcement in announcements"
                            :key="announcement.id"
                            class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1">
                                    <!-- Author Avatar -->
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-10 w-10 overflow-hidden rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center"
                                        >
                                            <img
                                                v-if="announcement.author_photo"
                                                :src="`/storage/${announcement.author_photo}`"
                                                :alt="`Photo of ${announcement.author}`"
                                                class="h-full w-full object-cover"
                                            />
                                            <span
                                                v-else
                                                class="text-xs font-semibold text-slate-500"
                                            >
                                                {{
                                                    announcement.author
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3
                                            class="text-base font-semibold text-slate-900"
                                        >
                                            {{ announcement.title }}
                                        </h3>
                                        <p class="mt-1 text-xs text-slate-500">
                                            By {{ announcement.author }} ·
                                            {{ announcement.created_at }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="
                                            route(
                                                'admin.announcements.edit',
                                                announcement.id
                                            )
                                        "
                                        class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="
                                            deleteAnnouncement(announcement.id)
                                        "
                                        class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                            <p
                                class="mt-3 text-sm text-slate-700 whitespace-pre-line"
                            >
                                {{ announcement.body }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
