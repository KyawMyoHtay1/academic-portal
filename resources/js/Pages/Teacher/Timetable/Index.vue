<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    courses: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="My Timetable" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                My Timetable
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'My Timetable' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Courses & Sessions
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Sessions for courses you are assigned to teach
                        </p>
                    </div>

                    <div
                        v-if="courses.length === 0"
                        class="rounded-lg bg-slate-50 p-8 text-center"
                    >
                        <h3 class="mt-2 text-sm font-medium text-slate-900">
                            No courses assigned
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            You need to be assigned to courses to view timetable
                            entries.
                        </p>
                    </div>

                    <div v-else class="space-y-6">
                        <div
                            v-for="course in courses"
                            :key="course.id"
                            class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="course.photo"
                                            :src="`/storage/${course.photo}`"
                                            :alt="`Photo for ${course.title}`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-xs font-semibold text-slate-500"
                                        >
                                            {{
                                                course.title
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </div>
                                    <div>
                                        <h3
                                            class="text-sm font-semibold text-slate-900"
                                        >
                                            {{ course.course_code }} -
                                            {{ course.title }}
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="course.timetables.length > 0"
                                class="mt-3 overflow-hidden rounded-md border border-slate-200"
                            >
                                <table
                                    class="min-w-full divide-y divide-slate-200"
                                >
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
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-slate-200 bg-white"
                                    >
                                        <tr
                                            v-for="entry in course.timetables"
                                            :key="entry.id"
                                            class="bg-white"
                                        >
                                            <td
                                                class="px-4 py-3 text-sm text-slate-700"
                                            >
                                                <div
                                                    class="flex items-center gap-3"
                                                >
                                                    <div
                                                        class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                                    >
                                                        <img
                                                            v-if="
                                                                entry.subject_photo
                                                            "
                                                            :src="`/storage/${entry.subject_photo}`"
                                                            :alt="`Photo for ${entry.subject_title}`"
                                                            class="h-full w-full object-cover"
                                                        />
                                                        <span
                                                            v-else
                                                            class="text-xs font-semibold text-slate-500"
                                                        >
                                                            {{
                                                                (entry.subject_title ||
                                                                    "N/A")[0]
                                                            }}
                                                        </span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="font-medium"
                                                            >{{
                                                                entry.subject_code ||
                                                                "N/A"
                                                            }}</span
                                                        >
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
                                                class="px-4 py-3 text-sm text-slate-700"
                                            >
                                                {{ entry.day_of_week }}
                                            </td>
                                            <td
                                                class="px-4 py-3 text-sm text-slate-700"
                                            >
                                                {{ entry.start_time }} -
                                                {{ entry.end_time }}
                                            </td>
                                            <td
                                                class="px-4 py-3 text-sm text-slate-700"
                                            >
                                                {{ entry.location || "-" }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="mt-3 text-sm text-slate-500">
                                No timetable entries for this course yet.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
