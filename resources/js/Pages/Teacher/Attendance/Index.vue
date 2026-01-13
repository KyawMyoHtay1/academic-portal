<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    subjects: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Mark Attendance" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Mark Attendance
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Select Subject
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Choose a subject to mark attendance for enrolled
                            students
                        </p>
                    </div>

                    <!-- Subjects List -->
                    <div
                        v-if="subjects.length === 0"
                        class="rounded-lg bg-slate-50 p-8 text-center"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                            />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">
                            No subjects assigned
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            You need to be assigned to courses with subjects
                            before you can mark attendance.
                        </p>
                    </div>

                    <div
                        v-else
                        class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <Link
                            v-for="subject in subjects"
                            :key="subject.id"
                            :href="route('teacher.attendance.show', subject.id)"
                            class="group rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-portal-navy hover:shadow-md"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3
                                        class="text-lg font-semibold text-slate-900 group-hover:text-portal-navy"
                                    >
                                        {{ subject.subject_code }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ subject.title }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ subject.course_code }} -
                                        {{ subject.course_title }}
                                    </p>
                                </div>
                                <svg
                                    class="h-5 w-5 text-slate-400 group-hover:text-portal-navy"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
