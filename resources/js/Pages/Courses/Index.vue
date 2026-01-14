<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

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

const stats = computed(() => {
    const list = props.courses ?? [];
    const total = list.length;
    const available = list.filter(
        (c) => c.enrollment_status !== "approved"
    ).length;
    const enrolled = list.filter(
        (c) => c.enrollment_status === "approved"
    ).length;

    return {
        total,
        available,
        enrolled,
    };
});

const searchTerm = ref("");
const semesterFilter = ref("all");
const availabilityFilter = ref("all");

const semesters = computed(() => {
    const set = new Set();

    props.courses.forEach((course) => {
        if (course.semester) {
            set.add(course.semester);
        }
    });

    return Array.from(set).sort();
});

const filteredCourses = computed(() => {
    const term = searchTerm.value.trim().toLowerCase();

    return props.courses.filter((course) => {
        if (
            semesterFilter.value !== "all" &&
            course.semester !== semesterFilter.value
        ) {
            return false;
        }

        if (availabilityFilter.value === "enrolled") {
            if (course.enrollment_status !== "approved") {
                return false;
            }
        } else if (availabilityFilter.value === "not-enrolled") {
            if (course.enrollment_status === "approved") {
                return false;
            }
        }

        if (!term) {
            return true;
        }

        const haystack = (
            course.course_code +
            " " +
            course.title +
            " " +
            course.semester
        ).toLowerCase();

        return haystack.includes(term);
    });
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
                    <!-- Summary stats -->
                    <div
                        v-if="courses.length > 0"
                        class="mb-6 grid gap-4 md:grid-cols-3"
                    >
                        <div class="portal-card p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Courses
                            </p>
                            <p
                                class="mt-2 text-2xl font-bold text-slate-900"
                            >
                                {{ stats.total }}
                            </p>
                        </div>
                        <div class="portal-card p-5 bg-emerald-50">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                            >
                                You're enrolled
                            </p>
                            <p
                                class="mt-2 text-2xl font-bold text-emerald-900"
                            >
                                {{ stats.enrolled }}
                            </p>
                        </div>
                        <div class="portal-card p-5 bg-slate-50">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-700"
                            >
                                Still available
                            </p>
                            <p
                                class="mt-2 text-2xl font-bold text-slate-900"
                            >
                                {{ stats.available }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mb-4 flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
                    >
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
                        <div
                            v-if="courses.length > 0"
                            class="flex w-full flex-col gap-2 md:w-auto md:flex-row md:items-end md:justify-end"
                        >
                            <div class="md:w-56">
                                <label
                                    for="courses-search"
                                    class="block text-xs font-medium text-slate-600"
                                >
                                    Search
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                                    >
                                        <svg
                                            class="h-4 w-4 text-slate-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"
                                            />
                                        </svg>
                                    </div>
                                    <input
                                        id="courses-search"
                                        v-model="searchTerm"
                                        type="search"
                                        class="block w-full rounded-md border-slate-300 pl-9 pr-3 py-2 text-sm placeholder-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Search by course code, title or semester"
                                    />
                                </div>
                            </div>
                            <div
                                class="flex flex-col gap-2 md:flex-row md:items-end"
                            >
                                <div class="md:w-40">
                                    <label
                                        for="courses-semester-filter"
                                        class="block text-xs font-medium text-slate-600"
                                    >
                                        Semester
                                    </label>
                                    <select
                                        id="courses-semester-filter"
                                        v-model="semesterFilter"
                                        class="mt-1 block w-full rounded-md border-slate-300 py-2 pl-3 pr-8 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500"
                                    >
                                        <option value="all">
                                            All semesters
                                        </option>
                                        <option
                                            v-for="semester in semesters"
                                            :key="semester"
                                            :value="semester"
                                        >
                                            {{ semester }}
                                        </option>
                                    </select>
                                </div>
                                <div class="md:w-40">
                                    <label
                                        for="courses-availability-filter"
                                        class="block text-xs font-medium text-slate-600"
                                    >
                                        Enrollment
                                    </label>
                                    <select
                                        id="courses-availability-filter"
                                        v-model="availabilityFilter"
                                        class="mt-1 block w-full rounded-md border-slate-300 py-2 pl-3 pr-8 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500"
                                    >
                                        <option value="all">All courses</option>
                                        <option value="enrolled">
                                            Enrolled only
                                        </option>
                                        <option value="not-enrolled">
                                            Not enrolled yet
                                        </option>
                                    </select>
                                </div>
                            </div>
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
                                    v-else-if="filteredCourses.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        :colspan="hasStudentRecord ? 6 : 5"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No courses match your current search or
                                        filters.
                                    </td>
                                </tr>
                                <tr
                                    v-for="course in filteredCourses"
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
