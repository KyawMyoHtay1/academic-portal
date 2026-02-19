<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head } from "@inertiajs/vue3";
import TimetableWeekGrid from "@/Components/TimetableWeekGrid.vue";
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
});

const urlParams = new URLSearchParams(
    typeof window !== "undefined" ? window.location.search : ""
);

const parseChoice = (value, allowed, fallback) =>
    allowed.includes(value) ? value : fallback;

const initialCourseId = urlParams.get("course_id");
const hasCourseId = (props.courses ?? []).some(
    (course) => String(course.id) === String(initialCourseId)
);

const viewMode = ref(
    parseChoice(urlParams.get("view"), ["week", "list"], "week")
); // week | list
const queryInput = ref(urlParams.get("search") ?? "");
const query = ref(queryInput.value.trim());
const selectedCourseId = ref(
    hasCourseId && initialCourseId ? String(initialCourseId) : "all"
);
const selectedSemester = ref(urlParams.get("semester") ?? "all");
const weekRange = ref(
    parseChoice(
        urlParams.get("week_range"),
        ["weekdays", "six_days", "full_week"],
        "weekdays"
    )
);
const timeFormat = ref(
    parseChoice(urlParams.get("time_format"), ["12h", "24h"], "12h")
);

const allEntries = computed(() => {
    const list = [];
    for (const c of props.courses ?? []) {
        for (const e of c.timetables ?? []) {
            list.push({
                ...e,
                course_id: c.id,
                course_code: c.course_code,
                course_title: c.title,
                semester: c.semester,
            });
        }
    }
    return list;
});

const semesterOptions = computed(() => {
    const values = new Set();
    for (const course of props.courses ?? []) {
        if (course?.semester) {
            values.add(String(course.semester));
        }
    }

    return Array.from(values).sort((a, b) => a.localeCompare(b));
});

watch(
    semesterOptions,
    (options) => {
        if (
            selectedSemester.value !== "all" &&
            !options.includes(String(selectedSemester.value))
        ) {
            selectedSemester.value = "all";
        }
    },
    { immediate: true }
);

const filteredEntries = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = allEntries.value;

    if (selectedCourseId.value !== "all") {
        list = list.filter((e) => String(e.course_id) === String(selectedCourseId.value));
    }

    if (selectedSemester.value !== "all") {
        list = list.filter((e) => String(e.semester ?? "") === String(selectedSemester.value));
    }

    if (q) {
        list = list.filter((e) => {
            const code = (e.subject_code ?? "").toLowerCase();
            const title = (e.subject_title ?? "").toLowerCase();
            const loc = (e.location ?? "").toLowerCase();
            const course = (e.course_code ?? "").toLowerCase();
            return (
                code.includes(q) ||
                title.includes(q) ||
                loc.includes(q) ||
                course.includes(q)
            );
        });
    }

    return list;
});

const dayOrder = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
];

const visibleDays = computed(() => {
    if (weekRange.value === "full_week") {
        return [...dayOrder];
    }

    if (weekRange.value === "six_days") {
        return dayOrder.slice(0, 6);
    }

    return dayOrder.slice(0, 5);
});

const timeToMinutes = (timeValue) => {
    if (!timeValue) return 0;
    const [hh = "0", mm = "0"] = String(timeValue).split(":");
    return Number(hh) * 60 + Number(mm);
};

const sessionDurationMinutes = (entry) =>
    Math.max(timeToMinutes(entry?.end_time) - timeToMinutes(entry?.start_time), 0);

const parseTimeParts = (value) => {
    if (!value) return null;
    const [hhRaw = "0", mmRaw = "0"] = String(value).split(":");
    const hh = Number.parseInt(hhRaw, 10);
    const mm = Number.parseInt(mmRaw, 10);
    if (Number.isNaN(hh) || Number.isNaN(mm)) return null;
    return { hh, mm };
};

