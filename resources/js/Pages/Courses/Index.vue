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

const searchTerm = ref("");
const semesterFilter = ref("all");
const availabilityFilter = ref("all");
const sortBy = ref("code");

const isEnrolledStatus = (status) =>
    status === "approved" || status === "withdrawal_pending";

const isOpenToEnroll = (status) => !status || status === "rejected";

const stats = computed(() => {
    const list = props.courses ?? [];

    return {
        total: list.length,
        enrolled: list.filter((c) => isEnrolledStatus(c.enrollment_status)).length,
        pending: list.filter((c) => c.enrollment_status === "pending").length,
        available: list.filter((c) => isOpenToEnroll(c.enrollment_status)).length,
    };
});

const semesters = computed(() => {
    const set = new Set();
    (props.courses ?? []).forEach((course) => {
        if (course.semester) set.add(course.semester);
    });
    return Array.from(set).sort();
});

const hasActiveFilters = computed(
    () =>
        searchTerm.value.trim() !== "" ||
        semesterFilter.value !== "all" ||
        availabilityFilter.value !== "all"
);

const filteredCourses = computed(() => {
    const term = searchTerm.value.trim().toLowerCase();

    let list = (props.courses ?? []).filter((course) => {
        if (
            semesterFilter.value !== "all" &&
            course.semester !== semesterFilter.value
        ) {
            return false;
        }

        if (availabilityFilter.value === "enrolled") {
            return isEnrolledStatus(course.enrollment_status);
        }

        if (availabilityFilter.value === "not-enrolled") {
            return isOpenToEnroll(course.enrollment_status);
        }

        return true;
    });

    if (term) {
        list = list.filter((course) => {
            const haystack = `${course.course_code} ${course.title} ${course.semester}`.toLowerCase();
            return haystack.includes(term);
        });
    }

    const compareText = (a, b) => String(a ?? "").localeCompare(String(b ?? ""));

    if (sortBy.value === "title") {
        list.sort((a, b) => compareText(a.title, b.title));
    } else if (sortBy.value === "credits-desc") {
        list.sort((a, b) => Number(b.credits ?? 0) - Number(a.credits ?? 0));
    } else if (sortBy.value === "credits-asc") {
        list.sort((a, b) => Number(a.credits ?? 0) - Number(b.credits ?? 0));
    } else {
        list.sort((a, b) => compareText(a.course_code, b.course_code));
    }

    return list;
});

const clearFilters = () => {
    searchTerm.value = "";
    semesterFilter.value = "all";
    availabilityFilter.value = "all";
    sortBy.value = "code";
};

const enroll = (courseId) => {
    router.post(route("courses.enroll", courseId), {}, { preserveScroll: false });
};

const statusClass = (status) => {
    if (status === "approved") {
        return "bg-emerald-100 text-emerald-800";
    }

    if (status === "pending") {
        return "bg-amber-100 text-amber-800";
    }

    if (status === "withdrawal_pending") {
        return "bg-orange-100 text-orange-800";
    }

    if (status === "rejected") {
        return "bg-red-100 text-red-800";
    }

    return "bg-slate-100 text-slate-700";
};

const statusLabel = (status) => {
    if (status === "approved") return "Enrolled";
    if (status === "pending") return "Pending";
    if (status === "withdrawal_pending") return "Withdrawal Pending";
    if (status === "rejected") return "Rejected";
    return "Open";
};
</script>

