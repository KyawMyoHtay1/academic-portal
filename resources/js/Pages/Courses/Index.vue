<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { computed, onBeforeUnmount, ref, watch } from "vue";

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

const queryParam = (key) => {
    if (typeof window === "undefined") return null;
    return new URLSearchParams(window.location.search).get(key);
};

const allowedAvailability = new Set(["all", "enrolled", "not-enrolled"]);
const allowedSorts = new Set(["code", "title", "credits-desc", "credits-asc"]);

const searchInput = ref(queryParam("search") ?? "");
const searchTerm = ref(searchInput.value);
const semesterFilter = ref(queryParam("semester") || "all");
const availabilityFilter = ref(
    allowedAvailability.has(queryParam("availability") || "")
        ? queryParam("availability")
        : "all"
);
const sortBy = ref(
    allowedSorts.has(queryParam("sort") || "") ? queryParam("sort") : "code"
);
const selectedCourseId = ref(null);

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

const activeFilterChips = computed(() => {
    const chips = [];

    if (searchTerm.value.trim() !== "") {
        chips.push({
            key: "search",
            label: `Search: ${searchTerm.value.trim()}`,
        });
    }

    if (semesterFilter.value !== "all") {
        chips.push({
            key: "semester",
            label: `Semester: ${semesterFilter.value}`,
        });
    }

    if (availabilityFilter.value !== "all") {
        chips.push({
            key: "availability",
            label:
                availabilityFilter.value === "enrolled"
                    ? "Enrollment: Enrolled"
                    : "Enrollment: Not enrolled",
        });
    }

    return chips;
});

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

const selectedCourse = computed(() => {
    if (selectedCourseId.value === null) {
        return null;
    }

    return (
        (props.courses ?? []).find(
            (course) =>
                String(course.id) === String(selectedCourseId.value)
        ) || null
    );
});

const clearFilters = () => {
    searchInput.value = "";
    searchTerm.value = "";
    semesterFilter.value = "all";
    availabilityFilter.value = "all";
    sortBy.value = "code";
};

const removeFilterChip = (key) => {
    if (key === "search") {
        searchInput.value = "";
        searchTerm.value = "";
        return;
    }

    if (key === "semester") {
        semesterFilter.value = "all";
        return;
    }

    if (key === "availability") {
        availabilityFilter.value = "all";
    }
};

const applySearch = debounce(() => {
    searchTerm.value = searchInput.value;
}, 300);

const persistFiltersToUrl = debounce(() => {
    if (typeof window === "undefined") return;

    const url = new URL(window.location.href);
    const params = url.searchParams;

    if (searchTerm.value.trim() !== "") {
        params.set("search", searchTerm.value.trim());
    } else {
        params.delete("search");
    }

    if (semesterFilter.value !== "all") {
        params.set("semester", semesterFilter.value);
    } else {
        params.delete("semester");
    }

    if (availabilityFilter.value !== "all") {
        params.set("availability", availabilityFilter.value);
    } else {
        params.delete("availability");
    }

    if (sortBy.value !== "code") {
        params.set("sort", sortBy.value);
    } else {
        params.delete("sort");
    }

    const query = params.toString();
    window.history.replaceState(
        {},
        "",
        query !== "" ? `${url.pathname}?${query}` : url.pathname
    );
}, 300);

watch(searchInput, () => {
    applySearch();
});

watch([searchTerm, semesterFilter, availabilityFilter, sortBy], () => {
    persistFiltersToUrl();
});

onBeforeUnmount(() => {
    applySearch.cancel();
    persistFiltersToUrl.cancel();
});

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

const openCourseQuickView = (courseId) => {
    selectedCourseId.value = courseId;
};

const closeCourseQuickView = () => {
    selectedCourseId.value = null;
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
                            Clear all filters
                        </button>
                    </div>

                    <div class="mt-4 grid gap-3 lg:grid-cols-4">
                        <div>
                            <label for="courses-search" class="block text-xs font-medium text-slate-600">Search</label>
                            <input
                                id="courses-search"
                                v-model="searchInput"
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

                    <div
                        v-if="activeFilterChips.length > 0"
                        class="mt-3 flex flex-wrap gap-2"
                    >
                        <span
                            v-for="chip in activeFilterChips"
                            :key="chip.key"
                            class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700"
                        >
                            {{ chip.label }}
                            <button
                                type="button"
                                class="rounded-full bg-white px-1.5 text-slate-500 hover:text-slate-700"
                                @click="removeFilterChip(chip.key)"
                            >
                                x
                            </button>
                        </span>
                    </div>
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
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-if="filteredCourses.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
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
                                    <td class="px-4 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                @click="openCourseQuickView(course.id)"
                                                class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                            >
                                                Quick view
                                            </button>
                                            <button
                                                v-if="hasStudentRecord && isOpenToEnroll(course.enrollment_status)"
                                                @click="enroll(course.id)"
                                                class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                                            >
                                                {{ course.enrollment_status === "rejected" ? "Reapply" : "Enroll" }}
                                            </button>
                                        </div>
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

                            <div class="mt-3 flex gap-2">
                                <button
                                    type="button"
                                    @click="openCourseQuickView(course.id)"
                                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                >
                                    Quick view
                                </button>
                                <button
                                    v-if="hasStudentRecord && isOpenToEnroll(course.enrollment_status)"
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

        <div
            v-if="selectedCourse"
            class="fixed inset-0 z-50 flex"
            aria-modal="true"
            role="dialog"
        >
            <button
                type="button"
                class="h-full flex-1 bg-slate-900/40"
                aria-label="Close quick view"
                @click="closeCourseQuickView"
            ></button>

            <aside
                class="h-full w-full max-w-md overflow-y-auto bg-white p-6 shadow-xl"
            >
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Course quick view
                        </p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">
                            {{ selectedCourse.title }}
                        </h3>
                        <p class="text-sm text-slate-500">
                            {{ selectedCourse.course_code }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-md border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        @click="closeCourseQuickView"
                    >
                        Close
                    </button>
                </div>

                <div class="space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-white"
                        >
                            <img
                                v-if="selectedCourse.photo"
                                :src="`/storage/${selectedCourse.photo}`"
                                :alt="`Photo for ${selectedCourse.title}`"
                                class="h-full w-full object-cover"
                            />
                            <span
                                v-else
                                class="text-sm font-semibold text-slate-500"
                            >
                                {{ selectedCourse.title?.[0] }}
                            </span>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                            :class="statusClass(selectedCourse.enrollment_status)"
                        >
                            {{ statusLabel(selectedCourse.enrollment_status) }}
                        </span>
                    </div>
                    <p>
                        <span class="font-semibold text-slate-700">Semester:</span>
                        {{ selectedCourse.semester || "N/A" }}
                    </p>
                    <p>
                        <span class="font-semibold text-slate-700">Credits:</span>
                        {{ selectedCourse.credits ?? "N/A" }}
                    </p>
                </div>

                <div class="mt-4 flex items-center gap-2">
                    <button
                        v-if="hasStudentRecord && isOpenToEnroll(selectedCourse.enrollment_status)"
                        type="button"
                        class="rounded-md bg-portal-navy px-3 py-2 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                        @click="enroll(selectedCourse.id)"
                    >
                        {{ selectedCourse.enrollment_status === "rejected" ? "Reapply to course" : "Enroll to course" }}
                    </button>
                    <button
                        type="button"
                        class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        @click="closeCourseQuickView"
                    >
                        Done
                    </button>
                </div>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