const formatTime = (value) => {
    const parts = parseTimeParts(value);
    if (!parts) return String(value ?? "-");
    if (timeFormat.value === "24h") {
        return `${String(parts.hh).padStart(2, "0")}:${String(parts.mm).padStart(2, "0")}`;
    }
    const suffix = parts.hh >= 12 ? "PM" : "AM";
    const hour12 = parts.hh % 12 || 12;
    return `${hour12}:${String(parts.mm).padStart(2, "0")} ${suffix}`;
};

const formatRange = (entry) => `${formatTime(entry?.start_time)} - ${formatTime(entry?.end_time)}`;

const timetableStats = computed(() => {
    const rows = filteredEntries.value;
    const totalSessions = rows.length;
    const totalMinutes = rows.reduce(
        (sum, entry) => sum + sessionDurationMinutes(entry),
        0
    );
    const totalHours = (totalMinutes / 60).toFixed(1);
    const courseCount = new Set(rows.map((entry) => entry.course_id)).size;

    const dayLoad = dayOrder.map((day) => {
        const sessions = rows.filter((entry) => entry.day_of_week === day);
        const minutes = sessions.reduce(
            (sum, entry) => sum + sessionDurationMinutes(entry),
            0
        );
        return {
            day,
            sessions: sessions.length,
            hours: minutes / 60,
        };
    });

    const busiestDay = [...dayLoad].sort((a, b) => {
        if (b.sessions !== a.sessions) return b.sessions - a.sessions;
        return b.hours - a.hours;
    })[0];

    return {
        totalSessions,
        totalHours,
        courseCount,
        busiestDay:
            busiestDay && busiestDay.sessions > 0
                ? `${busiestDay.day} (${busiestDay.sessions})`
                : "N/A",
        dayLoad,
    };
});

const hasActiveFilters = computed(
    () =>
        query.value.trim() !== "" ||
        selectedCourseId.value !== "all" ||
        selectedSemester.value !== "all" ||
        weekRange.value !== "weekdays" ||
        timeFormat.value !== "12h"
);

const activeFilterChips = computed(() => {
    const chips = [];
    if (query.value.trim() !== "") {
        chips.push({ key: "search", label: `Search: ${query.value.trim()}` });
    }
    if (selectedCourseId.value !== "all") {
        const selectedCourse = (props.courses ?? []).find(
            (course) => String(course.id) === String(selectedCourseId.value)
        );
        chips.push({
            key: "course_id",
            label: `Course: ${selectedCourse?.course_code ?? selectedCourseId.value}`,
        });
    }
    if (selectedSemester.value !== "all") {
        chips.push({
            key: "semester",
            label: `Semester: ${selectedSemester.value}`,
        });
    }
    if (weekRange.value !== "weekdays") {
        const map = {
            six_days: "Mon-Sat",
            full_week: "Mon-Sun",
        };
        chips.push({
            key: "week_range",
            label: `Week range: ${map[weekRange.value] ?? weekRange.value}`,
        });
    }
    if (timeFormat.value !== "12h") {
        chips.push({ key: "time_format", label: "Time: 24-hour" });
    }
    return chips;
});

const printTimetable = () => window.print();

const clearFilters = () => {
    queryInput.value = "";
    query.value = "";
    selectedCourseId.value = "all";
    selectedSemester.value = "all";
    weekRange.value = "weekdays";
    timeFormat.value = "12h";
};

const removeFilterChip = (key) => {
    if (key === "search") {
        queryInput.value = "";
        query.value = "";
        return;
    }
    if (key === "course_id") {
        selectedCourseId.value = "all";
        return;
    }
    if (key === "semester") {
        selectedSemester.value = "all";
        return;
    }
    if (key === "week_range") {
        weekRange.value = "weekdays";
        return;
    }
    if (key === "time_format") {
        timeFormat.value = "12h";
    }
};

const applySearch = debounce(() => {
    query.value = queryInput.value.trim();
}, 300);

