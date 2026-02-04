<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeroBanner from "@/Components/Dashboard/HeroBanner.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
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

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="card in cards"
                    :key="card.title"
                    class="portal-card p-5 transition-shadow hover:shadow-md"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                {{ card.title }}
                            </p>
                            <p
                                class="mt-2 text-2xl font-semibold text-slate-900"
                            >
                                {{ card.value }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ card.helper }}
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-lg"
                                :class="{
                                    'bg-blue-100':
                                        card.title === 'Students' ||
                                        card.title === 'My Courses',
                                    'bg-emerald-100':
                                        card.title === 'Courses' ||
                                        card.title === 'My Subjects',
                                    'bg-amber-100':
                                        card.title === 'Fees' ||
                                        card.title === 'Outstanding Fees',
                                    'bg-indigo-100':
                                        card.title === 'Attendance' ||
                                        card.title === 'Grades' ||
                                        card.title === 'Grades Recorded',
                                    'bg-purple-100':
                                        card.title === 'Students Taught',
                                }"
                            >
                                <svg
                                    v-if="
                                        card.title === 'Students' ||
                                        card.title === 'Students Taught'
                                    "
                                    class="h-6 w-6 text-blue-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                                <svg
                                    v-else-if="
                                        card.title === 'Courses' ||
                                        card.title === 'My Courses' ||
                                        card.title === 'My Subjects'
                                    "
                                    class="h-6 w-6 text-emerald-600"
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
                                <svg
                                    v-else-if="
                                        card.title === 'Fees' ||
                                        card.title === 'Outstanding Fees'
                                    "
                                    class="h-6 w-6 text-amber-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <svg
                                    v-else-if="card.title === 'Attendance'"
                                    class="h-6 w-6 text-indigo-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                    />
                                </svg>
                                <svg
                                    v-else-if="
                                        card.title === 'Grades' ||
                                        card.title === 'Grades Recorded'
                                    "
                                    class="h-6 w-6 text-indigo-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
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

                        <div class="mt-4 grid gap-4 md:grid-cols-3">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    My Courses
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-slate-900"
                                >
                                    {{ stats.myCourses ?? 0 }}
                                    course<span
                                        v-if="(stats.myCourses ?? 0) !== 1"
                                        >s</span
                                    >
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    View enrolled courses and manage
                                    registrations.
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Grades
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-slate-900"
                                >
                                    {{ stats.myGrades ?? 0 }} record<span
                                        v-if="(stats.myGrades ?? 0) !== 1"
                                        >s</span
                                    >
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Check subject scores published so far.
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Attendance
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-slate-900"
                                >
                                    {{ stats.attendanceRate ?? 0 }}%
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Overall attendance across your subjects.
                                </p>
                            </div>
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

                        <!-- Quick actions -->
                        <div class="portal-card p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Quick actions
                            </p>
                            <div class="mt-3 grid gap-2">
                                <Link
                                    v-for="a in quickActions"
                                    :key="a.href"
                                    :href="a.href"
                                    class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    <span>{{ a.label }}</span>
                                    <span class="text-slate-400">›</span>
                                </Link>
                            </div>
                        </div>
                    </div>
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
                                        You’re all caught up
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
                                        <span v-if="a.pinned">📌 </span>
                                        {{ a.title }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ a.author }} · {{ a.created_at }}
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
                        BSc (Hons) Computing final year project – University
                        Academic Portal using Vue.js and Laravel.
                    </p>
                </div>
            </div>

            <!-- Staff view: admin-focused overview -->
            <div v-else-if="role === 'staff'" class="space-y-6">
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="portal-card p-6 lg:col-span-2">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="portal-badge">Staff dashboard</p>
                                <h3
                                    class="mt-3 text-lg font-semibold text-slate-900"
                                >
                                    Manage students, courses and academic data
                                </h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    High-level view of registrations, fees and
                                    attendance across the institution.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-3">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Student & Course Registration
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Approve enrollments and keep records
                                    accurate and up to date.
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Grades & Fees
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Monitor grade submissions and student fee
                                    payments.
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Timetable & Attendance
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Coordinate timetables and track overall
                                    attendance.
                                </p>
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
                                        Manually run the low attendance alert check.
                                        Alerts will be queued and sent in the
                                        background.
                                    </p>
                                    
                                    <!-- Status indicator -->
                                    <div
                                        v-if="alertSystemStatus"
                                        class="mt-3 rounded-lg border p-3"
                                        :class="{
                                            'border-emerald-200 bg-emerald-50': alertSystemStatus.status === 'ready',
                                            'border-amber-200 bg-amber-50': alertSystemStatus.status === 'warning',
                                        }"
                                    >
                                        <div class="flex items-start gap-2">
                                            <svg
                                                v-if="alertSystemStatus.status === 'ready'"
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
                                                        'text-emerald-800': alertSystemStatus.status === 'ready',
                                                        'text-amber-800': alertSystemStatus.status === 'warning',
                                                    }"
                                                >
                                                    {{ alertSystemStatus.status === 'ready' ? 'Automatic alerts active' : 'Setup required' }}
                                                </p>
                                                <p
                                                    class="mt-1 text-xs"
                                                    :class="{
                                                        'text-emerald-700': alertSystemStatus.status === 'ready',
                                                        'text-amber-700': alertSystemStatus.status === 'warning',
                                                    }"
                                                >
                                                    {{ alertSystemStatus.message }}
                                                </p>
                                                <div
                                                    v-if="alertSystemStatus.pendingJobs > 0"
                                                    class="mt-2 text-xs"
                                                    :class="{
                                                        'text-emerald-700': alertSystemStatus.pendingJobs <= 10,
                                                        'text-amber-700': alertSystemStatus.pendingJobs > 10,
                                                    }"
                                                >
                                                    {{ alertSystemStatus.pendingJobs }} job(s) in queue
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

                        <!-- Quick actions -->
                        <div class="portal-card p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Quick actions
                            </p>
                            <div class="mt-3 grid gap-2">
                                <Link
                                    v-for="a in quickActions"
                                    :key="a.href"
                                    :href="a.href"
                                    class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    <span>{{ a.label }}</span>
                                    <span class="text-slate-400">›</span>
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
                                        You’re all caught up
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
                                        <span v-if="a.pinned">📌 </span>
                                        {{ a.title }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ a.author }} · {{ a.created_at }}
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
                            BSc (Hons) Computing final year project – University
                            Academic Portal using Vue.js and Laravel.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Teacher view: teaching-focused overview -->
            <div v-else class="space-y-6">
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="portal-card p-6 lg:col-span-2">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="portal-badge">Teaching dashboard</p>
                                <h3
                                    class="mt-3 text-lg font-semibold text-slate-900"
                                >
                                    Overview of your subjects and students
                                </h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    Track subjects taught, student coverage and
                                    grades you have recorded.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-3">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    My Subjects
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-slate-900"
                                >
                                    {{ stats.teachingSubjects ?? 0 }}
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Subjects assigned to you this period.
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Students taught
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-slate-900"
                                >
                                    {{ stats.studentsTaught ?? 0 }}
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Unique students across all your courses.
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Grades recorded
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold text-slate-900"
                                >
                                    {{ stats.gradesRecorded ?? 0 }}
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Grade entries you’ve submitted so far.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Quick actions -->
                        <div class="portal-card p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Quick actions
                            </p>
                            <div class="mt-3 grid gap-2">
                                <Link
                                    v-for="a in quickActions"
                                    :key="a.href"
                                    :href="a.href"
                                    class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    <span>{{ a.label }}</span>
                                    <span class="text-slate-400">›</span>
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
                                        You’re all caught up
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
                                        <span v-if="a.pinned">📌 </span>
                                        {{ a.title }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ a.author }} · {{ a.created_at }}
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
                            BSc (Hons) Computing final year project – University
                            Academic Portal using Vue.js and Laravel.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
