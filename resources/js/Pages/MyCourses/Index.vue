<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";

const props = defineProps({
    courses: {
        type: Array,
        required: true,
    },
    message: {
        type: String,
        default: null,
    },
});

const unenroll = (courseId) => {
    if (
        !confirm(
            "Are you sure you want to request withdrawal from this course? This request requires admin approval."
        )
    ) {
        return;
    }

    router.delete(route("courses.unenroll", courseId), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="My Courses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Courses
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- No Student Record Message -->
                <div v-if="message" class="portal-card p-6">
                    <div class="rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200">
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
                                <h3
                                    class="text-sm font-medium text-amber-800"
                                >
                                    Student Record Not Found
                                </h3>
                                <div class="mt-2 text-sm text-amber-700">
                                    <p>{{ message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses List -->
                <div v-else class="portal-card overflow-hidden p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Enrolled Courses
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Courses you are currently enrolled in
                            </p>
                        </div>
                    </div>

                    <!-- Courses Table -->
                    <div v-if="courses.length > 0" class="overflow-x-auto">
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
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Enrolled On
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
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
                                                {{ course.title.charAt(0).toUpperCase() }}
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
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{
                                            new Date(
                                                course.enrolled_at
                                            ).toLocaleDateString()
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <span
                                            v-if="course.enrollment_status === 'withdrawal_pending'"
                                            class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800"
                                        >
                                            Withdrawal Pending
                                        </span>
                                        <button
                                            v-else
                                            @click="unenroll(course.id)"
                                            class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        >
                                            Request Withdrawal
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else
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
                        <h3
                            class="mt-2 text-sm font-medium text-slate-900"
                        >
                            No enrolled courses
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            You haven't enrolled in any courses yet. Visit the
                            Courses page to register.
                        </p>
                        <div class="mt-6">
                            <a
                                :href="route('courses.index')"
                                class="inline-flex items-center rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark"
                            >
                                Browse Courses
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

