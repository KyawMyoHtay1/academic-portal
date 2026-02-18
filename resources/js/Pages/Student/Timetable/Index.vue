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
    message: {
        type: String,
        default: null,
    },
});

const viewMode = ref("week"); // week | list
const query = ref("");
const selectedCourseId = ref("all");

const allEntries = computed(() => {
    const list = [];
    for (const c of props.courses ?? []) {
        for (const e of c.timetables ?? []) {
            list.push({
                ...e,
                course_id: c.id,
                course_code: c.course_code,
                course_title: c.title,
            });
        }
    }
    return list;
});

const filteredEntries = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = allEntries.value;

    if (selectedCourseId.value !== "all") {
        list = list.filter((e) => String(e.course_id) === String(selectedCourseId.value));
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

const timeToMinutes = (timeValue) => {
    if (!timeValue) return 0;
    const [hh = "0", mm = "0"] = String(timeValue).split(":");
    return Number(hh) * 60 + Number(mm);
};

const sessionDurationMinutes = (entry) =>
    Math.max(timeToMinutes(entry?.end_time) - timeToMinutes(entry?.start_time), 0);

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
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <select
                                v-model="selectedCourseId"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-64"
                            >
                                <option value="all">All courses</option>
                                <option
                                    v-for="c in courses"
                                    :key="c.id"
                                    :value="c.id"
                                >
                                    {{ c.course_code }} - {{ c.title }}
                                </option>
                            </select>

                            <div class="relative w-full sm:w-72">
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
                                            {{ e.start_time }} - {{ e.end_time }}
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
