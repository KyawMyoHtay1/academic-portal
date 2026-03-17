<script setup>
import { Link } from "@inertiajs/vue3";
import DashboardChart from "@/Components/Dashboard/DashboardChart.vue";

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

<template>            <div v-if="role === 'student'" class="space-y-6">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div
                        class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-4"
                    >
                        <div
                            class="flex items-start justify-between gap-3"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                                >
                                    Fee progress
                                </p>
                                <p class="mt-2 text-sm text-emerald-900">
                                    Paid {{ formatCurrency(insights.feeProgress?.paidAmount) }}
                                    of
                                    {{ formatCurrency(insights.feeProgress?.totalAmount) }}
                                </p>
                            </div>
                            <span
                                class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700"
                            >
                                {{ insights.feeProgress?.paidPercent ?? 0 }}%
                            </span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-emerald-100">
                            <div
                                class="h-full rounded-full bg-emerald-500"
                                :style="{
                                    width: `${insights.feeProgress?.paidPercent ?? 0}%`,
                                }"
                            ></div>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-xs text-emerald-800">
                            <span>Outstanding {{ formatCurrency(insights.feeProgress?.outstandingAmount) }}</span>
                            <Link :href="route('student.fees.index')" class="font-semibold hover:underline">
                                Open fees
                            </Link>
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-4"
                    >
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-blue-700"
                        >
                            Attendance trend
                        </p>
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-sm font-medium text-blue-900">
                                {{ insights.attendanceTrend?.currentLabel ?? 'This month' }}
                            </p>
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="trendBadgeClass(insights.attendanceTrend?.direction, 'student')"
                            >
                                {{ formatTrendPercent(insights.attendanceTrend?.percent) }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-blue-700">
                            {{ insights.attendanceTrend?.current ?? 0 }}% vs
                            {{ insights.attendanceTrend?.previous ?? 0 }}%
                            {{
                                insights.attendanceTrend?.previousLabel ??
                                "last month"
                            }}
                        </p>
                        <Link
                            :href="route('student.attendance.index')"
                            class="mt-3 inline-flex text-xs font-semibold text-blue-700 hover:underline"
                        >
                            View attendance details
                        </Link>
                    </div>

                    <div
                        class="rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-4"
                    >
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-amber-700"
                        >
                            Grade risk hints
                        </p>
                        <p
                            v-if="!(insights.riskSubjects?.length > 0)"
                            class="mt-2 text-sm text-amber-900"
                        >
                            No subject risk flag right now.
                        </p>
                        <div v-else class="mt-2 space-y-2">
                            <div
                                v-for="subject in insights.riskSubjects"
                                :key="subject.subjectCode"
                                class="rounded-lg border border-amber-200 bg-white px-2.5 py-2"
                            >
                                <p class="text-xs font-semibold text-amber-900">
                                    {{ subject.subjectCode }} - {{ subject.avgScore }}%
                                </p>
                                <p class="mt-0.5 text-[11px] text-amber-700">
                                    {{ subject.gapFromAverage }} pts below your average
                                </p>
                            </div>
                        </div>
                        <Link
                            :href="route('student.grades.index')"
                            class="mt-3 inline-flex text-xs font-semibold text-amber-700 hover:underline"
                        >
                            Review grades
                        </Link>
                    </div>
                </div>

                <!-- GPA & Key Metrics Banner -->
                <div class="grid gap-4 md:grid-cols-3">
                    <!-- GPA Card -->
                    <div
                        v-if="stats.gpa !== null"
                        class="group relative overflow-hidden rounded-xl border-2 border-portal-navy bg-gradient-to-br from-portal-navy via-blue-700 to-indigo-800 p-6 text-white shadow-xl transition-all duration-300 hover:scale-105"
                    >
                        <div class="relative z-10">
                            <p
                                class="text-xs font-bold uppercase tracking-wider text-blue-200"
                            >
                                Your GPA
                            </p>
                            <p class="mt-3 text-5xl font-bold">
                                {{ stats.gpa?.toFixed(2) ?? "N/A" }}
                            </p>
                            <p class="mt-2 text-sm text-blue-100">
                                Cumulative Grade Point Average
                            </p>
                        </div>
                        <div class="absolute bottom-0 right-0 opacity-10">
                            <svg
                                class="h-32 w-32"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                />
                            </svg>
                        </div>
                    </div>

                    <!-- Pending Enrollments Alert -->
                    <Link
                        v-if="stats.pendingEnrollments > 0"
                        :href="route('my-courses.index')"
                        class="group relative overflow-hidden rounded-xl border-2 border-amber-300 bg-gradient-to-br from-amber-50 to-amber-100 p-6 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-amber-800"
                                >
                                    Enrollment Status
                                </p>
                                <p
                                    class="mt-2 text-2xl font-bold text-amber-900"
                                >
                                    {{ stats.pendingEnrollments }}
                                </p>
                                <p
                                    class="mt-1 text-xs font-medium text-amber-700"
                                >
                                    Pending approval
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
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- Outstanding Fees Alert -->
                    <Link
                        v-if="stats.outstandingFees > 0"
                        :href="route('student.fees.index')"
                        class="group relative overflow-hidden rounded-xl border-2 border-red-300 bg-gradient-to-br from-red-50 to-red-100 p-6 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-red-800"
                                >
                                    Outstanding Fees
                                </p>
                                <p class="mt-2 text-2xl font-bold text-red-900">
                                    {{ formatCurrency(stats.outstandingFees) }}
                                </p>
                                <p
                                    class="mt-1 text-xs font-medium text-red-700"
                                >
                                    Payment required
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
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Top row: hero + profile + quick actions -->
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="portal-card p-6 lg:col-span-2">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="portal-badge-no-margin mb-4">
                                    Your academic snapshot
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3"
                        >
                            <!-- My Courses Card -->
                            <Link
                                :href="route('my-courses.index')"
                                class="group relative overflow-hidden rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-4 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                            >
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">
                                            My Courses
                                        </p>
                                        <svg class="h-5 w-5 text-blue-600 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <p class="mt-2 text-3xl font-bold text-blue-900">
                                        {{ stats.myCourses ?? 0 }}
                                    </p>
                                    <p class="mt-1 text-xs font-medium text-blue-600">
                                        Enrolled courses
                                    </p>
                                </div>
                            </Link>

                            <!-- Grades Card -->
                            <Link
                                :href="route('student.grades.index')"
                                class="group relative overflow-hidden rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                            >
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold uppercase tracking-wider text-indigo-700">
                                            Grades
                                        </p>
                                        <svg class="h-5 w-5 text-indigo-600 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <p class="mt-2 text-3xl font-bold text-indigo-900">
                                        {{ stats.myGrades ?? 0 }}
                                    </p>
                                    <p class="mt-1 text-xs font-medium text-indigo-600">
                                        Subject scores
                                    </p>
                                </div>
                            </Link>

                            <!-- Attendance Card (Existing, slightly tweaked for consistency) -->
                            <div
                                class="group relative overflow-hidden rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                            >
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold uppercase tracking-wider text-indigo-700">
                                            Attendance
                                        </p>
                                        <svg class="h-5 w-5 text-indigo-600 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <p class="mt-2 text-3xl font-bold text-indigo-900">
                                        {{ stats.attendanceRate ?? 0 }}%
                                    </p>
                                    <div class="mt-3">
                                        <div class="mb-1 flex items-center justify-between text-xs">
                                            <span class="font-semibold text-indigo-700">Progress</span>
                                        </div>
                                        <div class="h-2 overflow-hidden rounded-full bg-indigo-200">
                                            <div
                                                class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600 transition-all duration-500"
                                                :style="{ width: `${stats.attendanceRate ?? 0}%` }"
                                            ></div>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs font-medium text-indigo-600">
                                        Overall attendance rate
                                    </p>
                                </div>
                            </div>

                            <!-- My Timetable Card -->
                            <Link
                                :href="route('student.timetable.index')"
                                class="group relative overflow-hidden rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-4 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                            >
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">
                                            My Timetable
                                        </p>
                                        <svg class="h-5 w-5 text-blue-600 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="mt-2 text-sm font-semibold text-blue-900 line-clamp-2">
                                        Check schedule
                                    </p>
                                    <p class="mt-4 text-xs font-medium text-blue-600">
                                        View upcoming classes
                                    </p>
                                </div>
                            </Link>

                            <!-- Fees & Payments Card -->
                            <Link
                                :href="route('student.fees.index')"
                                class="group relative overflow-hidden rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50 to-amber-100 p-4 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                            >
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold uppercase tracking-wider text-amber-700">
                                            Fees
                                        </p>
                                        <svg class="h-5 w-5 text-amber-600 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="mt-2 text-xl font-bold text-amber-900 truncate">
                                        {{ formatCurrency(stats.outstandingFees) }}
                                    </p>
                                    <p class="mt-2 text-xs font-medium text-amber-600">
                                        Outstanding balance
                                    </p>
                                </div>
                            </Link>

                            <!-- My Profile Card -->
                            <Link
                                :href="route('student.profile.show')"
                                class="group relative overflow-hidden rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 p-4 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                            >
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Profile
                                        </p>
                                        <svg class="h-5 w-5 text-slate-600 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">
                                        Manage Account
                                    </p>
                                    <p class="mt-4 text-xs font-medium text-slate-600">
                                        Update personal details
                                    </p>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- My Profile summary -->
                        <div class="portal-card p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                My profile
                            </p>
                            <p class="mt-1 text-sm text-slate-700">
                                Keep your student details and contact
                                information up to date.
                            </p>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 overflow-hidden rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="user?.photo"
                                            :src="`/storage/${user.photo}`"
                                            :alt="`Photo for ${user.name}`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-sm font-semibold text-slate-500"
                                        >
                                            {{
                                                user?.name
                                                    ?.charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-semibold text-slate-900"
                                        >
                                            {{ user?.name }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            Student account
                                        </p>
                                    </div>
                                </div>
                                <Link
                                    :href="route('student.profile.show')"
                                    class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    View profile
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
                    </div>
                </div>

                <!-- Dashboard charts - Student -->
                <div
                    v-if="
                        hasChartData(charts.feeStatus) ||
                        hasChartData(charts.gradesBySubject) ||
                        hasChartData(charts.attendanceLine) ||
                        hasChartData(charts.courseEnrollment) ||
                        hasChartData(charts.attendanceStatus) ||
                        hasChartData(charts.gradeTrendLine)
                    "
                    class="space-y-6 rounded-2xl border border-blue-100 border-l-4 border-l-blue-500 bg-gradient-to-b from-blue-50/50 to-white p-6 shadow-sm ring-1 ring-slate-900/5"
                >
                    <div
                        class="flex items-start gap-4 rounded-xl border border-blue-200/60 bg-white px-5 py-4 shadow-sm"
                    >
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600"
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
                                class="text-[11px] font-semibold uppercase tracking-[0.12em] text-blue-700"
                            >
                                Student analytics
                            </p>
                            <p class="mt-1 text-sm font-medium text-blue-900">
                                Personal trends across fees, enrollment, grades, and attendance.
                            </p>
                        </div>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <DashboardChart
                        v-if="hasChartData(charts.feeStatus)"
                        type="doughnut"
                        :chart-data="charts.feeStatus"
                        title="Fee status"
                        :variant="role"
                        :interactive="true"
                        @chart-click="onStudentFeeStatusClick"
                    />
                    <DashboardChart
                        v-if="hasChartData(charts.courseEnrollment)"
                        type="doughnut"
                        :chart-data="charts.courseEnrollment"
                        title="Enrollment status"
                        :variant="role"
                    />
                    <DashboardChart
                        v-if="hasChartData(charts.gradesBySubject)"
                        type="bar"
                        :chart-data="charts.gradesBySubject"
                        title="Scores by subject"
                        :variant="role"
                        :decimals="1"
                    />
                    <DashboardChart
                        v-if="hasChartData(charts.attendanceLine)"
                        type="line"
                        :chart-data="charts.attendanceLine"
                        title="Attendance (last 6 months)"
                        :y-max="100"
                        :variant="role"
                        value-format="percent"
                        :decimals="1"
                    />
                    </div>

                    <details
                        v-if="
                            hasChartData(charts.attendanceStatus) ||
                            hasChartData(charts.gradeTrendLine)
                        "
                        class="rounded-xl border border-blue-200/70 bg-white/90 p-4"
                    >
                        <summary
                            class="cursor-pointer text-xs font-semibold uppercase tracking-wide text-blue-700"
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
                                v-if="hasChartData(charts.gradeTrendLine)"
                                type="line"
                                :chart-data="charts.gradeTrendLine"
                                title="Average score trend (last 6 months)"
                                :y-max="100"
                                :variant="role"
                                :decimals="1"
                            />
                        </div>
                    </details>
                </div>

                <!-- Middle row: notifications, grades, my courses -->
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Notifications overview -->
                    <div class="portal-card p-6">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Notifications
                                </p>
                                <p class="mt-1 text-sm text-slate-700">
                                    Keep up with alerts, messages and
                                    announcements.
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
                                <p class="mt-1 text-xs text-slate-600">
                                    System notifications needing attention.
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
                                <p class="mt-1 text-xs text-slate-600">
                                    Unread messages from staff or teachers.
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
                                <p class="mt-1 text-xs text-slate-600">
                                    Latest news from your institution.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Grades -->
                    <div class="portal-card p-6">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Grades
                                </p>
                                <p class="mt-1 text-sm text-slate-700">
                                    View detailed subject scores.
                                </p>
                            </div>
                            <Link
                                :href="route('student.grades.index')"
                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                            >
                                View grades
                            </Link>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Grade records
                                </p>
                                <p class="text-xl font-semibold text-slate-900">
                                    {{ stats.myGrades ?? 0 }}
                                </p>
                            </div>
                            <p class="text-xs text-slate-600">
                                Each record represents a score for one subject
                                in an enrolled course.
                            </p>
                        </div>
                    </div>

                    <!-- My Courses -->
                    <div class="portal-card p-6">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    My Courses
                                </p>
                                <p class="mt-1 text-sm text-slate-700">
                                    See your current course enrolments.
                                </p>
                            </div>
                            <Link
                                :href="route('my-courses.index')"
                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                            >
                                View courses
                            </Link>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Enrolled courses
                                </p>
                                <p class="text-xl font-semibold text-slate-900">
                                    {{ stats.myCourses ?? 0 }}
                                </p>
                            </div>
                            <p class="text-xs text-slate-600">
                                Manage withdrawal requests and view course
                                details on the My Courses page.
                            </p>
                            <div class="pt-2">
                                <Link
                                    :href="route('courses.index')"
                                    class="inline-flex items-center rounded-md bg-portal-navy px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Browse all courses
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom row: Courses catalog teaser + announcements -->
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="portal-card p-6 lg:col-span-2">
                        <p class="portal-badge">Courses catalog</p>
                        <h3 class="mt-3 text-lg font-semibold text-slate-900">
                            Explore available courses
                        </h3>
                        <p class="mt-1 text-sm text-slate-600">
                            Browse the full catalog to discover required and
                            elective courses you can enroll in.
                        </p>

                        <div class="mt-4 grid gap-4 md:grid-cols-3">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    By programme
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Filter courses by department or programme
                                    focus.
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Credits & semester
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Check credit weight and semester
                                    availability.
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Enrol online
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Submit course registration directly from the
                                    portal.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <Link
                                :href="route('courses.index')"
                                class="inline-flex items-center rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                            >
                                Go to Courses catalog
                            </Link>
                        </div>
                    </div>

                    <!-- Announcements timeline -->
                    <div class="portal-card p-5">
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
</template>

