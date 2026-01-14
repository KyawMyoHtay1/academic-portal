<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    courses: {
        type: Array,
        required: true,
    },
});

const query = ref("");
const semesterFilter = ref("all");

const semesters = computed(() => {
    const set = new Set((props.courses ?? []).map((c) => c.semester).filter(Boolean));
    return Array.from(set).sort();
});

const stats = computed(() => {
    const list = props.courses ?? [];
    const creditsTotal = list.reduce((sum, c) => sum + (parseInt(c.credits, 10) || 0), 0);
    return {
        total: list.length,
        semesters: new Set(list.map((c) => c.semester).filter(Boolean)).size,
        creditsTotal,
    };
});

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.courses ?? [];

    if (semesterFilter.value !== "all") {
        list = list.filter((c) => c.semester === semesterFilter.value);
    }

    if (q) {
        list = list.filter((c) => {
            const code = (c.course_code ?? "").toLowerCase();
            const title = (c.title ?? "").toLowerCase();
            const semester = (c.semester ?? "").toLowerCase();
            return code.includes(q) || title.includes(q) || semester.includes(q);
        });
    }

    return list;
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
                <!-- Summary stats -->
                <div class="mb-6 grid gap-4 md:grid-cols-3">
                    <div class="portal-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Courses
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ stats.total }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                            Semesters
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ stats.semesters }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-amber-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                            Total credits (listed)
                        </p>
                        <p class="mt-2 text-2xl font-bold text-amber-900">
                            {{ stats.creditsTotal }}
                        </p>
                    </div>
                </div>

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

                    <!-- Filters -->
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <select
                                v-model="semesterFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-48"
                            >
                                <option value="all">All semesters</option>
                                <option v-for="s in semesters" :key="s" :value="s">
                                    {{ s }}
                                </option>
                            </select>

                            <div class="relative w-full sm:w-80">
                                <input
                                    v-model="query"
                                    type="text"
                                    placeholder="Search course code, title…"
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
                        </div>

                        <div class="text-xs text-slate-500">
                            Showing <span class="font-semibold text-slate-700">{{ filtered.length }}</span>
                            of <span class="font-semibold text-slate-700">{{ courses.length }}</span>
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
                                        scope="col"
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-if="filtered.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{
                                            courses.length === 0
                                                ? "No courses found. Create your first course to get started."
                                                : "No courses match your filters."
                                        }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="course in filtered"
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
