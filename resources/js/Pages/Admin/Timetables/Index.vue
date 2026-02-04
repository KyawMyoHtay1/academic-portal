<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import TimetableWeekGrid from "@/Components/TimetableWeekGrid.vue";
import { computed, ref } from "vue";

const props = defineProps({
    timetables: {
        type: Object,
        required: true,
    },
});

const viewMode = ref("table"); // table | week
const query = ref("");
const selectedDay = ref("all");

const entries = computed(() => props.timetables?.data ?? []);

const filteredEntries = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = entries.value;

    if (selectedDay.value !== "all") {
        list = list.filter((e) => e.day_of_week === selectedDay.value);
    }

    if (q) {
        list = list.filter((e) => {
            const subj = `${e.subject_code ?? ""} ${e.subject_title ?? ""}`.toLowerCase();
            const course = `${e.course_code ?? ""} ${e.course_title ?? ""}`.toLowerCase();
            const loc = (e.location ?? "").toLowerCase();
            return subj.includes(q) || course.includes(q) || loc.includes(q);
        });
    }

    return list;
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

<script>
import Pagination from "@/Components/Pagination.vue";

export default {
    components: {
        Pagination,
    },
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

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Timetable Management' }]" />
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

                    <!-- Controls -->
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <div class="relative w-full sm:w-80">
                                <input
                                    v-model="query"
                                    type="text"
                                    placeholder="Search subject, course, location…"
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                                <button
                                    v-if="query"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="query = ''"
                                >
                                    <span class="sr-only">Clear</span>
                                    ✕
                                </button>
                            </div>
                            <select
                                v-model="selectedDay"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-48"
                            >
                                <option value="all">All days</option>
                                <option>Monday</option>
                                <option>Tuesday</option>
                                <option>Wednesday</option>
                                <option>Thursday</option>
                                <option>Friday</option>
                                <option>Saturday</option>
                                <option>Sunday</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="inline-flex rounded-md bg-slate-100 p-1">
                                <button
                                    type="button"
                                    class="rounded-md px-3 py-1.5 text-xs font-semibold"
                                    :class="viewMode === 'table' ? 'bg-white text-slate-900 shadow' : 'text-slate-600 hover:text-slate-900'"
                                    @click="viewMode = 'table'"
                                >
                                    Table
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md px-3 py-1.5 text-xs font-semibold"
                                    :class="viewMode === 'week' ? 'bg-white text-slate-900 shadow' : 'text-slate-600 hover:text-slate-900'"
                                    @click="viewMode = 'week'"
                                >
                                    Week view
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="viewMode === 'week'">
                        <div v-if="filteredEntries.length === 0" class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-600">
                            No entries match your filters.
                        </div>
                        <TimetableWeekGrid
                            v-else
                            :entries="filteredEntries"
                            :showCourse="true"
                            :days="['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']"
                        />
                    </div>

                    <div v-else class="overflow-x-auto">
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
                                <tr v-if="filteredEntries.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No timetable entries match your filters.
                                    </td>
                                </tr>
                                <tr
                                    v-for="entry in filteredEntries"
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
                                                    :alt="`Photo for ${
                                                        entry.subject_title ||
                                                        'N/A'
                                                    }`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{
                                                        (entry.subject_title ||
                                                            entry.subject_code ||
                                                            "N")[0].toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span>{{
                                                    entry.subject_code || "N/A"
                                                }}</span>
                                                <span
                                                    class="text-xs text-slate-500"
                                                    >{{
                                                        entry.subject_title ||
                                                        ""
                                                    }}</span
                                                >
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
                                                    {{
                                                        entry.course_title
                                                            .charAt(0)
                                                            .toUpperCase()
                                                    }}
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
                        v-if="viewMode === 'table'"
                        class="mt-4 border-t border-slate-200 px-4 py-3 sm:px-6"
                    >
                        <Pagination :links="timetables.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
