<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { computed, onBeforeUnmount, ref, watch } from "vue";

const props = defineProps({
    courses: {
        type: Array,
        required: true,
    },
});

const queryParam = (key) => {
    if (typeof window === "undefined") return null;
    return new URLSearchParams(window.location.search).get(key);
};

const allowedEnrollmentFilters = new Set(["all", "enrolled", "not-enrolled"]);
const allowedSorts = new Set(["code", "title", "semester", "credits-desc", "credits-asc"]);

const searchInput = ref(queryParam("search") ?? "");
const query = ref(searchInput.value);
const semesterFilter = ref(queryParam("semester") || "all");
const enrollmentFilter = ref(
    allowedEnrollmentFilters.has(queryParam("enrollment") || "")
        ? queryParam("enrollment")
        : "all"
);
const sortBy = ref(
    allowedSorts.has(queryParam("sort") || "") ? queryParam("sort") : "code"
);
const selectedCourseId = ref(null);

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

const activeFilterChips = computed(() => {
    const chips = [];

    if (query.value.trim() !== "") {
        chips.push({ key: "search", label: `Search: ${query.value.trim()}` });
    }
    if (semesterFilter.value !== "all") {
        chips.push({ key: "semester", label: `Semester: ${semesterFilter.value}` });
    }
    if (enrollmentFilter.value !== "all") {
        chips.push({
            key: "enrollment",
            label:
                enrollmentFilter.value === "enrolled"
                    ? "Enrollment: Enrolled"
                    : "Enrollment: Not enrolled",
        });
    }

    return chips;
});

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

const selectedCourse = computed(() =>
    (props.courses ?? []).find((course) => String(course.id) === String(selectedCourseId.value)) || null
);