const persistFiltersToUrl = debounce(() => {
    if (typeof window === "undefined") {
        return;
    }

    const nextParams = new URLSearchParams();
    if (query.value.trim() !== "") {
        nextParams.set("search", query.value.trim());
    }
    if (selectedCourseId.value !== "all") {
        nextParams.set("course_id", String(selectedCourseId.value));
    }
    if (selectedSemester.value !== "all") {
        nextParams.set("semester", String(selectedSemester.value));
    }
    if (weekRange.value !== "weekdays") {
        nextParams.set("week_range", weekRange.value);
    }
    if (timeFormat.value !== "12h") {
        nextParams.set("time_format", timeFormat.value);
    }
    if (viewMode.value !== "week") {
        nextParams.set("view", viewMode.value);
    }

    const queryString = nextParams.toString();
    const targetUrl = `${window.location.pathname}${queryString ? `?${queryString}` : ""}`;
    window.history.replaceState(window.history.state, "", targetUrl);
}, 300);

watch(
    () => queryInput.value,
    () => {
        applySearch();
    }
);

watch(
    () => [
        query.value,
        selectedCourseId.value,
        selectedSemester.value,
        weekRange.value,
        timeFormat.value,
        viewMode.value,
    ],
    () => {
        persistFiltersToUrl();
    }
);

onBeforeUnmount(() => {
    applySearch.cancel();
    persistFiltersToUrl.cancel();
});

const exportPdfUrl = computed(() =>
    route("student.timetable.export", {
        format: "pdf",
        course_id: selectedCourseId.value !== "all" ? selectedCourseId.value : undefined,
        semester: selectedSemester.value !== "all" ? selectedSemester.value : undefined,
    })
);

const exportCsvUrl = computed(() =>
    route("student.timetable.export", {
        format: "csv",
        course_id: selectedCourseId.value !== "all" ? selectedCourseId.value : undefined,
        semester: selectedSemester.value !== "all" ? selectedSemester.value : undefined,
    })
);
</script>

