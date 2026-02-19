<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Pagination from "@/Components/Pagination.vue";
import DashboardChart from "@/Components/Dashboard/DashboardChart.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps({
    overall: {
        type: Object,
        required: true,
    },
    byCourse: {
        type: Array,
        default: () => [],
    },
    bySubject: {
        type: Array,
        default: () => [],
    },
    lowAttendanceStudents: {
        type: Array,
        default: () => [],
    },
    recentRecords: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    trend: {
        type: Object,
        default: () => ({
            weekly: [],
            monthly: [],
        }),
    },
    defaults: {
        type: Object,
        default: () => ({
            threshold: 75,
            cooldown_days: 7,
        }),
    },
    options: {
        type: Object,
        default: () => ({
            programmes: [],
            intakeYears: [],
            semesters: [],
            courses: [],
            subjects: [],
        }),
    },
    thresholdContext: {
        type: Object,
        default: () => ({
            value: 75,
            source: "global",
            label: "global",
        }),
    },
});

const searchStudents = ref("");
const searchCourses = ref("");
const searchSubjects = ref("");
const searchRecent = ref("");
const programmeFilter = ref(props.filters?.programme || "all");
const intakeYearFilter = ref(props.filters?.intake_year || "all");
const semesterFilter = ref(props.filters?.semester || "all");
const courseFilter = ref(
    props.filters?.course_id ? String(props.filters.course_id) : "all"
);
const subjectFilter = ref(
    props.filters?.subject_id ? String(props.filters.subject_id) : "all"
);
const dateFrom = ref(props.filters?.date_from || "");
const dateTo = ref(props.filters?.date_to || "");
const defaultThreshold = Number(props.defaults?.threshold ?? 75);
const defaultCooldownDays = Number(props.defaults?.cooldown_days ?? 7);
const thresholdFilter = ref(Number(props.filters?.threshold ?? defaultThreshold));
const courseThresholdFilter = ref(
    props.filters?.course_threshold ?? ""
);
const subjectThresholdFilter = ref(
    props.filters?.subject_threshold ?? ""
);
const alertCooldownDays = ref(Number(props.filters?.cooldown_days ?? defaultCooldownDays));
const trendMode = ref(props.filters?.trend_mode || "weekly");
const sendingAlerts = ref(false);

const normalizedThreshold = () => {
    const value = Number(thresholdFilter.value);
    return Number.isFinite(value) && value >= 1 && value <= 100 ? value : undefined;
};

const normalizedCooldownDays = () => {
    const value = Number(alertCooldownDays.value);
    return Number.isFinite(value) && value >= 0 && value <= 90 ? value : undefined;
};

const normalizedScopedThreshold = (rawValue) => {
    const value = Number(rawValue);
    return Number.isFinite(value) && value >= 1 && value <= 100 ? value : undefined;
};

const normalizedCourseThreshold = () =>
    normalizedScopedThreshold(courseThresholdFilter.value);
const normalizedSubjectThreshold = () =>
    normalizedScopedThreshold(subjectThresholdFilter.value);

const subjectOptions = computed(() => {
    const allSubjects = props.options?.subjects ?? [];
    if (courseFilter.value === "all") {
        return allSubjects;
    }

    return allSubjects.filter(
        (subject) => String(subject.course_id) === String(courseFilter.value)
    );
});

const effectiveThreshold = computed(() => {
    const subjectValue = normalizedSubjectThreshold();
    if (subjectFilter.value !== "all" && subjectValue !== undefined) {
        return {
            value: subjectValue,
            source: "subject",
            label: "subject",
        };
    }

    const courseValue = normalizedCourseThreshold();
    if (courseFilter.value !== "all" && courseValue !== undefined) {
        return {
            value: courseValue,
            source: "course",
            label: "course",
        };
    }

    const globalValue = normalizedThreshold();
    return {
        value: globalValue ?? defaultThreshold,
        source: "global",
        label: "global",
    };
});

const thresholdLabel = computed(() => {
    return Number(effectiveThreshold.value.value).toFixed(1);
});

const thresholdScopeLabel = computed(() => effectiveThreshold.value.label);

const cooldownLabel = computed(() => {
    const value = normalizedCooldownDays();
    return String(value ?? defaultCooldownDays);
});

