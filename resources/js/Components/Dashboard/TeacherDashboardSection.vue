<script setup>
import { Link } from "@inertiajs/vue3";
import { defineAsyncComponent } from "vue";

const DashboardChart = defineAsyncComponent(() =>
    import("@/Components/Dashboard/DashboardChart.vue")
);

defineProps({
    role: {
        type: String,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
    charts: {
        type: Object,
        default: () => ({}),
    },
    insights: {
        type: Object,
        default: () => ({}),
    },
    unread: {
        type: Object,
        default: () => ({
            messages: 0,
            notifications: 0,
            announcements: 0,
        }),
    },
    quickActions: {
        type: Array,
        default: () => [],
    },
    alertSystemStatus: {
        type: Object,
        default: null,
    },
    formatCurrency: {
        type: Function,
        required: true,
    },
    formatTrendPercent: {
        type: Function,
        required: true,
    },
    trendBadgeClass: {
        type: Function,
        required: true,
    },
    hasChartData: {
        type: Function,
        required: true,
    },
    onStudentFeeStatusClick: {
        type: Function,
        default: () => {},
    },
    onStaffFeeStatusClick: {
        type: Function,
        default: () => {},
    },
    onStaffEnrollmentStatusClick: {
        type: Function,
        default: () => {},
    },
    onTeacherGradeStatusClick: {
        type: Function,
        default: () => {},
    },
    onTeacherAssignmentsChartClick: {
        type: Function,
        default: () => {},
    },
});
</script>

<template>
    <div class="space-y-6">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div
                        class="rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-white p-4"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-indigo-700"
                            >
                                Needs grading
                            </p>
                            <span
                                class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700"
                            >
                                {{ insights.needsGradingSubmissions ?? 0 }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-indigo-900">
                            {{ insights.gradedSubmissions ?? 0 }} submissions graded
                        </p>
                        <Link
                            :href="route('teacher.assignments.index')"
                            class="mt-3 inline-flex text-xs font-semibold text-indigo-700 hover:underline"
                        >
                            Open assignments
                        </Link>
                    </div>

                    <div
                        class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-4"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                            >
                                Pending grades trend
                            </p>
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="trendBadgeClass(insights.pendingGradesTrend?.direction, 'staff')"
                            >
                                {{ formatTrendPercent(insights.pendingGradesTrend?.percent) }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-emerald-900">
                            {{ insights.pendingGradesTrend?.current ?? 0 }}
                            {{ insights.pendingGradesTrend?.currentLabel ?? "this week" }}
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">
                            Attendance trend
                            {{ formatTrendPercent(insights.attendanceTrend?.percent) }}
                            ({{ insights.attendanceTrend?.current ?? 0 }}%)
                        </p>
                        <Link
                            :href="route('teacher.grades.index')"
                            class="mt-3 inline-flex text-xs font-semibold text-emerald-700 hover:underline"
                        >
                            Open grading
                        </Link>
                    </div>

                    <div
                        class="rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-4"
                    >
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-amber-700"
                        >
                            At-risk students
                        </p>
                        <p
                            v-if="!(insights.atRiskStudents?.length > 0)"
                            class="mt-2 text-sm text-amber-900"
                        >
                            No students below threshold in current sample.
                        </p>
                        <div v-else class="mt-2 space-y-2">
                            <div
                                v-for="student in insights.atRiskStudents"
                                :key="student.student_id"
                                class="rounded-lg border border-amber-200 bg-white px-2.5 py-2"
                            >
                                <p class="text-xs font-semibold text-amber-900">
                                    {{ student.student_no }} - {{ student.name }}
                                </p>
                                <p class="mt-0.5 text-[11px] text-amber-700">
                                    {{ student.attendanceRate }}% ({{ student.reason }})
                                </p>
                            </div>
                        </div>
                        <Link
                            :href="route('teacher.attendance.index')"
                            class="mt-3 inline-flex text-xs font-semibold text-amber-700 hover:underline"
                        >
                            Open attendance
                        </Link>
                    </div>
                </div>

                <!-- Pending Grades Alert for Teachers -->
                <div
                    v-if="stats.pendingGrades > 0"
                    class="group relative overflow-hidden rounded-xl border-2 border-indigo-300 bg-gradient-to-br from-indigo-50 to-indigo-100 p-6 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                >
                    <Link
                        :href="route('admin.grades.index')"
                        class="flex items-center justify-between"
                    >
                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wider text-indigo-800"
                            >
                                 Action Required
                            </p>
                            <p class="mt-2 text-3xl font-bold text-indigo-900">
                                {{ stats.pendingGrades ?? 0 }}
                            </p>
                            <p class="mt-1 text-sm font-medium text-indigo-700">
                                Grades pending staff review
                            </p>
                            <p class="mt-2 text-xs text-indigo-600">
                                Click to view and track approval status
                            </p>
                        </div>
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-amber-500 shadow-lg"
                        >
                            <svg
                                class="h-8 w-8 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                />
                            </svg>
                        </div>
                    </Link>
                    <div
                        class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-200"
                    >
                        <div
                            class="h-full bg-indigo-500 transition-all duration-500"
                            :style="{
                                width: `${Math.min(
                                    (stats.pendingGrades / 30) * 100,
                                    100
                                )}%`,
                            }"
                        ></div>
                    </div>
                </div>

                <!-- Dashboard charts - Teacher -->
                <div
                    v-if="
                        hasChartData(charts.gradeStatus) ||
                        hasChartData(charts.gradesBySubject) ||
                        hasChartData(charts.attendanceLine) ||
                        hasChartData(charts.assignmentsBySubject) ||
                        hasChartData(charts.attendanceStatus) ||
                        hasChartData(charts.scoreDistribution)
                    "
                    class="space-y-6 rounded-2xl border border-indigo-100 border-l-4 border-l-indigo-500 bg-gradient-to-b from-indigo-50/50 to-white p-6 shadow-sm ring-1 ring-slate-900/5"
                >
                    <div
                        class="flex items-start gap-4 rounded-xl border border-indigo-200/60 bg-white px-5 py-4 shadow-sm"
                    >
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600"
                            aria-hidden="true"
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
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p
                                class="text-[11px] font-semibold uppercase tracking-[0.12em] text-indigo-700"
                            >
                                Teaching analytics
                            </p>
                            <p class="mt-1 text-sm font-medium text-indigo-900">
                        Class performance and engagement indicators for your subjects.
                    </p>
                        </div>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <DashboardChart
                        v-if="hasChartData(charts.gradeStatus)"
                        type="doughnut"
                        :chart-data="charts.gradeStatus"
                        title="Grade status (my subjects)"
                        :variant="role"
                        :interactive="true"
                        @chart-click="onTeacherGradeStatusClick"
                    />
                    <DashboardChart
                        v-if="hasChartData(charts.gradesBySubject)"
                        type="bar"
                        :chart-data="charts.gradesBySubject"
                        title="Grades by subject (top 8)"
                        :variant="role"
                    />
                    <DashboardChart
                        v-if="hasChartData(charts.attendanceLine)"
                        type="line"
                        :chart-data="charts.attendanceLine"
                        title="Attendance % (last 6 months)"
                        :y-max="100"
                        :variant="role"
                        value-format="percent"
                        :decimals="1"
                    />
                    <DashboardChart
                        v-if="hasChartData(charts.assignmentsBySubject)"
                        type="bar"
                        :chart-data="charts.assignmentsBySubject"
                        title="Assignments by subject"
                        :variant="role"
                        :interactive="true"
                        @chart-click="onTeacherAssignmentsChartClick"
                    />
                    </div>

                    <details
                        v-if="
                            hasChartData(charts.attendanceStatus) ||
                            hasChartData(charts.scoreDistribution)
                        "
                        class="rounded-xl border border-indigo-200/70 bg-white/90 p-4"
                    >
                        <summary
                            class="cursor-pointer text-xs font-semibold uppercase tracking-wide text-indigo-700"
                        >
                            More analytics
                        </summary>
                        <div class="mt-4 grid gap-5 md:grid-cols-2">
                            <DashboardChart
                                v-if="hasChartData(charts.attendanceStatus)"
                                type="doughnut"
                                :chart-data="charts.attendanceStatus"
                                title="Attendance status split"
                                :variant="role"
                            />
                            <DashboardChart
                                v-if="hasChartData(charts.scoreDistribution)"
                                type="bar"
                                :chart-data="charts.scoreDistribution"
                                title="Score distribution (approved grades)"
                                :variant="role"
                            />
                        </div>
                    </details>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="portal-card p-6 lg:col-span-2">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="portal-badge-no-margin mb-4">
                                    Teaching dashboard
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3"
                        >
                            <!-- Enhanced Visual Cards -->
                            <div
                                class="group relative overflow-hidden rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 transition-all duration-300 hover:shadow-lg"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-xs font-bold uppercase tracking-wider text-emerald-700"
                                        >
                                            My Subjects
                                        </p>
                                        <p
                                            class="mt-2 text-3xl font-bold text-emerald-900"
                                        >
                                            {{ stats.teachingSubjects ?? 0 }}
                                        </p>
                                        <p
                                            class="mt-1 text-xs font-medium text-emerald-600"
                                        >
                                            Assigned subjects
                                        </p>
                                    </div>
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500 shadow-md"
                                    >
                                        <svg
                                            class="h-6 w-6 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="group relative overflow-hidden rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 transition-all duration-300 hover:shadow-lg"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-xs font-bold uppercase tracking-wider text-indigo-700"
                                        >
                                            Students Taught
                                        </p>
                                        <p
                                            class="mt-2 text-3xl font-bold text-indigo-900"
                                        >
                                            {{ stats.studentsTaught ?? 0 }}
                                        </p>
                                        <p
                                            class="mt-1 text-xs font-medium text-indigo-600"
                                        >
                                            Unique students
                                        </p>
                                    </div>
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-500 shadow-md"
                                    >
                                        <svg
                                            class="h-6 w-6 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="group relative overflow-hidden rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 transition-all duration-300 hover:shadow-lg"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-xs font-bold uppercase tracking-wider text-indigo-700"
                                        >
                                            Grades Recorded
                                        </p>
                                        <p
                                            class="mt-2 text-3xl font-bold text-indigo-900"
                                        >
                                            {{ stats.gradesRecorded ?? 0 }}
                                        </p>
                                        <div
                                            class="mt-2 flex items-center gap-2 text-xs"
                                        >
                                            <span
                                                class="font-semibold text-emerald-600"
                                            >
                                                Approved:
                                                {{ stats.approvedGrades ?? 0 }}
                                            </span>
                                            <span
                                                v-if="stats.pendingGrades > 0"
                                                class="font-semibold text-amber-600"
                                            >
                                                Pending:
                                                {{ stats.pendingGrades }}
                                            </span>
                                        </div>
                                    </div>
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-500 shadow-md"
                                    >
                                        <svg
                                            class="h-6 w-6 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Action Cards -->
                            <Link
                                :href="route('teacher.timetable.index')"
                                class="group relative overflow-hidden rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-4 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                            >
                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-blue-700"
                                >
                                    My Timetable
                                </p>
                                <p
                                    class="mt-2 text-xs font-medium text-blue-600"
                                >
                                    View your teaching schedule and class
                                    locations.
                                </p>
                                <div
                                    class="mt-3 flex items-center gap-2 text-xs font-semibold text-blue-700"
                                >
                                    <span>View schedule</span>
                                    <span>-></span>
                                </div>
                            </Link>

                            <Link
                                :href="route('teacher.attendance.index')"
                                class="group relative overflow-hidden rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                            >
                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-indigo-700"
                                >
                                    Attendance Mgmt
                                </p>
                                <p
                                    class="mt-2 text-xs font-medium text-indigo-600"
                                >
                                    Record and review student attendance.
                                </p>
                                <div class="mt-3">
                                    <div
                                        class="mb-1 flex items-center justify-between text-xs"
                                    >
                                        <span
                                            class="font-semibold text-indigo-700"
                                            >Rate</span
                                        >
                                        <span class="font-bold text-indigo-900"
                                            >{{
                                                stats.attendanceRate ?? 0
                                            }}%</span
                                        >
                                    </div>
                                    <div
                                        class="h-1.5 overflow-hidden rounded-full bg-indigo-200"
                                    >
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600 transition-all duration-500"
                                            :style="{
                                                width: `${
                                                    stats.attendanceRate ?? 0
                                                }%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('messages.index')"
                                class="group relative overflow-hidden rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                            >
                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-indigo-700"
                                >
                                    Communication
                                </p>
                                <p
                                    class="mt-2 text-xs font-medium text-indigo-600"
                                >
                                    Send messages to students and view alerts.
                                </p>
                                <div
                                    class="mt-3 flex items-center gap-2 text-xs font-semibold text-indigo-700"
                                >
                                    <span>Open messages</span>
                                    <span>-></span>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Quick Actions -->
                        <div class="portal-card p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Quick Actions
                            </p>
                            <div class="mt-3 grid gap-2">
                                <Link
                                    v-for="a in quickActions"
                                    :key="a.href"
                                    :href="a.href"
                                    class="group flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[13px] font-medium text-slate-700 transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50"
                                >
                                    <span>{{ a.label }}</span>
                                    <span class="text-slate-400 transition-colors duration-200 group-hover:text-slate-600">></span>
                                </Link>
                            </div>
                        </div>

                        <!-- Teacher notifications overview -->
                        <div class="portal-card p-5">
                            <div
                                class="flex items-center justify-between gap-3"
                            >
                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                    >
                                        Notifications
                                    </p>
                                    <p class="mt-1 text-sm text-slate-700">
                                        Stay informed about class changes and
                                        messages.
                                    </p>
                                </div>
                                <Link
                                    :href="route('notifications.index')"
                                    class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Open inbox
                                </Link>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-lg bg-slate-50 p-3">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                    >
                                        Portal alerts
                                    </p>
                                    <p
                                        class="mt-1 text-xl font-semibold text-slate-900"
                                    >
                                        {{ unread.notifications ?? 0 }}
                                    </p>
                                </div>
                                <div class="rounded-lg bg-slate-50 p-3">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                    >
                                        Messages
                                    </p>
                                    <p
                                        class="mt-1 text-xl font-semibold text-slate-900"
                                    >
                                        {{ unread.messages ?? 0 }}
                                    </p>
                                </div>
                                <div class="rounded-lg bg-slate-50 p-3">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                    >
                                        Announcements
                                    </p>
                                    <p
                                        class="mt-1 text-xl font-semibold text-slate-900"
                                    >
                                        {{ unread.announcements ?? 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teacher announcements & project context -->
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="portal-card p-5 lg:col-span-2">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Announcements
                                </p>
                                <p class="mt-1 text-sm text-slate-700">
                                    <span
                                        v-if="unread.announcements > 0"
                                        class="font-semibold"
                                    >
                                        {{ unread.announcements }}
                                        unread
                                    </span>
                                    <span v-else class="text-slate-500">
                                        You're all caught up
                                    </span>
                                </p>
                            </div>
                            <Link
                                :href="route('announcements.index')"
                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                            >
                                View all
                            </Link>
                        </div>

                        <div class="mt-4 space-y-2">
                            <div
                                v-if="
                                    !$page.props.announcementsWidget?.latest
                                        ?.length
                                "
                                class="rounded-lg bg-slate-50 p-3 text-xs text-slate-500"
                            >
                                No announcements to show.
                            </div>

                            <div
                                v-for="a in $page.props.announcementsWidget
                                    ?.latest"
                                :key="a.id"
                                class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-3"
                            >
                                <div
                                    class="mt-0.5 h-2.5 w-2.5 rounded-full"
                                    :class="{
                                        'bg-red-500': a.priority === 'urgent',
                                        'bg-amber-500':
                                            a.priority === 'important',
                                        'bg-blue-500': a.priority === 'info',
                                    }"
                                />
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="truncate text-sm font-medium text-slate-900"
                                    >
                                        <span v-if="a.pinned">[Pinned] </span>
                                        {{ a.title }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ a.author }} | {{ a.created_at }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="portal-card p-5">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Project context
                        </p>
                        <p class="mt-2 text-sm text-slate-700">
                            BSc (Hons) Computing final year project - University
                            Academic Portal using Vue.js and Laravel.
                        </p>
                    </div>
                </div>
            </div>
</template>