<template>
    <Head title="My Timetable" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                My Timetable
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Timetable' }]" />
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

                <div v-else class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            My weekly timetable
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Visual week view with filters and search
                        </p>
                    </div>

                    <div class="mb-5 grid gap-4 md:grid-cols-4">
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">
                                Sessions
                            </p>
                            <p class="mt-2 text-2xl font-bold text-blue-900">
                                {{ timetableStats.totalSessions }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                                Study Hours
                            </p>
                            <p class="mt-2 text-2xl font-bold text-emerald-900">
                                {{ timetableStats.totalHours }}h
                            </p>
                        </div>
                        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">
                                Courses
                            </p>
                            <p class="mt-2 text-2xl font-bold text-indigo-900">
                                {{ timetableStats.courseCount }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                                Busiest Day
                            </p>
                            <p class="mt-2 text-lg font-bold text-amber-900">
                                {{ timetableStats.busiestDay }}
                            </p>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="grid w-full gap-2 sm:grid-cols-2 lg:grid-cols-5">
                            <select
                                v-model="selectedCourseId"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All courses</option>
                                <option
                                    v-for="c in courses"
                                    :key="c.id"
                                    :value="c.id"
                                >
                                    {{ c.course_code }} - {{ c.title }}{{ c.semester ? ` (${c.semester})` : "" }}
                                </option>
                            </select>
                            <select
                                v-model="selectedSemester"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All semesters</option>
                                <option
                                    v-for="semester in semesterOptions"
                                    :key="semester"
                                    :value="semester"
                                >
                                    {{ semester }}
                                </option>
                            </select>
                            <select
                                v-model="weekRange"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="weekdays">Weekdays (Mon-Fri)</option>
                                <option value="six_days">Mon-Sat</option>
                                <option value="full_week">Full week (Mon-Sun)</option>
                            </select>
                            <select
                                v-model="timeFormat"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="12h">12-hour time</option>
                                <option value="24h">24-hour time</option>
                            </select>

                            <div class="relative w-full">
                                <input
                                    v-model="queryInput"
                                    type="text"
                                    placeholder="Search subject, course, location..."
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                                <button
                                    v-if="queryInput"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="
                                        queryInput = '';
                                        query = '';
                                    "
                                >
                                    <span class="sr-only">Clear</span>
                                    x
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a
                                :href="exportPdfUrl"
                                target="_blank"
                                class="rounded-md bg-indigo-100 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-200"
                            >
                                Export PDF
                            </a>
                            <a
                                :href="exportCsvUrl"
                                class="rounded-md bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                            >
                                Export CSV
                            </a>
                            <div class="inline-flex rounded-md bg-slate-100 p-1">
                                <button
                                    type="button"
                                    class="rounded-md px-3 py-1.5 text-xs font-semibold"
                                    :class="viewMode === 'week' ? 'bg-white text-slate-900 shadow' : 'text-slate-600 hover:text-slate-900'"
                                    @click="viewMode = 'week'"
                                >
                                    Week view
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md px-3 py-1.5 text-xs font-semibold"
                                    :class="viewMode === 'list' ? 'bg-white text-slate-900 shadow' : 'text-slate-600 hover:text-slate-900'"
                                    @click="viewMode = 'list'"
                                >
                                    List view
                                </button>
                            </div>
                            <button
                                type="button"
                                class="rounded-md bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                @click="printTimetable"
                            >
                                Print
                            </button>
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
                        class="mb-5 flex flex-wrap items-center gap-2"
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
                        v-if="filteredEntries.length > 0"
                        class="mb-5 rounded-xl border border-slate-200 bg-white p-4"
                    >
                        <div class="mb-3 flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Weekly Load
                            </p>
                            <p class="text-xs text-slate-500">
                                Sessions and hours by day
                            </p>
                        </div>

                        <div class="grid gap-2 md:grid-cols-2">
                            <div
                                v-for="load in timetableStats.dayLoad"
                                :key="load.day"
                                class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2"
                            >
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-slate-700">
                                        {{ load.day }}
                                    </span>
                                    <span class="text-slate-500">
                                        {{ load.sessions }} sessions | {{ load.hours.toFixed(1) }}h
                                    </span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                                    <div
                                        class="h-full rounded-full bg-portal-navy"
                                        :style="{
                                            width: `${
                                                timetableStats.totalSessions > 0
                                                    ? (load.sessions / timetableStats.totalSessions) * 100
                                                    : 0
                                            }%`,
                                        }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="courses.length === 0"
                        class="rounded-lg bg-slate-50 p-8 text-center"
                    >
                        <h3 class="mt-2 text-sm font-medium text-slate-900">
                            No enrolled courses
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            You need to enroll in courses to view timetable
                            entries.
                        </p>
                    </div>

                    <div v-else>
                        <div v-if="filteredEntries.length === 0" class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-600">
                            No sessions match your filters.
                        </div>

                        <TimetableWeekGrid
                            v-if="viewMode === 'week' && filteredEntries.length > 0"
                            :entries="filteredEntries"
                            :showCourse="selectedCourseId === 'all'"
                            :days="visibleDays"
                            :timeFormat="timeFormat"
                            :highlightToday="true"
                        />

                        <div v-else-if="viewMode === 'list' && filteredEntries.length > 0" class="overflow-hidden rounded-md border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                            Subject
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                            Course
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                            Day
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                            Time
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                            Location
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    <tr v-for="e in filteredEntries" :key="e.id" class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ e.subject_code || "N/A" }}</span>
                                                <span class="text-xs text-slate-500">{{ e.subject_title || "" }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            {{ e.course_code }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            {{ e.day_of_week }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            {{ formatRange(e) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            {{ e.location || "-" }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
