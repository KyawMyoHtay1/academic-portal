<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { formatDateTimeLocal } from "@/utils/dateTime";
import { Head } from "@inertiajs/vue3";
import { computed, onBeforeUnmount, ref, watch } from "vue";
import debounce from "lodash/debounce";

const props = defineProps({
    courses: {
        type: Array,
        required: true,
    },
    message: {
        type: String,
        default: null,
    },
    gpa: {
        type: Number,
        default: null,
    },
    totalGrades: {
        type: Number,
        default: 0,
    },
});

const urlParams = new URLSearchParams(
    typeof window !== "undefined" ? window.location.search : ""
);

const searchInput = ref(urlParams.get("search") ?? "");
const searchTerm = ref(searchInput.value.trim());
const semesterFilter = ref(urlParams.get("semester") ?? "all");
const showOnlyGraded = ref(urlParams.get("graded_only") === "1");
const expandedSubjects = ref(new Set());

const toggleSubjectExpansion = (subjectId) => {
    if (expandedSubjects.value.has(subjectId)) {
        expandedSubjects.value.delete(subjectId);
    } else {
        expandedSubjects.value.add(subjectId);
    }
};

const semesters = computed(() => {
    const set = new Set();

    props.courses.forEach((course) => {
        if (course.semester) {
            set.add(course.semester);
        }
    });

    return Array.from(set).sort();
});

watch(
    semesters,
    (options) => {
        if (
            semesterFilter.value !== "all" &&
            !options.includes(String(semesterFilter.value))
        ) {
            semesterFilter.value = "all";
        }
    },
    { immediate: true }
);

const filteredCourses = computed(() => {
    return props.courses
        .map((course) => {
            const filteredSubjects = (course.subjects || []).filter(
                (subject) => {
                    if (
                        showOnlyGraded.value &&
                        (subject.score === null || subject.score === undefined)
                    ) {
                        return false;
                    }

                    const term = searchTerm.value.trim().toLowerCase();

                    if (term) {
                        const haystack = (
                            course.course_code +
                            " " +
                            course.title +
                            " " +
                            subject.subject_code +
                            " " +
                            subject.title
                        ).toLowerCase();

                        if (!haystack.includes(term)) {
                            return false;
                        }
                    }

                    return true;
                }
            );

            return {
                ...course,
                subjects: filteredSubjects,
            };
        })
        .filter((course) => {
            if (
                semesterFilter.value !== "all" &&
                String(course.semester ?? "") !== String(semesterFilter.value)
            ) {
                return false;
            }

            if (searchTerm.value || showOnlyGraded.value) {
                return course.subjects && course.subjects.length > 0;
            }

            return true;
        });
});

const hasActiveFilters = computed(
    () =>
        searchTerm.value.trim() !== "" ||
        semesterFilter.value !== "all" ||
        showOnlyGraded.value
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
    if (showOnlyGraded.value) {
        chips.push({
            key: "graded_only",
            label: "Only graded",
        });
    }

    return chips;
});

const clearFilters = () => {
    searchInput.value = "";
    searchTerm.value = "";
    semesterFilter.value = "all";
    showOnlyGraded.value = false;
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
    if (key === "graded_only") {
        showOnlyGraded.value = false;
    }
};

const applySearch = debounce(() => {
    searchTerm.value = searchInput.value.trim();
}, 300);

const persistFiltersToUrl = debounce(() => {
    if (typeof window === "undefined") {
        return;
    }

    const nextParams = new URLSearchParams();
    if (searchTerm.value.trim() !== "") {
        nextParams.set("search", searchTerm.value.trim());
    }
    if (semesterFilter.value !== "all") {
        nextParams.set("semester", String(semesterFilter.value));
    }
    if (showOnlyGraded.value) {
        nextParams.set("graded_only", "1");
    }

    const queryString = nextParams.toString();
    const targetUrl = `${window.location.pathname}${queryString ? `?${queryString}` : ""}`;
    window.history.replaceState(window.history.state, "", targetUrl);
}, 300);

watch(
    () => searchInput.value,
    () => {
        applySearch();
    }
);

watch(
    () => [searchTerm.value, semesterFilter.value, showOnlyGraded.value],
    () => {
        persistFiltersToUrl();
    }
);