const thresholdValue = computed(() => Number(effectiveThreshold.value.value));

const applyFilters = () => {
    router.get(
        route("admin.attendance.report"),
        {
            programme:
                programmeFilter.value !== "all"
                    ? programmeFilter.value
                    : undefined,
            intake_year:
                intakeYearFilter.value !== "all"
                    ? intakeYearFilter.value
                    : undefined,
            semester:
                semesterFilter.value !== "all"
                    ? semesterFilter.value
                    : undefined,
            course_id:
                courseFilter.value !== "all"
                    ? Number(courseFilter.value)
                    : undefined,
            subject_id:
                subjectFilter.value !== "all"
                    ? Number(subjectFilter.value)
                    : undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
            threshold: normalizedThreshold(),
            course_threshold: normalizedCourseThreshold(),
            subject_threshold: normalizedSubjectThreshold(),
            cooldown_days: normalizedCooldownDays(),
            trend_mode: trendMode.value !== "weekly" ? trendMode.value : undefined,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    );
};

const clearReportFilters = () => {
    programmeFilter.value = "all";
    intakeYearFilter.value = "all";
    semesterFilter.value = "all";
    courseFilter.value = "all";
    subjectFilter.value = "all";
    dateFrom.value = "";
    dateTo.value = "";
    thresholdFilter.value = defaultThreshold;
    courseThresholdFilter.value = "";
    subjectThresholdFilter.value = "";
    alertCooldownDays.value = defaultCooldownDays;
    trendMode.value = "weekly";
    applyFilters();
};

const runLowAttendanceAlerts = () => {
    sendingAlerts.value = true;
    router.post(
        route("admin.attendance.alerts.run"),
        {
            threshold: thresholdValue.value,
            cooldown_days: normalizedCooldownDays(),
        },
        {
            preserveScroll: true,
            onFinish: () => {
                sendingAlerts.value = false;
            },
        }
    );
};

const exportUrl = (format) =>
    route("admin.attendance.report.export", {
        format,
        programme:
            programmeFilter.value !== "all" ? programmeFilter.value : undefined,
        intake_year:
            intakeYearFilter.value !== "all"
                ? intakeYearFilter.value
                : undefined,
        semester:
            semesterFilter.value !== "all" ? semesterFilter.value : undefined,
        course_id:
            courseFilter.value !== "all"
                ? Number(courseFilter.value)
                : undefined,
        subject_id:
            subjectFilter.value !== "all"
                ? Number(subjectFilter.value)
                : undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    });

watch(courseFilter, () => {
    if (
        subjectFilter.value !== "all" &&
        !subjectOptions.value.some(
            (subject) => String(subject.id) === String(subjectFilter.value)
        )
    ) {
        subjectFilter.value = "all";
    }
});

watch([programmeFilter, intakeYearFilter, semesterFilter, courseFilter, subjectFilter], () => {
    applyFilters();
});

const trendRows = computed(() =>
    trendMode.value === "monthly"
        ? props.trend?.monthly ?? []
        : props.trend?.weekly ?? []
);

const trendChartData = computed(() => ({
    labels: trendRows.value.map((row) => row.label),
    datasets: [
        {
            label: "Attendance rate",
            data: trendRows.value.map((row) => Number(row.rate ?? 0)),
            borderColor: "#0f766e",
            backgroundColor: "rgba(15, 118, 110, 0.18)",
            fill: true,
            tension: 0.35,
            pointRadius: 3,
        },
    ],
}));

const filteredStudents = computed(() => {
    const q = searchStudents.value.trim().toLowerCase();
    if (!q) return props.lowAttendanceStudents;
    return props.lowAttendanceStudents.filter((s) => {
        const hay = `${s.student_no} ${s.full_name} ${s.email} ${s.programme ?? ""}`.toLowerCase();
        return hay.includes(q);
    });
});

const filteredCourses = computed(() => {
    const q = searchCourses.value.trim().toLowerCase();
    if (!q) return props.byCourse;
    return props.byCourse.filter((c) => {
        const hay = `${c.course_code} ${c.title}`.toLowerCase();
        return hay.includes(q);
    });
});

const filteredSubjects = computed(() => {
    const q = searchSubjects.value.trim().toLowerCase();
    if (!q) return props.bySubject;
    return props.bySubject.filter((s) => {
        const hay = `${s.subject_code} ${s.title} ${s.course_code}`.toLowerCase();
        return hay.includes(q);
    });
});

const filteredRecent = computed(() => {
    const q = searchRecent.value.trim().toLowerCase();
    const recentRows = props.recentRecords?.data ?? [];
    if (!q) return recentRows;
    return recentRows.filter((r) => {
        const hay = `${r.student_no} ${r.student_name} ${r.subject_code} ${r.course_code} ${r.date}`.toLowerCase();
        return hay.includes(q);
    });
});
</script>

<template>
    <Head title="Attendance Report" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Attendance Report
                </h2>
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        :href="exportUrl('csv')"
                        class="inline-flex items-center rounded-md border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 shadow-sm transition-colors hover:bg-emerald-100"
                    >
                        Export CSV
                    </a>
                    <a
                        :href="exportUrl('pdf')"
                        class="inline-flex items-center rounded-md border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm transition-colors hover:bg-blue-100"
                    >
                        Export PDF
                    </a>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                        :disabled="sendingAlerts"
                        @click="runLowAttendanceAlerts"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            />
                        </svg>
                        <span>{{ sendingAlerts ? "Queueing..." : "Send Low Attendance Alerts" }}</span>
                    </button>
                </div>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Attendance Report' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Report Filters
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Narrow analytics by cohort, date range, risk threshold, and trend mode.
                            </p>
                        </div>
                        <div class="grid w-full gap-2 sm:grid-cols-2 lg:grid-cols-5">
                            <select
                                v-model="programmeFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All programmes</option>
                                <option
                                    v-for="programme in options.programmes"
                                    :key="programme"
                                    :value="programme"
                                >
                                    {{ programme }}
                                </option>
                            </select>
                            <select
                                v-model="intakeYearFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All intake years</option>
                                <option
                                    v-for="year in options.intakeYears"
                                    :key="year"
                                    :value="String(year)"
                                >
                                    {{ year }}
                                </option>
                            </select>
                            <select
                                v-model="semesterFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All semesters</option>
                                <option
                                    v-for="semester in options.semesters"
                                    :key="semester"
                                    :value="semester"
                                >
                                    {{ semester }}
                                </option>
                            </select>
                            <select
                                v-model="courseFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All courses</option>
                                <option
                                    v-for="course in options.courses"
                                    :key="course.id"
                                    :value="String(course.id)"
                                >
                                    {{ course.course_code }} - {{ course.title }}
                                </option>
                            </select>
                            <select
                                v-model="subjectFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All subjects</option>
                                <option
                                    v-for="subject in subjectOptions"
                                    :key="subject.id"
                                    :value="String(subject.id)"
                                >
                                    {{ subject.subject_code }} - {{ subject.title }}
                                </option>
                            </select>
                            <input
                                v-model="dateFrom"
                                type="date"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                title="Date from"
                            />
                            <input
                                v-model="dateTo"
                                type="date"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                title="Date to"
                            />
                            <input
                                v-model.number="thresholdFilter"
                                type="number"
                                min="1"
                                max="100"
                                step="0.1"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                placeholder="Low-attendance threshold %"
                            />
                            <input
                                v-model.number="courseThresholdFilter"
                                type="number"
                                min="1"
                                max="100"
                                step="0.1"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                placeholder="Course threshold % (optional)"
                            />
                            <input
                                v-model.number="subjectThresholdFilter"
                                type="number"
                                min="1"
                                max="100"
                                step="0.1"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                placeholder="Subject threshold % (optional)"
                            />
                            <input
                                v-model.number="alertCooldownDays"
                                type="number"
                                min="0"
                                max="90"
                                step="1"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                placeholder="Alert cooldown days"
                            />
                            <select
                                v-model="trendMode"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="weekly">Weekly trend (12 weeks)</option>
                                <option value="monthly">Monthly trend (6 months)</option>
                            </select>
                            <button
                                type="button"
                                class="rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                                @click="applyFilters"
                            >
                                Apply
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                        <p>
                            Active threshold: <span class="font-semibold text-slate-700">{{ thresholdLabel }}%</span>
                            (<span class="font-semibold text-slate-700">{{ thresholdScopeLabel }}</span>)
                            | Global threshold: <span class="font-semibold text-slate-700">{{ Number(normalizedThreshold() ?? defaultThreshold).toFixed(1) }}%</span>
                            | Alert cooldown: <span class="font-semibold text-slate-700">{{ cooldownLabel }} day(s)</span>
                        </p>
                        <button
                            type="button"
                            class="rounded-md border border-slate-300 bg-white px-3 py-1.5 font-semibold text-slate-700 hover:bg-slate-50"
                            @click="clearReportFilters"
                        >
                            Clear filters
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <DashboardChart
                        type="line"
                        variant="staff"
                        :title="trendMode === 'monthly' ? 'Attendance Trend (Monthly)' : 'Attendance Trend (Weekly)'"
                        :chart-data="trendChartData"
                        :y-max="100"
                        value-format="percent"
                        :decimals="1"
                        :height="280"
                    />
                    <p class="mt-2 text-xs text-slate-500">
                        {{ trendMode === "monthly" ? "Last 6 months" : "Last 12 weeks" }} present rate after current filters.
                    </p>
                </div>

                <!-- Overall Statistics -->
                <div class="mb-6 grid gap-4 md:grid-cols-4">
                    <div class="portal-card p-5">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Total Records
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ overall.total }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                        >
                            Present
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ overall.present }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-red-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-red-700"
                        >
                            Absent
                        </p>
                        <p class="mt-2 text-2xl font-bold text-red-900">
                            {{ overall.absent }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-indigo-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-indigo-700"
                        >
                            Attendance Rate
                        </p>
                        <p class="mt-2 text-2xl font-bold text-indigo-900">
                            {{ overall.rate }}%
                        </p>
                    </div>
                </div>

                <!-- Students with Low Attendance -->
                <div class="mb-6 portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-slate-900">
                                Students with Low Attendance (&lt; {{ thresholdLabel }}% {{ thresholdScopeLabel }} threshold)
                            </h3>
                            <p class="mt-1 text-sm text-slate-600">
                                These students may require attention or intervention.
                            </p>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div class="w-full sm:w-80">
                                <label class="block text-xs font-medium text-slate-600">Search</label>
                                <input
                                    v-model="searchStudents"
                                    type="search"
                                    placeholder="Search by student no, name, programme…"
                                    class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                            </div>
                            <div class="flex-shrink-0">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                                    :disabled="sendingAlerts"
                                    @click="runLowAttendanceAlerts"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                        />
                                    </svg>
                                    <span>{{ sendingAlerts ? "Queueing..." : "Send Alerts" }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Student No
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Name
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Programme
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Present
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Absent
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Rate
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Risk Reason
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-for="student in filteredStudents"
                                    :key="student.id"
                                    class="hover:bg-slate-50"
                                >
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900">
                                        {{ student.student_no }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ student.full_name }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ student.programme || "N/A" }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-emerald-600">
                                        {{ student.present }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-red-600">
                                        {{ student.absent }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm">
                                        <span
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                            :class="{
                                                'bg-red-100 text-red-800':
                                                    student.rate < thresholdValue - 10,
                                                'bg-amber-100 text-amber-800':
                                                    student.rate >= thresholdValue - 10 &&
                                                    student.rate < thresholdValue,
                                            }"
                                        >
                                            {{ student.rate }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-600">
                                        {{ student.reason || "Below threshold" }}
                                    </td>
                                </tr>
                                <tr v-if="filteredStudents.length === 0 && lowAttendanceStudents.length > 0">
                                    <td
                                        colspan="7"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No students match your search.
                                    </td>
                                </tr>
                                <tr v-if="lowAttendanceStudents.length === 0">
                                    <td
                                        colspan="7"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        <div class="flex flex-col items-center gap-3">
                                            <svg
                                                class="h-12 w-12 text-emerald-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            <div>
                                                <p class="font-semibold text-slate-700">
                                                    Great news!
                                                </p>
                                                <p class="mt-1">
                                                    All students are meeting the attendance threshold (>= {{ thresholdLabel }}% {{ thresholdScopeLabel }} threshold).
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-if="lowAttendanceStudents.length === 0"
                        class="mt-4 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-800"
                    >
                        <div class="flex items-start gap-3">
                            <svg
                                class="h-5 w-5 mt-0.5 flex-shrink-0"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <div>
                                <p class="font-semibold">No action needed</p>
                                <p class="mt-1 text-emerald-700">
                                    All students are maintaining good attendance. The automated alert system will notify students if their attendance drops below {{ thresholdLabel }}%.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance by Course -->
                <div class="mb-6 portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">
                            Attendance by Course
                        </h3>
                        <div class="w-full sm:w-80">
                            <label class="block text-xs font-medium text-slate-600">Search</label>
                            <input
                                v-model="searchCourses"
                                type="search"
                                placeholder="Search by course code, title…"
                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Course Code
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Title
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Total
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Present
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Absent
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Rate
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-for="course in filteredCourses"
                                    :key="course.id"
                                    class="hover:bg-slate-50"
                                >
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900">
                                        {{ course.course_code }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ course.title }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-slate-600">
                                        {{ course.total }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-emerald-600">
                                        {{ course.present }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-red-600">
                                        {{ course.absent }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm">
                                        <span
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                            :class="{
                                                'bg-emerald-100 text-emerald-800':
                                                    course.rate >= 80,
                                                'bg-amber-100 text-amber-800':
                                                    course.rate >= 60 &&
                                                    course.rate < 80,
                                                'bg-red-100 text-red-800':
                                                    course.rate < 60,
                                            }"
                                        >
                                            {{ course.rate }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="filteredCourses.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{ searchCourses.trim() ? "No courses match your search." : "No attendance records found for any courses." }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Attendance by Subject -->
                <div class="mb-6 portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">
                            Attendance by Subject
                        </h3>
                        <div class="w-full sm:w-80">
                            <label class="block text-xs font-medium text-slate-600">Search</label>
                            <input
                                v-model="searchSubjects"
                                type="search"
                                placeholder="Search by subject code, title, course…"
                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Subject Code
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Title
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Course
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Total
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Present
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Absent
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Rate
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-for="subject in filteredSubjects"
                                    :key="subject.id"
                                    class="hover:bg-slate-50"
                                >
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900">
                                        {{ subject.subject_code }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ subject.title }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">
                                        {{ subject.course_code }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-slate-600">
                                        {{ subject.total }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-emerald-600">
                                        {{ subject.present }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-red-600">
                                        {{ subject.absent }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm">
                                        <span
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                            :class="{
                                                'bg-emerald-100 text-emerald-800':
                                                    subject.rate >= 80,
                                                'bg-amber-100 text-amber-800':
                                                    subject.rate >= 60 &&
                                                    subject.rate < 80,
                                                'bg-red-100 text-red-800':
                                                    subject.rate < 60,
                                            }"
                                        >
                                            {{ subject.rate }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="filteredSubjects.length === 0">
                                    <td
                                        colspan="7"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{ searchSubjects.trim() ? "No subjects match your search." : "No attendance records found for any subjects." }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Attendance Records -->
                <div class="portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">
                            Recent Attendance Records (Last 30 Days)
                        </h3>
                        <div class="w-full sm:w-80">
                            <label class="block text-xs font-medium text-slate-600">Search</label>
                            <input
                                v-model="searchRecent"
                                type="search"
                                placeholder="Search by student, subject, course, date…"
                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Student
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Subject
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Course
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-for="record in filteredRecent"
                                    :key="record.id"
                                    class="hover:bg-slate-50"
                                >
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">
                                        {{ record.date }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ record.student_name }} ({{ record.student_no }})
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ record.subject_code }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ record.course_code }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm">
                                        <span
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize"
                                            :class="{
                                                'bg-emerald-100 text-emerald-800':
                                                    record.status === 'present',
                                                'bg-red-100 text-red-800':
                                                    record.status === 'absent',
                                            }"
                                        >
                                            {{ record.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="filteredRecent.length === 0">
                                    <td
                                        colspan="5"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{ searchRecent.trim() ? "No records match your search." : "No recent attendance records found." }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <Pagination :links="recentRecords.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
