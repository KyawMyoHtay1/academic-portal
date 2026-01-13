<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineProps({
    courses: {
        type: Array,
        required: true,
    },
});

const deleteCourse = (courseId) => {
    if (
        !confirm(
            "Are you sure you want to delete this course? This action cannot be undone."
        )
    ) {
        return;
    }

    router.delete(route("admin.courses.destroy", courseId), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Course Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Course Management
                </h2>
                <Link
                    :href="route('admin.courses.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                >
                    Create Course
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Course Management' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            All Courses
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Manage course offerings for the university
                        </p>
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
                                        scope="col"
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-if="courses.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No courses found. Create your first
                                        course to get started.
                                    </td>
                                </tr>
                                <tr
                                    v-for="course in courses"
                                    :key="course.id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                >
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <div class="flex items-center">
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
                                                    {{ course.title[0] }}
                                                </span>
                                            </div>
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
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.courses.edit',
                                                        course.id
                                                    )
                                                "
                                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteCourse(course.id)"
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
