<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head } from "@inertiajs/vue3";
import TimetableWeekGrid from "@/Components/TimetableWeekGrid.vue";
import { computed, ref } from "vue";

const props = defineProps({
    courses: {
        type: Array,
        required: true,
    },
});

const viewMode = ref("week"); // week | list
const query = ref("");
const selectedCourseId = ref("all");
const selectedSemester = ref("all");
const weekRange = ref("weekdays");
const timeFormat = ref("12h");

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

const printTimetable = () => window.print();

const workloadByCourse = computed(() => {
    const map = new Map();
    for (const entry of filteredEntries.value) {
        const key = `${entry.course_id}-${entry.course_code}`;
        const current = map.get(key) ?? {
            course_id: entry.course_id,
            course_code: entry.course_code,
            course_title: entry.course_title,
            sessions: 0,
            minutes: 0,
            subjects: new Set(),
        };
        current.sessions += 1;
        current.minutes += sessionDurationMinutes(entry);
        if (entry.subject_code) current.subjects.add(entry.subject_code);
        map.set(key, current);
    }

    return Array.from(map.values())
        .map((item) => ({
            ...item,
            hours: item.minutes / 60,
            subjects_count: item.subjects.size,
        }))
        .sort((a, b) => b.sessions - a.sessions);
});

const workloadBySubject = computed(() =>
    filteredEntries.value
        .reduce((acc, entry) => {
            const key = `${entry.subject_code}-${entry.course_code}`;
            if (!acc[key]) {
                acc[key] = {
                    subject_code: entry.subject_code,
                    subject_title: entry.subject_title,
                    course_code: entry.course_code,
                    sessions: 0,
                    minutes: 0,
                };
            }
            acc[key].sessions += 1;
            acc[key].minutes += sessionDurationMinutes(entry);
            return acc;
        }, {})
);

const workloadBySubjectList = computed(() =>
    Object.values(workloadBySubject.value)
        .map((item) => ({
            ...item,
            hours: item.minutes / 60,
        }))
        .sort((a, b) => b.sessions - a.sessions)
);

const exportPdfUrl = computed(() =>
    route("teacher.timetable.export", {
        format: "pdf",
        course_id: selectedCourseId.value !== "all" ? selectedCourseId.value : undefined,
        semester: selectedSemester.value !== "all" ? selectedSemester.value : undefined,
    })
);

const exportCsvUrl = computed(() =>
    route("teacher.timetable.export", {
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
                <Breadcrumb :items="[{ label: 'My Timetable' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Teaching timetable
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
                                Teaching Hours
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
                                    v-model="query"
                                    type="text"
                                    placeholder="Search subject, course, location..."
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                                <button
                                    v-if="query"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="query = ''"
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
                        </div>
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
                        v-if="filteredEntries.length > 0"
                        class="mb-5 grid gap-4 lg:grid-cols-2"
                    >
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Workload by Course
                                </p>
                                <p class="text-xs text-slate-500">
                                    Sessions and hours
                                </p>
                            </div>
                            <div class="space-y-2">
                                <div
                                    v-for="row in workloadByCourse"
                                    :key="`course-${row.course_id}`"
                                    class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-xs text-slate-700"
                                >
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-semibold">
                                            {{ row.course_code }}
                                        </span>
                                        <span>{{ row.sessions }} sessions | {{ row.hours.toFixed(1) }}h</span>
                                    </div>
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        {{ row.course_title }} - {{ row.subjects_count }} subject(s)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Workload by Subject
                                </p>
                                <p class="text-xs text-slate-500">
                                    Teaching sessions
                                </p>
                            </div>
                            <div class="space-y-2">
                                <div
                                    v-for="row in workloadBySubjectList"
                                    :key="`subject-${row.subject_code}-${row.course_code}`"
                                    class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-xs text-slate-700"
                                >
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-semibold">
                                            {{ row.subject_code }}
                                        </span>
                                        <span>{{ row.sessions }} sessions | {{ row.hours.toFixed(1) }}h</span>
                                    </div>
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        {{ row.subject_title }} ({{ row.course_code }})
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="courses.length === 0"
                        class="rounded-lg bg-slate-50 p-8 text-center"
                    >
                        <h3 class="mt-2 text-sm font-medium text-slate-900">
                            No courses assigned
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            You need to be assigned to courses to view timetable
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