onBeforeUnmount(() => {
    applySearch.cancel();
    persistFiltersToUrl.cancel();
});

const gradeSummary = computed(() => {
    let totalScore = 0;
    let gradedCount = 0;
    let totalSubjects = 0;
    const coursesWithAnyGrade = new Set();

    props.courses.forEach((course) => {
        (course.subjects || []).forEach((subject) => {
            totalSubjects += 1;
            if (subject.score !== null && subject.score !== undefined) {
                totalScore += Number(subject.score);
                gradedCount += 1;
                coursesWithAnyGrade.add(course.id);
            }
        });
    });

    return {
        totalSubjects,
        gradedCount,
        coursesWithGrades: coursesWithAnyGrade.size,
        averageScore: gradedCount ? totalScore / gradedCount : null,
    };
});

const semesterSortWeight = (semesterLabel) => {
    const text = String(semesterLabel ?? "").toLowerCase();
    const numericMatch = text.match(/(\d+)/);
    if (numericMatch) {
        return parseInt(numericMatch[1], 10);
    }

    return Number.MAX_SAFE_INTEGER;
};

const gradeTrend = computed(() => {
    const buckets = new Map();

    props.courses.forEach((course) => {
        const semesterLabel = course.semester || "Unspecified";
        if (!buckets.has(semesterLabel)) {
            buckets.set(semesterLabel, {
                label: semesterLabel,
                sortWeight: semesterSortWeight(semesterLabel),
                sum: 0,
                count: 0,
            });
        }

        const bucket = buckets.get(semesterLabel);
        (course.subjects || []).forEach((subject) => {
            if (subject.score === null || subject.score === undefined || subject.score === "") {
                return;
            }

            const numericScore = Number(subject.score);
            if (Number.isNaN(numericScore)) {
                return;
            }

            bucket.sum += numericScore;
            bucket.count += 1;
        });
    });

    return Array.from(buckets.values())
        .filter((bucket) => bucket.count > 0)
        .sort(
            (a, b) =>
                a.sortWeight - b.sortWeight || a.label.localeCompare(b.label),
        )
        .map((bucket) => ({
            label: bucket.label,
            average: bucket.sum / bucket.count,
            count: bucket.count,
        }));
});

const gradeTrendDelta = computed(() => {
    if (gradeTrend.value.length < 2) {
        return null;
    }

    const previous = gradeTrend.value[gradeTrend.value.length - 2];
    const current = gradeTrend.value[gradeTrend.value.length - 1];

    return current.average - previous.average;
});

const hasFinalScore = (subject) =>
    subject?.score !== null &&
    subject?.score !== undefined &&
    subject?.score !== "";

const hasSubjectDetails = (subject) =>
    Boolean(subject?.has_assignments || (subject?.grade_history?.length ?? 0) > 0);

const formatHistoryAction = (action) => {
    if (action === "submitted") return "Submitted for review";
    if (action === "approved") return "Approved";
    if (action === "rejected") return "Rejected";
    return action;
};

const historyActionClass = (action) => {
    if (action === "approved") return "bg-emerald-100 text-emerald-800";
    if (action === "rejected") return "bg-red-100 text-red-800";
    if (action === "submitted") return "bg-indigo-100 text-indigo-800";
    return "bg-slate-100 text-slate-700";
};

// Convert numeric score to letter grade
// Grading scale: A: 80-100, B: 70-79, C: 60-69, D: 50-59, E: 40-49, F: 0-39
const getLetterGrade = (score) => {
    if (score === null || score === undefined) return null;
    const s = parseFloat(score);
    if (isNaN(s)) return null;
    if (s >= 80) return { letter: "A", class: "bg-emerald-100 text-emerald-800" };
    if (s >= 70) return { letter: "B", class: "bg-blue-100 text-blue-800" };
    if (s >= 60) return { letter: "C", class: "bg-amber-100 text-amber-800" };
    if (s >= 50) return { letter: "D", class: "bg-yellow-100 text-yellow-800" };
    if (s >= 40) return { letter: "E", class: "bg-orange-100 text-orange-800" };
    if (s >= 0) return { letter: "F", class: "bg-red-100 text-red-800" };
    return null;
};
</script>

