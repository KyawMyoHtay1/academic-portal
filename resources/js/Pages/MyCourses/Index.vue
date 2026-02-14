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
    message: {
        type: String,
        default: null,
    },
});

const searchTerm = ref("");
const semesterFilter = ref("all");

const semesters = computed(() => {
    const set = new Set();

    (props.courses ?? []).forEach((course) => {
        if (course.semester) set.add(course.semester);
    });

    return Array.from(set).sort();
});

const stats = computed(() => {
    const list = props.courses ?? [];
    const enrolled = list.filter((c) => c.enrollment_status === "approved").length;
    const withdrawalPending = list.filter((c) => c.enrollment_status === "withdrawal_pending").length;
    const totalCredits = list.reduce((sum, c) => sum + (Number(c.credits) || 0), 0);

    return {
        total: list.length,
        enrolled,
        withdrawalPending,
        totalCredits,
    };
});

const filteredCourses = computed(() => {
    const term = searchTerm.value.trim().toLowerCase();

    return (props.courses ?? []).filter((course) => {
        if (semesterFilter.value !== "all" && course.semester !== semesterFilter.value) {
            return false;
        }

        if (!term) return true;

        const haystack = `${course.course_code ?? ""} ${course.title ?? ""} ${course.semester ?? ""}`.toLowerCase();
        return haystack.includes(term);
    });
});

const hasActiveFilters = computed(() => searchTerm.value.trim() !== "" || semesterFilter.value !== "all");

const clearFilters = () => {
    searchTerm.value = "";
    semesterFilter.value = "all";
};

const unenroll = (courseId) => {
    if (!confirm("Are you sure you want to request withdrawal from this course? This request requires admin approval.")) {
        return;
    }

    router.delete(route("courses.unenroll", courseId), {
        preserveScroll: true,
    });
};

const formatDate = (value) => {
    if (!value) return "-";

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "-";

    return date.toLocaleDateString();
};
</script>

<template>
    <Head title="My Courses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">My Courses</h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'My Courses' }]" />
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div v-if="message" class="portal-card p-6">
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <h3 class="text-sm font-semibold text-amber-900">Student Record Not Found</h3>
                        <p class="mt-1 text-sm text-amber-800">{{ message }}</p>
                    </div>
                </div>

                <template v-else>
                    <div class="rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-indigo-700">Enrollment overview</p>
                        <h3 class="mt-2 text-2xl font-semibold text-indigo-950">Track your registered courses by semester</h3>
                        <p class="mt-2 text-sm text-indigo-900/80">Review active enrollments, pending withdrawals, credits, and take action when needed.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Total courses</p>
                            <p class="mt-2 text-2xl font-bold text-blue-900">{{ stats.total }}</p>
                        </div>
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Active</p>
                            <p class="mt-2 text-2xl font-bold text-emerald-900">{{ stats.enrolled }}</p>
                        </div>
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Withdrawal pending</p>
                            <p class="mt-2 text-2xl font-bold text-amber-900">{{ stats.withdrawalPending }}</p>
                        </div>
                        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Total credits</p>
                            <p class="mt-2 text-2xl font-bold text-indigo-900">{{ stats.totalCredits }}</p>
                        </div>
                    </div>

                    <div class="portal-card p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Filter courses</p>
                                <p class="mt-1 text-sm text-slate-600">Search by code/title and limit by semester.</p>
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

                        <div class="mt-4 grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                            <div class="lg:col-span-2">
                                <input
                                    v-model="searchTerm"
                                    type="search"
                                    placeholder="Search by course code, title, semester"
                                    class="block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                            </div>
                            <div>
                                <select
                                    v-model="semesterFilter"
                                    class="block w-full rounded-md border-slate-300 py-2 text-sm focus:border-portal-navy focus:ring-portal-navy"
                                >
                                    <option value="all">All semesters</option>
                                    <option v-for="semester in semesters" :key="semester" :value="semester">
                                        {{ semester }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-slate-500">
                            Showing <span class="font-semibold text-slate-700">{{ filteredCourses.length }}</span>
                            of <span class="font-semibold text-slate-700">{{ courses.length }}</span> courses
                        </p>
                    </div>

                    <div v-if="courses.length === 0" class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
                        <h3 class="text-sm font-semibold text-slate-900">No enrolled courses</h3>
                        <p class="mt-1 text-sm text-slate-500">You have not enrolled in any courses yet.</p>
                        <div class="mt-4">
                            <Link
                                :href="route('courses.index')"
                                class="inline-flex items-center rounded-md bg-portal-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-portal-navy-dark"
                            >
                                Browse Courses
                            </Link>
                        </div>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-if="filteredCourses.length === 0" class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
                            <h3 class="text-sm font-semibold text-slate-900">No courses match your filters</h3>
                            <p class="mt-1 text-sm text-slate-500">Try clearing your search or selecting another semester.</p>
                        </div>

                        <div class="grid gap-4 lg:hidden">
                            <div
                                v-for="course in filteredCourses"
                                :key="course.id"
                                class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ course.title }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ course.course_code }} | {{ course.semester }}</p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700">{{ course.credits }} cr</span>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-1">
                                    <span
                                        v-for="s in (course.subjects ?? [])"
                                        :key="s.id"
                                        class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                    >
                                        {{ s.subject_code }}
                                    </span>
                                    <span v-if="(course.subjects ?? []).length === 0" class="text-xs text-slate-500">No subjects</span>
                                </div>

                                <div class="mt-3 flex items-center justify-between">
                                    <p class="text-xs text-slate-500">Enrolled: {{ formatDate(course.enrolled_at) }}</p>
                                    <span
                                        v-if="course.enrollment_status === 'withdrawal_pending'"
                                        class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800"
                                    >
                                        Withdrawal Pending
                                    </span>
                                    <button
                                        v-else
                                        type="button"
                                        @click="unenroll(course.id)"
                                        class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200"
                                    >
                                        Request Withdrawal
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="hidden overflow-x-auto rounded-xl border border-slate-200 bg-white lg:block">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Course</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Subjects</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Credits</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Semester</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Enrolled On</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    <tr v-for="course in filteredCourses" :key="course.id" class="hover:bg-slate-50">
                                        <td class="px-4 py-4">
                                            <p class="text-sm font-semibold text-slate-900">{{ course.title }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ course.course_code }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-slate-700">
                                            <div v-if="(course.subjects ?? []).length" class="flex flex-wrap gap-1">
                                                <span
                                                    v-for="s in course.subjects"
                                                    :key="s.id"
                                                    class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                                >
                                                    {{ s.subject_code }}
                                                </span>
                                            </div>
                                            <span v-else class="text-xs text-slate-500">No subjects</span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700">{{ course.credits }}</td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700">{{ course.semester }}</td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700">{{ formatDate(course.enrolled_at) }}</td>
                                        <td class="whitespace-nowrap px-4 py-4 text-right text-sm">
                                            <span
                                                v-if="course.enrollment_status === 'withdrawal_pending'"
                                                class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800"
                                            >
                                                Withdrawal Pending
                                            </span>
                                            <button
                                                v-else
                                                type="button"
                                                @click="unenroll(course.id)"
                                                class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200"
                                            >
                                                Request Withdrawal
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