const formatUpdatedAt = (value) => {
    if (!value) return "-";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return String(value);

    return new Intl.DateTimeFormat("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    }).format(date);
};

const formatThreshold = (value) =>
    value === null || value === undefined ? "Global default" : `${value}%`;

const clearFilters = () => {
    searchInput.value = "";
    query.value = "";
    semesterFilter.value = "all";
    enrollmentFilter.value = "all";
    sortBy.value = "code";
};

const removeFilterChip = (key) => {
    if (key === "search") {
        searchInput.value = "";
        query.value = "";
        return;
    }

    if (key === "semester") {
        semesterFilter.value = "all";
        return;
    }

    if (key === "enrollment") {
        enrollmentFilter.value = "all";
    }
};

const applySearch = debounce(() => {
    query.value = searchInput.value.trim();
}, 250);

const persistFiltersToUrl = debounce(() => {
    if (typeof window === "undefined") return;

    const url = new URL(window.location.href);
    const params = url.searchParams;

    if (query.value.trim() !== "") {
        params.set("search", query.value.trim());
    } else {
        params.delete("search");
    }

    if (semesterFilter.value !== "all") {
        params.set("semester", semesterFilter.value);
    } else {
        params.delete("semester");
    }

    if (enrollmentFilter.value !== "all") {
        params.set("enrollment", enrollmentFilter.value);
    } else {
        params.delete("enrollment");
    }

    if (sortBy.value !== "code") {
        params.set("sort", sortBy.value);
    } else {
        params.delete("sort");
    }

    const queryString = params.toString();
    window.history.replaceState(
        {},
        "",
        queryString ? `${url.pathname}?${queryString}` : url.pathname
    );
}, 200);

watch(searchInput, () => {
    applySearch();
});

watch([query, semesterFilter, enrollmentFilter, sortBy], () => {
    persistFiltersToUrl();
});

onBeforeUnmount(() => {
    applySearch.cancel();
    persistFiltersToUrl.cancel();
});

const openQuickView = (courseId) => {
    selectedCourseId.value = courseId;
};

const closeQuickView = () => {
    selectedCourseId.value = null;
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
                            Clear all filters
                        </button>
                    </div>

                    <div class="mt-4 grid gap-3 lg:grid-cols-4">
                        <div>
                            <label for="admin-courses-search" class="block text-xs font-medium text-slate-600">Search</label>
                            <input
                                id="admin-courses-search"
                                v-model="searchInput"
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

                    <div
                        v-if="activeFilterChips.length > 0"
                        class="mt-3 flex flex-wrap gap-2"
                    >
                        <span
                            v-for="chip in activeFilterChips"
                            :key="chip.key"
                            class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"
                        >
                            {{ chip.label }}
                            <button
                                type="button"
                                class="rounded px-1 text-slate-500 hover:bg-slate-200"
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
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Enrollment</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Details</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-if="filtered.length === 0">
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
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
                                    <td class="px-4 py-4 text-xs text-slate-600">
                                        <div class="space-y-1">
                                            <p>
                                                Subjects:
                                                <span class="font-semibold text-slate-700">{{ Number(course.subjects_count ?? 0) }}</span>
                                            </p>
                                            <p>
                                                Teachers:
                                                <span class="font-semibold text-slate-700">{{ Number(course.teachers_count ?? 0) }}</span>
                                            </p>
                                            <p>
                                                Attendance threshold:
                                                <span class="font-semibold text-slate-700">{{ formatThreshold(course.attendance_threshold) }}</span>
                                            </p>
                                            <p>
                                                Updated:
                                                <span class="font-semibold text-slate-700">{{ formatUpdatedAt(course.updated_at) }}</span>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                class="rounded-md bg-indigo-100 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-200"
                                                @click="openQuickView(course.id)"
                                            >
                                                Quick view
                                            </button>
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
                            <p class="mt-1 text-xs text-slate-600">
                                Subjects: <span class="font-semibold text-slate-700">{{ Number(course.subjects_count ?? 0) }}</span>
                                • Teachers: <span class="font-semibold text-slate-700">{{ Number(course.teachers_count ?? 0) }}</span>
                            </p>
                            <p class="mt-1 text-xs text-slate-600">
                                Threshold:
                                <span class="font-semibold text-slate-700">{{ formatThreshold(course.attendance_threshold) }}</span>
                                • Updated:
                                <span class="font-semibold text-slate-700">{{ formatUpdatedAt(course.updated_at) }}</span>
                            </p>

                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    class="rounded-md bg-indigo-100 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-200"
                                    @click="openQuickView(course.id)"
                                >
                                    Quick view
                                </button>
                                <Link
                                    :href="route('admin.courses.edit', course.id)"
                                    class="rounded-md bg-slate-100 px-3 py-2 text-center text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                >
                                    Edit
                                </Link>
                            </div>

                            <div class="mt-2">
                                <button
                                    @click="deleteCourse(course.id)"
                                    class="w-full rounded-md bg-red-100 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-200"
                                >
                                    Delete
                                </button>
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
                        @click="closeQuickView"
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
                                @click="closeQuickView"
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
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ selectedCourse.semester || 'N/A' }}
                                </span>
                            </div>
                            <p>
                                <span class="font-semibold text-slate-700">Credits:</span>
                                {{ selectedCourse.credits }}
                            </p>
                            <p>
                                <span class="font-semibold text-slate-700">Enrollment:</span>
                                <span v-if="Number(selectedCourse.enrolled_students_count ?? 0) > 0" class="font-semibold text-emerald-700">
                                    Enrolled ({{ selectedCourse.enrolled_students_count }})
                                </span>
                                <span v-else class="font-semibold text-slate-700">Not enrolled</span>
                            </p>
                            <p>
                                <span class="font-semibold text-slate-700">Subjects:</span>
                                {{ Number(selectedCourse.subjects_count ?? 0) }}
                            </p>
                            <p>
                                <span class="font-semibold text-slate-700">Assigned teachers:</span>
                                {{ Number(selectedCourse.teachers_count ?? 0) }}
                            </p>
                            <p>
                                <span class="font-semibold text-slate-700">Attendance threshold:</span>
                                {{ formatThreshold(selectedCourse.attendance_threshold) }}
                            </p>
                            <p>
                                <span class="font-semibold text-slate-700">Last updated:</span>
                                {{ formatUpdatedAt(selectedCourse.updated_at) }}
                            </p>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <Link
                                :href="route('admin.courses.edit', selectedCourse.id)"
                                class="rounded-md bg-portal-navy px-3 py-2 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                            >
                                Edit course
                            </Link>
                            <button
                                type="button"
                                class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                @click="closeQuickView"
                            >
                                Done
                            </button>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
