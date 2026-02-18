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
const enrollmentFilter = ref("all");
const sortBy = ref("code");

const semesters = computed(() => {
    const set = new Set((props.courses ?? []).map((c) => c.semester).filter(Boolean));
    return Array.from(set).sort();
});

const stats = computed(() => {
    const list = props.courses ?? [];
    const creditsTotal = list.reduce(
        (sum, c) => sum + (parseInt(c.credits, 10) || 0),
        0
    );

    return {
        total: list.length,
        semesters: new Set(list.map((c) => c.semester).filter(Boolean)).size,
        creditsTotal,
    };
});

const hasActiveFilters = computed(
    () =>
        query.value.trim() !== "" ||
        semesterFilter.value !== "all" ||
        enrollmentFilter.value !== "all" ||
        sortBy.value !== "code"
);

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = [...(props.courses ?? [])];

    if (semesterFilter.value !== "all") {
        list = list.filter((c) => c.semester === semesterFilter.value);
    }

    if (enrollmentFilter.value === "enrolled") {
        list = list.filter((c) => Number(c.enrolled_students_count ?? 0) > 0);
    } else if (enrollmentFilter.value === "not-enrolled") {
        list = list.filter((c) => Number(c.enrolled_students_count ?? 0) === 0);
    }

    if (q) {
        list = list.filter((c) => {
            const code = (c.course_code ?? "").toLowerCase();
            const title = (c.title ?? "").toLowerCase();
            const semester = (c.semester ?? "").toLowerCase();
            return code.includes(q) || title.includes(q) || semester.includes(q);
        });
    }

    const cmp = (a, b) => String(a ?? "").localeCompare(String(b ?? ""));
    if (sortBy.value === "title") {
        list.sort((a, b) => cmp(a.title, b.title));
    } else if (sortBy.value === "semester") {
        list.sort((a, b) => cmp(a.semester, b.semester));
    } else if (sortBy.value === "credits-desc") {
        list.sort((a, b) => Number(b.credits ?? 0) - Number(a.credits ?? 0));
    } else if (sortBy.value === "credits-asc") {
        list.sort((a, b) => Number(a.credits ?? 0) - Number(b.credits ?? 0));
    } else {
        list.sort((a, b) => cmp(a.course_code, b.course_code));
    }

    return list;
});

const clearFilters = () => {
    query.value = "";
    semesterFilter.value = "all";
    enrollmentFilter.value = "all";
    sortBy.value = "code";
};

const deleteCourse = (courseId) => {
    if (!confirm("Are you sure you want to delete this course? This action cannot be undone.")) {
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
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-portal-navy-dark"
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

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Courses</p>
                        <p class="mt-2 text-3xl font-bold text-blue-900">{{ stats.total }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Semesters</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ stats.semesters }}</p>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Total credits</p>
                        <p class="mt-2 text-3xl font-bold text-amber-900">{{ stats.creditsTotal }}</p>
                    </div>
                </div>

                <div class="portal-card p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Manage catalog</p>
                            <p class="mt-1 text-sm text-slate-600">Search, filter, sort, edit, and delete course entries.</p>
                        </div>
                        <button
                            v-if="hasActiveFilters"
                            type="button"
                            @click="clearFilters"
                            class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Reset filters
                        </button>
                    </div>

                    <div class="mt-4 grid gap-3 lg:grid-cols-4">
                        <div>
                            <label for="admin-courses-search" class="block text-xs font-medium text-slate-600">Search</label>
                            <input
                                id="admin-courses-search"
                                v-model="query"
                                type="search"
                                placeholder="Course code, title, semester"
                                class="mt-1 block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>
                        <div>
                            <label for="admin-courses-semester" class="block text-xs font-medium text-slate-600">Semester</label>
                            <select
                                id="admin-courses-semester"
                                v-model="semesterFilter"
                                class="mt-1 block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All semesters</option>
                                <option v-for="s in semesters" :key="s" :value="s">{{ s }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="admin-courses-enrollment" class="block text-xs font-medium text-slate-600">Enrollment status</label>
                            <select
                                id="admin-courses-enrollment"
                                v-model="enrollmentFilter"
                                class="mt-1 block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All courses</option>
                                <option value="enrolled">Enrolled</option>
                                <option value="not-enrolled">Not enrolled</option>
                            </select>
                        </div>
                        <div>
                            <label for="admin-courses-sort" class="block text-xs font-medium text-slate-600">Sort</label>
                            <select
                                id="admin-courses-sort"
                                v-model="sortBy"
                                class="mt-1 block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="code">Course code</option>
                                <option value="title">Title</option>
                                <option value="semester">Semester</option>
                                <option value="credits-desc">Credits (high to low)</option>
                                <option value="credits-asc">Credits (low to high)</option>
                            </select>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ filtered.length }}</span>
                        of <span class="font-semibold text-slate-700">{{ courses.length }}</span> courses
                    </p>
                </div>

                <div class="portal-card overflow-hidden p-0">
                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Course</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Credits</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Semester</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Enrollment</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-if="filtered.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                        {{
                                            courses.length === 0
                                                ? "No courses found. Create your first course to get started."
                                                : "No courses match your filters."
                                        }}
                                    </td>
                                </tr>

                                <tr v-for="course in filtered" :key="course.id" class="hover:bg-slate-50">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 overflow-hidden rounded-md border border-slate-200 bg-slate-100">
                                                <img
                                                    v-if="course.photo"
                                                    :src="`/storage/${course.photo}`"
                                                    :alt="`Photo for ${course.title}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <div v-else class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-500">
                                                    {{ course.title?.charAt(0)?.toUpperCase() }}
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">{{ course.title }}</p>
                                                <p class="text-xs text-slate-500">{{ course.course_code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">{{ course.credits }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-700">{{ course.semester }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        <span
                                            v-if="Number(course.enrolled_students_count ?? 0) > 0"
                                            class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800"
                                        >
                                            Enrolled ({{ course.enrolled_students_count }})
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700"
                                        >
                                            Not enrolled
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('admin.courses.edit', course.id)"
                                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteCourse(course.id)"
                                                class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-3 p-4 lg:hidden">
                        <div
                            v-if="filtered.length === 0"
                            class="rounded-lg border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500"
                        >
                            {{
                                courses.length === 0
                                    ? "No courses found. Create your first course to get started."
                                    : "No courses match your filters."
                            }}
                        </div>

                        <div
                            v-for="course in filtered"
                            :key="`admin-mobile-${course.id}`"
                            class="rounded-xl border border-slate-200 bg-white p-4"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ course.title }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ course.course_code }}</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ course.semester }}
                                </span>
                            </div>

                            <p class="mt-3 text-xs text-slate-600">
                                Credits: <span class="font-semibold text-slate-700">{{ course.credits }}</span>
                            </p>
                            <p class="mt-1 text-xs text-slate-600">
                                Enrollment:
                                <span
                                    v-if="Number(course.enrolled_students_count ?? 0) > 0"
                                    class="font-semibold text-emerald-700"
                                >
                                    Enrolled ({{ course.enrolled_students_count }})
                                </span>
                                <span v-else class="font-semibold text-slate-700">Not enrolled</span>
                            </p>

                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <Link
                                    :href="route('admin.courses.edit', course.id)"
                                    class="rounded-md bg-slate-100 px-3 py-2 text-center text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="deleteCourse(course.id)"
                                    class="rounded-md bg-red-100 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-200"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