<template>
    <Head title="Courses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Course Catalog
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Courses' }]" />
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="!hasStudentRecord"
                    class="rounded-xl border border-amber-200 bg-amber-50 p-4"
                >
                    <p class="text-sm font-semibold text-amber-900">Student profile required</p>
                    <p class="mt-1 text-sm text-amber-800">
                        Your account is active, but administration must complete your student
                        profile before enrollment is available.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" v-if="courses.length > 0">
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Total courses</p>
                        <p class="mt-2 text-3xl font-bold text-blue-900">{{ stats.total }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Enrolled</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ stats.enrolled }}</p>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Pending</p>
                        <p class="mt-2 text-3xl font-bold text-amber-900">{{ stats.pending }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Open to enroll</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.available }}</p>
                    </div>
                </div>

                <div class="portal-card p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Find your courses</p>
                            <p class="mt-1 text-sm text-slate-600">Search, filter, sort, and enroll.</p>
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
                            <label for="courses-search" class="block text-xs font-medium text-slate-600">Search</label>
                            <input
                                id="courses-search"
                                v-model="searchTerm"
                                type="search"
                                class="mt-1 block w-full rounded-md border-slate-300 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Code, title, semester"
                            />
                        </div>
                        <div>
                            <label for="courses-semester-filter" class="block text-xs font-medium text-slate-600">Semester</label>
                            <select
                                id="courses-semester-filter"
                                v-model="semesterFilter"
                                class="mt-1 block w-full rounded-md border-slate-300 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="all">All semesters</option>
                                <option v-for="semester in semesters" :key="semester" :value="semester">
                                    {{ semester }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label for="courses-availability-filter" class="block text-xs font-medium text-slate-600">Enrollment</label>
                            <select
                                id="courses-availability-filter"
                                v-model="availabilityFilter"
                                class="mt-1 block w-full rounded-md border-slate-300 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="all">All courses</option>
                                <option value="enrolled">Enrolled</option>
                                <option value="not-enrolled">Not enrolled</option>
                            </select>
                        </div>
                        <div>
                            <label for="courses-sort" class="block text-xs font-medium text-slate-600">Sort</label>
                            <select
                                id="courses-sort"
                                v-model="sortBy"
                                class="mt-1 block w-full rounded-md border-slate-300 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="code">Course code</option>
                                <option value="title">Title</option>
                                <option value="credits-desc">Credits (high to low)</option>
                                <option value="credits-asc">Credits (low to high)</option>
                            </select>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ filteredCourses.length }}</span>
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
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Status</th>
                                    <th v-if="hasStudentRecord" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-if="filteredCourses.length === 0">
                                    <td :colspan="hasStudentRecord ? 5 : 4" class="px-4 py-8 text-center text-sm text-slate-500">
                                        No courses match your current search or filters.
                                    </td>
                                </tr>
                                <tr v-for="course in filteredCourses" :key="course.id" class="hover:bg-slate-50">
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
                                                    {{ course.title.charAt(0).toUpperCase() }}
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
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(course.enrollment_status)">
                                            {{ statusLabel(course.enrollment_status) }}
                                        </span>
                                    </td>
                                    <td v-if="hasStudentRecord" class="px-4 py-4 text-right text-sm">
                                        <button
                                            v-if="isOpenToEnroll(course.enrollment_status)"
                                            @click="enroll(course.id)"
                                            class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                                        >
                                            {{ course.enrollment_status === "rejected" ? "Reapply" : "Enroll" }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-3 p-4 lg:hidden">
                        <div v-if="filteredCourses.length === 0" class="rounded-lg border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                            No courses match your current search or filters.
                        </div>

                        <div
                            v-for="course in filteredCourses"
                            :key="`mobile-${course.id}`"
                            class="rounded-xl border border-slate-200 bg-white p-4"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ course.title }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ course.course_code }}</p>
                                </div>
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(course.enrollment_status)">
                                    {{ statusLabel(course.enrollment_status) }}
                                </span>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-600">
                                <p>Credits: <span class="font-semibold text-slate-700">{{ course.credits }}</span></p>
                                <p>Semester: <span class="font-semibold text-slate-700">{{ course.semester }}</span></p>
                            </div>

                            <div v-if="hasStudentRecord" class="mt-3">
                                <button
                                    v-if="isOpenToEnroll(course.enrollment_status)"
                                    @click="enroll(course.id)"
                                    class="w-full rounded-md bg-portal-navy px-3 py-2 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                                >
                                    {{ course.enrollment_status === "rejected" ? "Reapply" : "Enroll" }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
