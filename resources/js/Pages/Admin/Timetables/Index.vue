<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineProps({
    timetables: {
        type: Object,
        required: true,
    },
});

const deleteEntry = (id) => {
    if (
        !confirm(
            "Are you sure you want to delete this timetable entry? This action cannot be undone."
        )
    ) {
        return;
    }

    router.delete(route("admin.timetables.destroy", id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Timetable Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Timetable Management
                </h2>
                <Link
                    :href="route('admin.timetables.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                >
                    Create Entry
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            All Timetable Entries
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Manage course schedules
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Subject
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Course
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Day
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Time
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Location
                                    </th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-if="timetables.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No timetable entries found. Create your
                                        first entry.
                                    </td>
                                </tr>
                                <tr
                                    v-for="entry in timetables.data"
                                    :key="entry.id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                >
                                    <td
                                        class="px-4 py-4 text-sm font-medium text-slate-900"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                            >
                                                <img
                                                    v-if="entry.subject_photo"
                                                    :src="`/storage/${entry.subject_photo}`"
                                                    :alt="`Photo for ${entry.subject_title || 'N/A'}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{ (entry.subject_title || entry.subject_code || 'N')[0].toUpperCase() }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span>{{
                                                    entry.subject_code || "N/A"
                                                }}</span>
                                                <span
                                                    class="text-xs text-slate-500"
                                                >{{
                                                    entry.subject_title || ""
                                                }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-700"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                            >
                                                <img
                                                    v-if="entry.course_photo"
                                                    :src="`/storage/${entry.course_photo}`"
                                                    :alt="`Photo for ${entry.course_title}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{ entry.course_title.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                            <div>
                                                {{ entry.course_code }} -
                                                {{ entry.course_title }}
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-700"
                                    >
                                        {{ entry.day_of_week }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-700"
                                    >
                                        {{ entry.start_time }} -
                                        {{ entry.end_time }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-700"
                                    >
                                        {{ entry.location || "-" }}
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm">
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.timetables.edit',
                                                        entry.id
                                                    )
                                                "
                                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteEntry(entry.id)"
                                                class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="timetables.links && timetables.links.length > 3"
                        class="mt-4 flex items-center justify-between border-t border-slate-200 px-4 py-3 sm:px-6"
                    >
                        <div class="flex flex-1 justify-between sm:hidden">
                            <Link
                                v-if="timetables.prev_page_url"
                                :href="timetables.prev_page_url"
                                class="relative inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                            >
                                Previous
                            </Link>
                            <Link
                                v-if="timetables.next_page_url"
                                :href="timetables.next_page_url"
                                class="relative ml-3 inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                            >
                                Next
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
