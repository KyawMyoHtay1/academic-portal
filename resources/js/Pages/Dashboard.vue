<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeroBanner from "@/Components/Dashboard/HeroBanner.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import DashboardChart from "@/Components/Dashboard/DashboardChart.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    role: {
        type: String,
        default: "student",
    },
    stats: {
        type: Object,
        required: true,
    },
    charts: {
        type: Object,
        default: () => ({}),
    },
    alertSystemStatus: {
        type: Object,
        default: null,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const unread = computed(
    () =>
        page.props.unread ?? {
            messages: 0,
            notifications: 0,
            announcements: 0,
        }
);
const userRoleLabel = computed(() => {
    if (!user.value?.role) {
        return "";
    }
    return user.value.role.charAt(0).toUpperCase() + user.value.role.slice(1);
});

const formatNumber = (value) => new Intl.NumberFormat().format(value ?? 0);
const formatCurrency = (value) =>
    new Intl.NumberFormat("en-GB", {
        style: "currency",
        currency: "GBP",
        maximumFractionDigits: 0,
    }).format(value ?? 0);
const hasChartData = (chart) => {
    if (!chart || !Array.isArray(chart.datasets)) {
        return false;
    }

    return chart.datasets.some(
        (dataset) => Array.isArray(dataset?.data) && dataset.data.length > 0
    );
};

const cards = computed(() => {
    const list = [];
    const s = props.stats || {};

    // Staff cards
    if (props.role === "staff") {
        list.push(
            {
                title: "Students",
                value: formatNumber(s.students),
                helper: "Total registered students",
            },
            {
                title: "Courses",
                value: formatNumber(s.courses),
                helper: "Active course offerings",
            },
            {
                title: "Fees",
                value: formatCurrency(s.feeTotal),
                helper: "Fees recorded in the system",
            },
            {
                title: "Attendance",
                value: `${s.attendanceRate ?? 0}%`,
                helper: "Overall attendance",
            }
        );
    }

    // Teacher cards
    if (props.role === "teacher") {
        list.push(
            {
                title: "My Subjects",
                value: formatNumber(s.teachingSubjects),
                helper: "Subjects you are teaching",
            },
            {
                title: "Students Taught",
                value: formatNumber(s.studentsTaught),
                helper: "Unique students across your courses",
            },
            {
                title: "Grades Recorded",
                value: formatNumber(s.gradesRecorded),
                helper: "Grades entered for your courses",
            },
            {
                title: "Attendance",
                value: `${s.attendanceRate ?? 0}%`,
                helper: "Attendance across your courses",
            }
        );
    }

    // Student cards
    if (props.role === "student") {
        list.push(
            {
                title: "My Courses",
                value: formatNumber(s.myCourses),
                helper: "Courses you are enrolled in",
            },
            {
                title: "Outstanding Fees",
                value: formatCurrency(s.outstandingFees),
                helper: "Pending fees for your account",
            },
            {
                title: "Grades",
                value: formatNumber(s.myGrades),
                helper: "Grades available for you",
            },
            {
                title: "Attendance",
                value: `${s.attendanceRate ?? 0}%`,
                helper: "Your attendance rate",
            }
        );
    }

    return list;
});

const quickActions = computed(() => {
    const role = props.role;
    if (role === "staff") {
        return [
            {
                label: "Enrollment Requests",
                href: route("admin.enrollments.index"),
            },
            { label: "Manage Courses", href: route("admin.courses.index") },
            { label: "Manage Subjects", href: route("admin.subjects.index") },
            { label: "Fee Management", href: route("admin.fees.index") },
            {
                label: "Timetable Management",
                href: route("admin.timetables.index"),
            },
            { label: "User Management", href: route("admin.users.index") },
            {
                label: "Announcements",
                href: route("admin.announcements.index"),
            },
            {
                label: "Failed Jobs",
                href: route("admin.failed-jobs.index"),
            },
        ];
    }
    if (role === "teacher") {
        return [
            { label: "My Timetable", href: route("teacher.timetable.index") },
            {
                label: "My Teaching Subjects",
                href: route("teacher.courses.index"),
            },
            {
                label: "Mark Attendance",
                href: route("teacher.attendance.index"),
            },
            { label: "Grades", href: route("teacher.grades.index") },
            { label: "Announcements", href: route("announcements.index") },
            { label: "Messages", href: route("messages.index") },
        ];
    }
    // student
    return [
        { label: "My Courses", href: route("my-courses.index") },
        { label: "Courses Catalog", href: route("courses.index") },
        { label: "Grades", href: route("student.grades.index") },
        { label: "Notifications", href: route("notifications.index") },
        { label: "Timetable", href: route("student.timetable.index") },
        { label: "Fees", href: route("student.fees.index") },
        { label: "My Profile", href: route("student.profile.show") },
        { label: "Announcements", href: route("announcements.index") },
        { label: "Messages", href: route("messages.index") },
    ];
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Academic Overview
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[]" />
            </div>
        </template>

        <div class="space-y-6">
            <!-- Hero Banner -->
            <HeroBanner :user="user" :role="role" />

            <!-- Enhanced Visual Stats Cards -->
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="card in cards"
                    :key="card.title"
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br p-6 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl"
                    :class="{
                        'from-blue-50 to-blue-100 border border-blue-200':
                            card.title === 'Students' ||
                            card.title === 'My Courses',
                        'from-emerald-50 to-emerald-100 border border-emerald-200':
                            card.title === 'Courses' ||
                            card.title === 'My Subjects',
                        'from-amber-50 to-amber-100 border border-amber-200':
                            card.title === 'Fees' ||
                            card.title === 'Outstanding Fees',
                        'from-indigo-50 to-indigo-100 border border-indigo-200':
                            card.title === 'Attendance' ||
                            card.title === 'Grades' ||
                            card.title === 'Grades Recorded' ||
                            card.title === 'Students Taught',
                    }"
                >
                    <!-- Decorative background pattern -->
                    <div
                        class="absolute inset-0 opacity-5"
                        :class="{
                            'bg-blue-600':
                                card.title === 'Students' ||
                                card.title === 'My Courses',
                            'bg-emerald-600':
                                card.title === 'Courses' ||
                                card.title === 'My Subjects',
                            'bg-amber-600':
                                card.title === 'Fees' ||
                                card.title === 'Outstanding Fees',
                            'bg-indigo-600':
                                card.title === 'Attendance' ||
                                card.title === 'Grades' ||
                                card.title === 'Grades Recorded' ||
                                card.title === 'Students Taught',
                        }"
                    >
                        <svg
                            class="h-full w-full"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <defs>
                                <pattern
                                    id="grid"
                                    width="40"
                                    height="40"
                                    patternUnits="userSpaceOnUse"
                                >
                                    <path
                                        d="M 40 0 L 0 0 0 40"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1"
                                    />
                                </pattern>
                            </defs>
                            <rect
                                width="100%"
                                height="100%"
                                fill="url(#grid)"
                            />
                        </svg>
                    </div>

                    <div class="relative flex items-center justify-between">
                        <div class="flex-1">
                            <p
                                class="text-xs font-bold uppercase tracking-wider"
                                :class="{
                                    'text-blue-700':
                                        card.title === 'Students' ||
                                        card.title === 'My Courses',
                                    'text-emerald-700':
                                        card.title === 'Courses' ||
                                        card.title === 'My Subjects',
                                    'text-amber-700':
                                        card.title === 'Fees' ||
                                        card.title === 'Outstanding Fees',
                                    'text-indigo-700':
                                        card.title === 'Attendance' ||
                                        card.title === 'Grades' ||
                                        card.title === 'Grades Recorded' ||
                                        card.title === 'Students Taught',
                                }"
                            >
                                {{ card.title }}
                            </p>
                            <p class="mt-3 text-3xl font-bold text-slate-900">
                                {{ card.value }}
                            </p>
                            <p
                                class="mt-2 text-xs font-medium"
                                :class="{
                                    'text-blue-600':
                                        card.title === 'Students' ||
                                        card.title === 'My Courses',
                                    'text-emerald-600':
                                        card.title === 'Courses' ||
                                        card.title === 'My Subjects',
                                    'text-amber-600':
                                        card.title === 'Fees' ||
                                        card.title === 'Outstanding Fees',
                                    'text-indigo-600':
                                        card.title === 'Attendance' ||
                                        card.title === 'Grades' ||
                                        card.title === 'Grades Recorded' ||
                                        card.title === 'Students Taught',
                                }"
                            >
                                {{ card.helper }}
                            </p>
                            <!-- Progress bar for Attendance -->
                            <div
                                v-if="card.title === 'Attendance'"
                                class="mt-4"
                            >
                                <div
                                    class="mb-1 flex items-center justify-between text-xs"
                                >
                                    <span
                                        class="font-semibold"
                                        :class="{
                                            'text-indigo-700':
                                                card.title === 'Attendance',
                                        }"
                                    >
                                        Progress
                                    </span>
                                    <span
                                        class="font-bold"
                                        :class="{
                                            'text-indigo-700':
                                                card.title === 'Attendance',
                                        }"
                                    >
                                        {{ stats.attendanceRate ?? 0 }}%
                                    </span>
                                </div>
                                <div
                                    class="h-2 overflow-hidden rounded-full bg-white/60"
                                >
                                    <div
                                        class="h-full rounded-full transition-all duration-500"
                                        :class="{
                                            'bg-gradient-to-r from-indigo-500 to-indigo-600':
                                                card.title === 'Attendance',
                                        }"
                                        :style="{
                                            width: `${
                                                stats.attendanceRate ?? 0
                                            }%`,
                                        }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-xl bg-slate-500 text-white shadow-lg transition-transform duration-300 group-hover:scale-110"
                                :class="{
                                    'bg-gradient-to-br from-blue-500 to-blue-600':
                                        card.title === 'Students' ||
                                        card.title === 'My Courses',
                                    'bg-gradient-to-br from-emerald-500 to-emerald-600':
                                        card.title === 'Courses' ||
                                        card.title === 'My Subjects',
                                    'bg-gradient-to-br from-amber-500 to-amber-600':
                                        card.title === 'Fees' ||
                                        card.title === 'Outstanding Fees',
                                    'bg-gradient-to-br from-indigo-500 to-indigo-600':
                                        card.title === 'Attendance' ||
                                        card.title === 'Grades' ||
                                        card.title === 'Grades Recorded' ||
                                        card.title === 'Students Taught',
                                }"
                            >
                                <svg
                                    v-if="
                                        card.title === 'Students' ||
                                        card.title === 'Students Taught'
                                    "
                                    class="h-8 w-8 text-white"
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
                                <svg
                                    v-else-if="
                                        card.title === 'Courses' ||
                                        card.title === 'My Courses' ||
                                        card.title === 'My Subjects'
                                    "
                                    class="h-8 w-8 text-white"
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
                                <svg
                                    v-else-if="
                                        card.title === 'Fees' ||
                                        card.title === 'Outstanding Fees'
                                    "
                                    class="h-8 w-8 text-white"
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
                                <svg
                                    v-else-if="card.title === 'Attendance'"
                                    class="h-8 w-8 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                    />
                                </svg>
                                <svg
                                    v-else-if="
                                        card.title === 'Grades' ||
                                        card.title === 'Grades Recorded'
                                    "
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student dashboard: more visual overview for key areas -->
            <div v-if="role === 'student'" class="space-y-6">
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

            <!-- Staff view: admin-focused overview -->
            <div v-else-if="role === 'staff'" class="space-y-6">
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
                        </div>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <DashboardChart
                        v-if="hasChartData(charts.feeStatus)"
                        type="doughnut"
                        :chart-data="charts.feeStatus"
                        title="Fee status (all fees)"
                        :variant="role"
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

            <!-- Teacher view: teaching-focused overview -->
            <div v-else class="space-y-6">
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
        </div>
    </AuthenticatedLayout>
</template>
