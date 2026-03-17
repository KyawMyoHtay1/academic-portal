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
                <div class="grid gap-4 md:grid-cols-3">
                    <div
                        class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-4"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                            >
                                Fees collected
                            </p>
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="trendBadgeClass(insights.feesCollectedTrend?.direction, 'staff')"
                            >
                                {{ formatTrendPercent(insights.feesCollectedTrend?.percent) }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-emerald-900">
                            {{ formatCurrency(insights.feesCollectedTrend?.current) }}
                            {{ insights.feesCollectedTrend?.currentLabel ?? "this month" }}
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">
                            vs {{ formatCurrency(insights.feesCollectedTrend?.previous) }}
                            {{ insights.feesCollectedTrend?.previousLabel ?? "last month" }}
                        </p>
                        <Link
                            :href="route('admin.fees.index')"
                            class="mt-3 inline-flex text-xs font-semibold text-emerald-700 hover:underline"
                        >
                            Open fee management
                        </Link>
                    </div>

                    <div
                        class="rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-white p-4"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-indigo-700"
                            >
                                Pending grades trend
                            </p>
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="trendBadgeClass(insights.pendingGradesTrend?.direction, 'teacher')"
                            >
                                {{ formatTrendPercent(insights.pendingGradesTrend?.percent) }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-indigo-900">
                            {{ insights.pendingGradesTrend?.current ?? 0 }}
                            {{ insights.pendingGradesTrend?.currentLabel ?? "this week" }}
                        </p>
                        <p class="mt-1 text-xs text-indigo-700">
                            vs {{ insights.pendingGradesTrend?.previous ?? 0 }}
                            {{ insights.pendingGradesTrend?.previousLabel ?? "last week" }}
                        </p>
                        <Link
                            :href="route('admin.grades.index')"
                            class="mt-3 inline-flex text-xs font-semibold text-indigo-700 hover:underline"
                        >
                            Open grade review
                        </Link>
                    </div>

                    <div
                        class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-4"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-blue-700"
                            >
                                Enrollment approvals
                            </p>
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="trendBadgeClass(insights.approvalsTrend?.direction, 'student')"
                            >
                                {{ formatTrendPercent(insights.approvalsTrend?.percent) }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-blue-900">
                            {{ insights.approvalsTrend?.current ?? 0 }}
                            {{ insights.approvalsTrend?.currentLabel ?? "this week" }}
                        </p>
                        <p class="mt-1 text-xs text-blue-700">
                            vs {{ insights.approvalsTrend?.previous ?? 0 }}
                            {{ insights.approvalsTrend?.previousLabel ?? "last week" }}
                        </p>
                        <Link
                            :href="route('admin.enrollments.index')"
                            class="mt-3 inline-flex text-xs font-semibold text-blue-700 hover:underline"
                        >
                            Open enrollments
                        </Link>
                    </div>
                </div>

                <!-- Pending Actions Alert Cards -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Link
                        v-if="stats.pendingEnrollments > 0"
                        :href="route('admin.enrollments.index')"
                        class="group relative overflow-hidden rounded-xl border-2 border-amber-300 bg-gradient-to-br from-amber-50 to-amber-100 p-5 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-amber-800"
                                >
                                    Pending Enrollments
                                </p>
                                <p
                                    class="mt-2 text-3xl font-bold text-amber-900"
                                >
                                    {{ stats.pendingEnrollments ?? 0 }}
                                </p>
                                <p
                                    class="mt-1 text-xs font-medium text-amber-700"
                                >
                                    Require approval
                                </p>
                            </div>
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-500 shadow-lg"
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
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-amber-200"
                        >
                            <div
                                class="h-full bg-amber-500 transition-all duration-500"
                                :style="{
                                    width: `${Math.min(
                                        (stats.pendingEnrollments / 10) * 100,
                                        100
                                    )}%`,
                                }"
                            ></div>
                        </div>
                    </Link>

                    <Link
                        v-if="stats.pendingGrades > 0"
                        :href="route('admin.grades.index')"
                        class="group relative overflow-hidden rounded-xl border-2 border-indigo-300 bg-gradient-to-br from-indigo-50 to-indigo-100 p-5 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-indigo-800"
                                >
                                    Pending Grades
                                </p>
                                <p
                                    class="mt-2 text-3xl font-bold text-indigo-900"
                                >
                                    {{ stats.pendingGrades ?? 0 }}
                                </p>
                                <p
                                    class="mt-1 text-xs font-medium text-indigo-700"
                                >
                                    Awaiting review
                                </p>
                            </div>
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-500 shadow-lg"
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
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-200"
                        >
                            <div
                                class="h-full bg-indigo-500 transition-all duration-500"
                                :style="{
                                    width: `${Math.min(
                                        (stats.pendingGrades / 20) * 100,
                                        100
                                    )}%`,
                                }"
                            ></div>
                        </div>
                    </Link>

                    <Link
                        v-if="stats.pendingPayments > 0"
                        :href="route('admin.fees.index')"
                        class="group relative overflow-hidden rounded-xl border-2 border-emerald-300 bg-gradient-to-br from-emerald-50 to-emerald-100 p-5 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-emerald-800"
                                >
                                    Pending Payments
                                </p>
                                <p
                                    class="mt-2 text-3xl font-bold text-emerald-900"
                                >
                                    {{ stats.pendingPayments ?? 0 }}
                                </p>
                                <p
                                    class="mt-1 text-xs font-medium text-emerald-700"
                                >
                                    Need confirmation
                                </p>
                            </div>
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500 shadow-lg"
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
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-200"
                        >
                            <div
                                class="h-full bg-emerald-500 transition-all duration-500"
                                :style="{
                                    width: `${Math.min(
                                        (stats.pendingPayments / 15) * 100,
                                        100
                                    )}%`,
                                }"
                            ></div>
                        </div>
                    </Link>

                    <div
                        v-if="stats.pendingWithdrawals > 0"
                        class="group relative overflow-hidden rounded-xl border-2 border-red-300 bg-gradient-to-br from-red-50 to-red-100 p-5"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-red-800"
                                >
                                    Pending Withdrawals
                                </p>
                                <p class="mt-2 text-3xl font-bold text-red-900">
                                    {{ stats.pendingWithdrawals ?? 0 }}
                                </p>
                                <p
                                    class="mt-1 text-xs font-medium text-red-700"
                                >
                                    Awaiting approval
                                </p>
                            </div>
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-red-500 shadow-lg"
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
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-red-200"
                        >
                            <div
                                class="h-full bg-red-500 transition-all duration-500"
                                :style="{
                                    width: `${Math.min(
                                        (stats.pendingWithdrawals / 10) * 100,
                                        100
                                    )}%`,
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard charts - Staff -->
                <div
                    v-if="
                        hasChartData(charts.feeStatus) ||
                        hasChartData(charts.enrollmentsByCourse) ||
                        hasChartData(charts.feesCollectedLine) ||
                        hasChartData(charts.gradeStatus) ||
                        hasChartData(charts.attendanceStatus) ||
                        hasChartData(charts.enrollmentStatus)
                    "
                    class="space-y-6 rounded-2xl border border-emerald-100 border-l-4 border-l-emerald-500 bg-gradient-to-b from-emerald-50/50 to-white p-6 shadow-sm ring-1 ring-slate-900/5"
                >
                    <div
                        class="flex items-start gap-4 rounded-xl border border-emerald-200/60 bg-white px-5 py-4 shadow-sm"
                    >
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600"
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
                                class="text-[11px] font-semibold uppercase tracking-[0.12em] text-emerald-700"
                            >
                                Operations analytics
                            </p>
                            <p class="mt-1 text-sm font-medium text-emerald-900">
                                Institution-wide snapshots for review, enrollment, and finance.
                            </p>
                            <p class="mt-1 text-xs text-emerald-700">
                                Tip: click a fee status slice to open filtered fee records.
                            </p>
                        </div>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <DashboardChart
                        v-if="hasChartData(charts.feeStatus)"
                        type="doughnut"
                        :chart-data="charts.feeStatus"
                        title="Fee status (all fees)"
                        :variant="role"
                        :interactive="true"
                        @chart-click="onStaffFeeStatusClick"
                    />
                    <DashboardChart
                        v-if="hasChartData(charts.gradeStatus)"
                        type="doughnut"
                        :chart-data="charts.gradeStatus"
                        title="Grade review status"
                        :variant="role"
                    />
                    <DashboardChart
                        v-if="hasChartData(charts.enrollmentsByCourse)"
                        type="bar"
                        :chart-data="charts.enrollmentsByCourse"
                        title="Enrollments by course (top 8)"
                        :variant="role"
                    />
                    <DashboardChart
                        v-if="hasChartData(charts.feesCollectedLine)"
                        type="line"
                        :chart-data="charts.feesCollectedLine"
                        title="Fees collected (last 6 months)"
                        :variant="role"
                        value-format="currency"
                    />
                    </div>

                    <details
                        v-if="
                            hasChartData(charts.attendanceStatus) ||
                            hasChartData(charts.enrollmentStatus)
                        "
                        class="rounded-xl border border-emerald-200/70 bg-white/90 p-4"
                    >
                        <summary
                            class="cursor-pointer text-xs font-semibold uppercase tracking-wide text-emerald-700"
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
                                v-if="hasChartData(charts.enrollmentStatus)"
                                type="doughnut"
                                :chart-data="charts.enrollmentStatus"
                                title="Enrollment request status"
                                :variant="role"
                                :interactive="true"
                                @chart-click="onStaffEnrollmentStatusClick"
                            />
                        </div>
                    </details>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="portal-card p-6 lg:col-span-2">
                        <div class="flex items-center justify-between gap-4">
                            <p class="portal-badge-no-margin mb-4">
                                Staff dashboard
                            </p>
                        </div>

                        <div
                            class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3"
                        >
                            <!-- Enhanced Visual Action Cards -->
                            <Link
                                :href="route('admin.enrollments.index')"
                                class="group relative overflow-hidden rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 via-blue-100 to-blue-50 p-5 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p
                                            class="text-xs font-bold uppercase tracking-wider text-blue-700"
                                        >
                                            Student & Course Registration
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-medium text-blue-900"
                                        >
                                            Approve enrollments and keep records
                                            accurate.
                                        </p>
                                        <div
                                            v-if="stats.pendingEnrollments > 0"
                                            class="mt-3 inline-flex items-center gap-2 rounded-full bg-blue-500 px-3 py-1 text-xs font-bold text-white"
                                        >
                                            <span>{{
                                                stats.pendingEnrollments
                                            }}</span>
                                            <span>pending</span>
                                        </div>
                                    </div>
                                    <div
                                        class="ml-3 flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-500 shadow-md"
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
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('admin.grades.index')"
                                class="group relative overflow-hidden rounded-xl border-2 border-indigo-200 bg-gradient-to-br from-indigo-50 via-indigo-100 to-indigo-50 p-5 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p
                                            class="text-xs font-bold uppercase tracking-wider text-indigo-700"
                                        >
                                            Grades & Fees
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-medium text-indigo-900"
                                        >
                                            Monitor grade submissions and fee
                                            payments.
                                        </p>
                                        <div
                                            v-if="stats.pendingGrades > 0"
                                            class="mt-3 inline-flex items-center gap-2 rounded-full bg-indigo-500 px-3 py-1 text-xs font-bold text-white"
                                        >
                                            <span>{{
                                                stats.pendingGrades
                                            }}</span>
                                            <span>to review</span>
                                        </div>
                                    </div>
                                    <div
                                        class="ml-3 flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-emerald-600 shadow-md"
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
                            </Link>

                            <Link
                                :href="route('admin.timetables.index')"
                                class="group relative overflow-hidden rounded-xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-50 p-5 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p
                                            class="text-xs font-bold uppercase tracking-wider text-emerald-700"
                                        >
                                            Timetable & Attendance
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-medium text-emerald-900"
                                        >
                                            Coordinate schedules and track
                                            attendance.
                                        </p>
                                        <div
                                            class="mt-3 flex items-center gap-2 text-xs font-semibold text-emerald-700"
                                        >
                                            <span
                                                >{{
                                                    stats.attendanceRate ?? 0
                                                }}%</span
                                            >
                                            <span>overall rate</span>
                                        </div>
                                    </div>
                                    <div
                                        class="ml-3 flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-emerald-500 shadow-md"
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
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('admin.users.index')"
                                class="group relative overflow-hidden rounded-xl border-2 border-indigo-200 bg-gradient-to-br from-indigo-50 via-indigo-100 to-indigo-50 p-5 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p
                                            class="text-xs font-bold uppercase tracking-wider text-indigo-700"
                                        >
                                            User Management
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-medium text-indigo-900"
                                        >
                                            Control access rights and profiles.
                                        </p>
                                    </div>
                                    <div
                                        class="ml-3 flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-500 shadow-md"
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
                            </Link>

                            <Link
                                :href="route('admin.announcements.index')"
                                class="group relative overflow-hidden rounded-xl border-2 border-amber-200 bg-gradient-to-br from-amber-50 via-amber-100 to-amber-50 p-5 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p
                                            class="text-xs font-bold uppercase tracking-wider text-amber-700"
                                        >
                                            Communication Center
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-medium text-amber-900"
                                        >
                                            Manage announcements, messages, and
                                            feedback.
                                        </p>
                                    </div>
                                    <div
                                        class="ml-3 flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-amber-500 shadow-md"
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
                                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </Link>

                            <div
                                class="group relative overflow-hidden rounded-xl border-2 border-slate-200 bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50 p-5"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p
                                            class="text-xs font-bold uppercase tracking-wider text-slate-700"
                                        >
                                            System Health
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-medium text-slate-900"
                                        >
                                            Monitor alerts, queue status, and
                                            logs.
                                        </p>
                                        <div
                                            v-if="alertSystemStatus"
                                            class="mt-3 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold"
                                            :class="{
                                                'bg-emerald-500 text-white':
                                                    alertSystemStatus.status ===
                                                    'ready',
                                                'bg-amber-500 text-white':
                                                    alertSystemStatus.status ===
                                                    'warning',
                                            }"
                                        >
                                            <span
                                                :class="{
                                                    'bg-emerald-400':
                                                        alertSystemStatus.status ===
                                                        'ready',
                                                    'bg-amber-400':
                                                        alertSystemStatus.status ===
                                                        'warning',
                                                }"
                                                class="h-2 w-2 rounded-full"
                                            ></span>
                                            <span>{{
                                                alertSystemStatus.status ===
                                                "ready"
                                                    ? "Active"
                                                    : "Warning"
                                            }}</span>
                                        </div>
                                    </div>
                                    <div
                                        class="ml-3 flex h-12 w-12 shrink-0 items-center justify-center rounded-lg shadow-md"
                                        :class="{
                                            'bg-emerald-600':
                                                alertSystemStatus?.status ===
                                                'ready',
                                            'bg-amber-600':
                                                alertSystemStatus?.status ===
                                                'warning',
                                        }"
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
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Attendance alerts -->
                        <div class="portal-card p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                    >
                                        Attendance alerts
                                    </p>
                                    <p class="mt-1 text-sm text-slate-700">
                                        Manually run the low attendance alert
                                        check. Alerts will be queued and sent in
                                        the background.
                                    </p>

                                    <!-- Status indicator -->
                                    <div
                                        v-if="alertSystemStatus"
                                        class="mt-3 rounded-lg border p-3"
                                        :class="{
                                            'border-emerald-200 bg-emerald-50':
                                                alertSystemStatus.status ===
                                                'ready',
                                            'border-amber-200 bg-amber-50':
                                                alertSystemStatus.status ===
                                                'warning',
                                        }"
                                    >
                                        <div class="flex items-start gap-2">
                                            <svg
                                                v-if="
                                                    alertSystemStatus.status ===
                                                    'ready'
                                                "
                                                class="h-5 w-5 mt-0.5 flex-shrink-0 text-emerald-600"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <svg
                                                v-else
                                                class="h-5 w-5 mt-0.5 flex-shrink-0 text-amber-600"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="text-xs font-semibold"
                                                    :class="{
                                                        'text-emerald-800':
                                                            alertSystemStatus.status ===
                                                            'ready',
                                                        'text-amber-800':
                                                            alertSystemStatus.status ===
                                                            'warning',
                                                    }"
                                                >
                                                    {{
                                                        alertSystemStatus.status ===
                                                        "ready"
                                                            ? "Automatic alerts active"
                                                            : "Setup required"
                                                    }}
                                                </p>
                                                <p
                                                    class="mt-1 text-xs"
                                                    :class="{
                                                        'text-emerald-700':
                                                            alertSystemStatus.status ===
                                                            'ready',
                                                        'text-amber-700':
                                                            alertSystemStatus.status ===
                                                            'warning',
                                                    }"
                                                >
                                                    {{
                                                        alertSystemStatus.message
                                                    }}
                                                </p>
                                                <div
                                                    v-if="
                                                        alertSystemStatus.pendingJobs >
                                                        0
                                                    "
                                                    class="mt-2 text-xs"
                                                    :class="{
                                                        'text-emerald-700':
                                                            alertSystemStatus.pendingJobs <=
                                                            10,
                                                        'text-amber-700':
                                                            alertSystemStatus.pendingJobs >
                                                            10,
                                                    }"
                                                >
                                                    {{
                                                        alertSystemStatus.pendingJobs
                                                    }}
                                                    job(s) in queue
                                                </div>
                                                <div
                                                    v-if="
                                                        alertSystemStatus.failedJobs >
                                                        0
                                                    "
                                                    class="mt-1 text-xs text-red-700"
                                                >
                                                    {{
                                                        alertSystemStatus.failedJobs
                                                    }}
                                                    failed job(s) require attention
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <Link
                                    :href="route('admin.attendance.alerts.run')"
                                    method="post"
                                    as="button"
                                    class="inline-flex items-center rounded-md bg-portal-navy px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Run low attendance alerts
                                </Link>
                            </div>
                        </div>

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

                        <!-- Staff notifications overview -->
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
                                        Stay on top of approvals and system
                                        alerts.
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

                <!-- Staff announcements & context -->
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
                                        v-if="
                                            $page.props.announcementsWidget
                                                ?.unreadCount > 0
                                        "
                                        class="font-semibold"
                                    >
                                        {{
                                            $page.props.announcementsWidget
                                                .unreadCount
                                        }}
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
