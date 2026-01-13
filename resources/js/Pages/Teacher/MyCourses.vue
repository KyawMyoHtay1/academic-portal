<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";

defineProps({
    courses: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <Head title="My Teaching Subjects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                My Teaching Subjects
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Teacher profile summary -->
                <div class="mb-4 portal-card flex items-center gap-4 p-6">
                    <div
                        class="h-14 w-14 overflow-hidden rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center"
                    >
                        <img
                            v-if="user.photo"
                            :src="`/storage/${user.photo}`"
                            :alt="`Photo of ${user.name}`"
                            class="h-full w-full object-cover"
                        />
                        <span
                            v-else
                            class="text-base font-semibold text-slate-500"
                        >
                            {{ user.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">
                            {{ user.name }}
                        </h3>
                        <p class="text-xs text-slate-600">
                            Teacher &bull; My assigned teaching subjects
                        </p>
                    </div>
                </div>

                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Assigned Subjects
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Subjects you are assigned to teach, grouped by
                            course
                        </p>
                    </div>

                    <!-- Courses with Subjects -->
                    <div v-if="courses.length > 0" class="space-y-6">
                        <div
                            v-for="course in courses"
                            :key="course.id"
                            class="rounded-lg border border-slate-200 bg-white"
                        >
                            <div
                                class="border-b border-slate-200 bg-slate-50 px-4 py-3"
                            >
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                        >
                                            <img
                                                v-if="course.course_photo"
                                                :src="`/storage/${course.course_photo}`"
                                                :alt="`Photo for ${course.course_title}`"
                                                class="h-full w-full object-cover"
                                            />
                                            <span
                                                v-else
                                                class="text-xs font-semibold text-slate-500"
                                            >
                                                {{ course.course_title[0] }}
                                            </span>
                                        </div>
                                        <div>
                                            <h3
                                                class="text-sm font-semibold text-slate-900"
                                            >
                                                {{ course.course_code }} -
                                                {{ course.course_title }}
                                            </h3>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center rounded-full bg-portal-navy/10 px-2.5 py-0.5 text-xs font-medium text-portal-navy"
                                    >
                                        {{ course.subjects.length }}
                                        {{
                                            course.subjects.length === 1
                                                ? "subject"
                                                : "subjects"
                                        }}
                                    </span>
                                </div>
                            </div>

                            <div class="divide-y divide-slate-200">
                                <div
                                    v-for="subject in course.subjects"
                                    :key="subject.id"
                                    class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition-colors"
                                >
                                    <div class="flex items-center gap-3 flex-1">
                                        <div
                                            class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                        >
                                            <img
                                                v-if="subject.photo"
                                                :src="`/storage/${subject.photo}`"
                                                :alt="`Photo for ${subject.title}`"
                                                class="h-full w-full object-cover"
                                            />
                                            <span
                                                v-else
                                                class="text-xs font-semibold text-slate-500"
                                            >
                                                {{ subject.title[0] }}
                                            </span>
                                        </div>
                                        <div>
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <span
                                                    class="text-sm font-medium text-slate-900"
                                                >
                                                    {{ subject.subject_code }}
                                                </span>
                                                <span
                                                    class="text-sm text-slate-600"
                                                >
                                                    {{ subject.title }}
                                                </span>
                                            </div>
                                            <div
                                                v-if="subject.credits"
                                                class="mt-1 text-xs text-slate-500"
                                            >
                                                {{ subject.credits }}
                                                {{
                                                    subject.credits === 1
                                                        ? "credit"
                                                        : "credits"
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Link
                                            :href="
                                                route(
                                                    'teacher.attendance.show',
                                                    subject.id
                                                )
                                            "
                                            class="rounded-md bg-emerald-100 px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                        >
                                            Attendance
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'teacher.grades.show',
                                                    subject.id
                                                )
                                            "
                                            class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-medium text-white hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                        >
                                            Grades
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="rounded-lg bg-slate-50 p-8 text-center">
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
                            You need to be assigned to subjects to view your
                            teaching schedule.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