<template>
    <Head title="My Grades" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Grades
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Grades' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- No Student Record Message -->
                <div v-if="message" class="portal-card p-6">
                    <div
                        class="rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200"
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
                                    Student Record Not Found
                                </h3>
                                <div class="mt-2 text-sm text-amber-700">
                                    <p>{{ message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grades List -->
                <div v-else class="portal-card overflow-hidden p-6">
                    <!-- Summary stats -->
                    <div
                        v-if="courses.length > 0"
                        class="mb-6 grid gap-4 md:grid-cols-4"
                    >
                        <div class="portal-card p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Subjects
                            </p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">
                                {{ gradeSummary.totalSubjects }}
                            </p>
                        </div>
                        <div class="portal-card p-5 bg-emerald-50">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                            >
                                Graded
                            </p>
                            <p class="mt-2 text-2xl font-bold text-emerald-900">
                                {{ gradeSummary.gradedCount }}
                            </p>
                        </div>
                        <div class="portal-card p-5 bg-amber-50">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-amber-700"
                            >
                                Not graded yet
                            </p>
                            <p class="mt-2 text-2xl font-bold text-amber-900">
                                {{
                                    gradeSummary.totalSubjects -
                                    gradeSummary.gradedCount
                                }}
                            </p>
                        </div>
                        <div
                            v-if="gpa !== null"
                            class="portal-card p-5 bg-indigo-50"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-indigo-700"
                            >
                                GPA (Grade Point Average)
                            </p>
                            <p class="mt-2 text-2xl font-bold text-indigo-900">
                                {{ gpa.toFixed(2) }}
                            </p>
                            <p class="mt-1 text-xs text-indigo-700">
                                Based on {{ totalGrades }} grade(s)
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="gradeTrend.length > 0"
                        class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50/40 p-4"
                    >
                        <div
                            class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-indigo-700"
                                >
                                    Grade Trend by Semester
                                </p>
                                <p class="mt-1 text-xs text-indigo-700">
                                    Average approved score across each semester.
                                </p>
                            </div>
                            <p
                                v-if="gradeTrendDelta !== null"
                                class="text-xs font-semibold"
                                :class="
                                    gradeTrendDelta >= 0
                                        ? 'text-emerald-700'
                                        : 'text-red-700'
                                "
                            >
                                {{
                                    gradeTrendDelta >= 0
                                        ? `+${gradeTrendDelta.toFixed(1)}`
                                        : gradeTrendDelta.toFixed(1)
                                }} pts vs previous semester
                            </p>
                        </div>

                        <div
                            class="mt-4 flex items-end gap-3 overflow-x-auto pb-1"
                        >
                            <div
                                v-for="point in gradeTrend"
                                :key="point.label"
                                class="min-w-[72px] text-center"
                            >
                                <div
                                    class="mx-auto flex h-28 w-8 items-end overflow-hidden rounded-md bg-indigo-100"
                                >
                                    <div
                                        class="w-full rounded-md bg-indigo-500 transition-all duration-500"
                                        :style="{
                                            height: `${Math.max(Math.min(point.average, 100), 4)}%`,
                                        }"
                                        :title="`${point.label}: ${point.average.toFixed(1)}% (${point.count} subject(s))`"
                                    ></div>
                                </div>
                                <p class="mt-2 text-[11px] text-slate-600">
                                    {{ point.label }}
                                </p>
                                <p class="text-xs font-semibold text-slate-900">
                                    {{ point.average.toFixed(1) }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mb-4 flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
                    >
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Subject Grades
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                View grades for subjects in your enrolled
                                courses
                            </p>
                        </div>
                        <div
                            v-if="gradeSummary.gradedCount > 0 || gpa !== null"
                            class="rounded-lg bg-emerald-50 px-4 py-3 text-right shadow-sm"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                            >
                                Academic Summary
                            </p>
                            <p
                                v-if="gradeSummary.gradedCount > 0"
                                class="mt-1 text-xs text-emerald-800"
                            >
                                Graded subjects:
                                <span class="font-semibold">{{
                                    gradeSummary.gradedCount
                                }}</span>
                            </p>
                            <p
                                v-if="gpa !== null"
                                class="mt-0.5 text-xs text-emerald-800"
                            >
                                Overall GPA:
                                <span class="font-semibold text-base">
                                    {{ gpa.toFixed(2) }}
                                </span>
                            </p>
                            <p
                                v-else-if="gradeSummary.averageScore !== null"
                                class="mt-0.5 text-xs text-emerald-800"
                            >
                                Average score:
                                <span class="font-semibold">
                                    {{ gradeSummary.averageScore.toFixed(2) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Grades by Course -->
                    <div v-if="courses.length > 0" class="space-y-6">
                        <!-- Filters -->
                        <div
                            class="mb-2 flex flex-col gap-3 md:flex-row md:items-end md:justify-between"
                        >
                            <div class="flex-1">
                                <label
                                    for="grades-search"
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
                                        id="grades-search"
                                        v-model="searchInput"
                                        type="search"
                                        class="block w-full rounded-md border-slate-300 py-2 pl-9 pr-9 text-sm placeholder-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Search by course or subject name / code"
                                    />
                                    <button
                                        v-if="searchInput"
                                        type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                        @click="
                                            searchInput = '';
                                            searchTerm = '';
                                        "
                                    >
                                        <span class="sr-only">Clear search</span>
                                        x
                                    </button>
                                </div>
                            </div>

                            <div
                                class="flex flex-col gap-2 md:w-72 md:flex-row md:items-center md:justify-end"
                            >
                                <div class="md:w-40">
                                    <label
                                        for="semester-filter"
                                        class="block text-xs font-medium text-slate-600"
                                    >
                                        Semester
                                    </label>
                                    <select
                                        id="semester-filter"
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

                                <label
                                    class="inline-flex items-center gap-2 text-xs font-medium text-slate-600"
                                >
                                    <input
                                        v-model="showOnlyGraded"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    Show only graded subjects
                                </label>
                                <button
                                    v-if="hasActiveFilters"
                                    type="button"
                                    class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                    @click="clearFilters"
                                >
                                    Clear all filters
                                </button>
                            </div>
                        </div>

                        <div
                            v-if="activeFilterChips.length > 0"
                            class="mt-2 flex flex-wrap items-center gap-2"
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

                        <div
                            v-if="filteredCourses.length > 0"
                            class="space-y-6"
                        >
                            <div
                                v-for="course in filteredCourses"
                                :key="course.id"
                                class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                            >
                                <div class="mb-3 flex items-center gap-3">
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
                                    <div>
                                        <h3
                                            class="text-sm font-semibold text-slate-900"
                                        >
                                            {{ course.course_code }} -
                                            {{ course.title }}
                                        </h3>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ course.credits }} credits •
                                            {{ course.semester }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        course.subjects &&
                                        course.subjects.length > 0
                                    "
                                    class="overflow-x-auto"
                                >
                                    <table
                                        class="min-w-full divide-y divide-slate-200"
                                    >
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Subject
                                                </th>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Score & Grade
                                                </th>
                                                <th
                                                    class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="divide-y divide-slate-200 bg-white"
                                        >
                                            <template
                                                v-for="subject in course.subjects"
                                                :key="subject.id"
                                            >
                                            <tr
                                                class="bg-white hover:bg-slate-50 transition-colors"
                                            >
                                                <td
                                                    class="px-4 py-3 text-sm text-slate-700"
                                                >
                                                    <div
                                                        class="flex items-center gap-3"
                                                    >
                                                        <div
                                                            class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                                        >
                                                            <img
                                                                v-if="
                                                                    subject.photo
                                                                "
                                                                :src="`/storage/${subject.photo}`"
                                                                :alt="`Photo for ${subject.title}`"
                                                                class="h-full w-full object-cover"
                                                            />
                                                            <span
                                                                v-else
                                                                class="text-xs font-semibold text-slate-500"
                                                            >
                                                                {{
                                                                    subject.title
                                                                        .charAt(
                                                                            0
                                                                        )
                                                                        .toUpperCase()
                                                                }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div
                                                                class="text-sm font-medium text-slate-900"
                                                            >
                                                                {{
                                                                    subject.subject_code
                                                                }}
                                                            </div>
                                                            <div
                                                                class="text-xs text-slate-500"
                                                            >
                                                                {{
                                                                    subject.title
                                                                }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <div
                                                        v-if="
                                                            subject.score !==
                                                                null &&
                                                            subject.score !==
                                                                undefined
                                                        "
                                                        class="flex items-center gap-2"
                                                    >
                                                        <span
                                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                                            :class="getLetterGrade(subject.score)?.class || 'bg-slate-100 text-slate-700'"
                                                        >
                                                            {{
                                                                Number(
                                                                    subject.score
                                                                ).toFixed(2)
                                                            }}
                                                        </span>
                                                        <span
                                                            v-if="getLetterGrade(subject.score)"
                                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                                            :class="getLetterGrade(subject.score).class"
                                                        >
                                                            {{
                                                                getLetterGrade(
                                                                    subject.score
                                                                ).letter
                                                            }}
                                                        </span>
                                                    </div>
                                                    <span
                                                        v-else
                                                        class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600"
                                                    >
                                                        Not graded yet
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button
                                                        v-if="hasSubjectDetails(subject)"
                                                        type="button"
                                                        @click="toggleSubjectExpansion(subject.id)"
                                                        class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                                    >
                                                        <span v-if="expandedSubjects.has(subject.id)">
                                                            Hide Details
                                                        </span>
                                                        <span v-else>
                                                            View Details
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Assignment Breakdown Row -->
                                            <tr
                                                v-if="expandedSubjects.has(subject.id) && hasSubjectDetails(subject)"
                                                class="bg-slate-50"
                                            >
                                                <td colspan="3" class="px-4 py-4">
                                                    <div class="space-y-3">
                                                        <div class="flex items-center justify-between">
                                                            <h4 class="text-sm font-semibold text-slate-900">
                                                                Assignment and Grade Details
                                                            </h4>
                                                            <div class="flex items-center gap-4 text-xs">
                                                                <span
                                                                    v-if="subject.computed_grade !== null"
                                                                    class="text-slate-600"
                                                                >
                                                                    Computed Grade:
                                                                    <span
                                                                        class="ml-1 font-semibold"
                                                                        :class="getLetterGrade(subject.computed_grade)?.class || 'text-slate-700'"
                                                                    >
                                                                        {{ subject.computed_grade.toFixed(2) }}%
                                                                    </span>
                                                                </span>
                                                                <span
                                                                    v-if="hasFinalScore(subject)"
                                                                    class="text-slate-600"
                                                                >
                                                                    Final Grade:
                                                                    <span
                                                                        class="ml-1 font-semibold"
                                                                        :class="getLetterGrade(subject.score)?.class || 'text-slate-700'"
                                                                    >
                                                                        {{ Number(subject.score).toFixed(2) }}%
                                                                    </span>
                                                                    <span
                                                                        v-if="subject.grade_status === 'approved'"
                                                                        class="ml-2 inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800"
                                                                    >
                                                                        Approved
                                                                    </span>
                                                                    <span
                                                                        v-else-if="subject.grade_status === 'pending'"
                                                                        class="ml-2 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800"
                                                                    >
                                                                        Pending
                                                                    </span>
                                                                </span>
                                                                <span class="text-slate-600">
                                                                    Graded: {{ subject.graded_assignments }}/{{ subject.total_assignments }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="subject.assignment_breakdown?.length > 0"
                                                            class="overflow-x-auto"
                                                        >
                                                            <table class="min-w-full divide-y divide-slate-200">
                                                                <thead class="bg-white">
                                                                    <tr>
                                                                        <th class="px-3 py-2 text-left text-xs font-medium text-slate-600">
                                                                            Assignment
                                                                        </th>
                                                                        <th class="px-3 py-2 text-center text-xs font-medium text-slate-600">
                                                                            Due Date
                                                                        </th>
                                                                        <th class="px-3 py-2 text-center text-xs font-medium text-slate-600">
                                                                            Status
                                                                        </th>
                                                                        <th class="px-3 py-2 text-center text-xs font-medium text-slate-600">
                                                                            Graded By
                                                                        </th>
                                                                        <th class="px-3 py-2 text-center text-xs font-medium text-slate-600">
                                                                            Score
                                                                        </th>
                                                                        <th class="px-3 py-2 text-center text-xs font-medium text-slate-600">
                                                                            Percentage
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-slate-200 bg-white">
                                                                    <tr
                                                                        v-for="assignment in subject.assignment_breakdown"
                                                                        :key="assignment.assignment_id"
                                                                    >
                                                                        <td class="px-3 py-2 text-sm text-slate-900">
                                                                            {{ assignment.title }}
                                                                        </td>
                                                                        <td class="px-3 py-2 text-center text-xs text-slate-600">
                                                                            {{ assignment.due_date }}
                                                                        </td>
                                                                        <td class="px-3 py-2 text-center">
                                                                            <span
                                                                                v-if="assignment.graded"
                                                                                class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-800"
                                                                            >
                                                                                Graded
                                                                            </span>
                                                                            <span
                                                                                v-else-if="assignment.submitted"
                                                                                class="inline-flex rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-800"
                                                                            >
                                                                                Submitted
                                                                            </span>
                                                                            <span
                                                                                v-else
                                                                                class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600"
                                                                            >
                                                                                Not submitted
                                                                            </span>
                                                                        </td>
                                                                        <td class="px-3 py-2 text-center text-sm text-slate-700">
                                                                            <span v-if="assignment.graded_by">
                                                                                {{ assignment.graded_by }}
                                                                            </span>
                                                                            <span v-else class="text-slate-400">-</span>
                                                                        </td>
                                                                        <td class="px-3 py-2 text-center text-sm">
                                                                            <span
                                                                                v-if="assignment.score !== null"
                                                                                class="font-medium"
                                                                                :class="getLetterGrade(assignment.percentage)?.class || 'text-slate-700'"
                                                                            >
                                                                                {{ assignment.score }}/{{ assignment.max_score }}
                                                                            </span>
                                                                            <span v-else class="text-slate-400">—</span>
                                                                        </td>
                                                                        <td class="px-3 py-2 text-center text-sm">
                                                                            <span
                                                                                v-if="assignment.percentage !== null"
                                                                                class="font-medium"
                                                                                :class="getLetterGrade(assignment.percentage)?.class || 'text-slate-700'"
                                                                            >
                                                                                {{ assignment.percentage.toFixed(1) }}%
                                                                            </span>
                                                                            <span v-else class="text-slate-400">—</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div
                                                            v-else
                                                            class="rounded-md bg-slate-50 p-3 text-center text-xs text-slate-500"
                                                        >
                                                            No assignments found for this subject.
                                                        </div>

                                                        <div class="rounded-md border border-slate-200 bg-white p-3">
                                                            <div class="mb-2 flex items-center justify-between">
                                                                <h5 class="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                                                    Grade History Timeline
                                                                </h5>
                                                                <span class="text-[11px] text-slate-500">
                                                                    {{ subject.grade_history?.length ?? 0 }} event(s)
                                                                </span>
                                                            </div>

                                                            <div
                                                                v-if="subject.grade_history?.length > 0"
                                                                class="space-y-2"
                                                            >
                                                                <div
                                                                    v-for="event in subject.grade_history"
                                                                    :key="event.id"
                                                                    class="rounded-md bg-slate-50 px-3 py-2 text-xs"
                                                                >
                                                                    <div class="flex flex-wrap items-center gap-2">
                                                                        <span
                                                                            class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                                                            :class="historyActionClass(event.action)"
                                                                        >
                                                                            {{ formatHistoryAction(event.action) }}
                                                                        </span>
                                                                        <span class="text-slate-700">
                                                                            {{ event.performed_by ?? "System" }}
                                                                        </span>
                                                                        <span class="text-slate-500">
                                                                            {{ formatDateTimeLocal(event.performed_at) }}
                                                                        </span>
                                                                    </div>
                                                                    <p
                                                                        v-if="event.reason"
                                                                        class="mt-1 text-[11px] text-red-700"
                                                                    >
                                                                        Reason: {{ event.reason }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <p v-else class="text-xs text-slate-500">
                                                                No review timeline available yet.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else class="text-sm text-slate-500 py-2">
                                    No subjects with grades yet.
                                </div>
                            </div>
                        </div>
                        <div
                            v-else
                            class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-500"
                        >
                            No subjects match your current search or filters.
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="rounded-lg bg-slate-50 p-8 text-center">
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
                        <h3 class="mt-2 text-sm font-medium text-slate-900">
                            No enrolled courses
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            You haven't enrolled in any courses yet.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
