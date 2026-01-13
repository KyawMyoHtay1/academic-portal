<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    courses: {
        type: Array,
        required: true,
    },
    hasStudentRecord: {
        type: Boolean,
        default: false,
    },
});

const enroll = (courseId) => {
    router.post(
        route("courses.enroll", courseId),
        {},
        {
            preserveScroll: false, // Allow page refresh to show flash messages
            onSuccess: () => {
                // Page will refresh automatically with updated enrollment status and flash messages
            },
        }
    );
};
</script>

<template>
    <Head title="Courses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Available Courses
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Courses' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Alert if no student record -->
                <div
                    v-if="!hasStudentRecord"
                    class="mb-6 rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200"
                >
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-amber-400"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">
                                Student Profile Required
                            </h3>
                            <p class="mt-1 text-sm text-amber-700">
                                Your account is registered, but a student
                                profile needs to be created by administration
                                before you can enroll in courses. Please contact
                                the administration office to complete your
                                student profile setup.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Course Catalog
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Browse available courses and enroll in courses
                            </p>
                        </div>
                    </div>

                    <!-- Courses Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Photo
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Course Code
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Title
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Credits
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Semester
                                    </th>
                                    <th
                                        v-if="hasStudentRecord"
                                        scope="col"
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-if="courses.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        :colspan="hasStudentRecord ? 6 : 5"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No courses available yet.
                                    </td>
                                </tr>
                                <tr
                                    v-for="course in courses"
                                    :key="course.id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                >
                                    <td class="whitespace-nowrap px-4 py-4">
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
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900"
                                    >
                                        {{ course.course_code }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-700"
                                    >
                                        {{ course.title }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ course.credits }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ course.semester }}
                                    </td>
                                    <td
                                        v-if="hasStudentRecord"
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <span
                                            v-if="
                                                course.enrollment_status ===
                                                'approved'
                                            "
                                            class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-800"
                                        >
                                            Enrolled
                                        </span>
                                        <span
                                            v-else-if="
                                                course.enrollment_status ===
                                                'pending'
                                            "
                                            class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800"
                                        >
                                            Pending Approval
                                        </span>
                                        <span
                                            v-else-if="
                                                course.enrollment_status ===
                                                'rejected'
                                            "
                                            class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800"
                                        >
                                            Rejected
                                        </span>
                                        <button
                                            v-else
                                            @click="enroll(course.id)"
                                            class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                        >
                                            Register
                                        </button>
                                        <button
                                            v-if="
                                                course.enrollment_status ===
                                                'rejected'
                                            "
                                            @click="enroll(course.id)"
                                            class="ml-2 rounded-md bg-portal-navy px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                        >
                                            Reapply
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
