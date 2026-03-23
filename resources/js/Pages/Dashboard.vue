<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeroBanner from "@/Components/Dashboard/HeroBanner.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import StudentDashboardSection from "@/Components/Dashboard/StudentDashboardSection.vue";
import StaffDashboardSection from "@/Components/Dashboard/StaffDashboardSection.vue";
import TeacherDashboardSection from "@/Components/Dashboard/TeacherDashboardSection.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
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
    insights: {
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
const insights = computed(() => props.insights ?? {});
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

const feeStatusByChartIndex = ["pending", "payment_pending", "failed", "paid"];
const enrollmentStatusByChartIndex = [
    "pending",
    "approved",
    "rejected",
    "withdrawal_pending",
];
const formatTrendPercent = (value) => {
    const numeric = Number(value ?? 0);
    if (!Number.isFinite(numeric)) {
        return "0%";
    }
    return `${numeric > 0 ? "+" : ""}${numeric.toFixed(1)}%`;
};
const trendBadgeClass = (direction, variant = "staff") => {
    if (direction === "up") {
        if (variant === "staff") {
            return "bg-emerald-100 text-emerald-700";
        }
        if (variant === "student") {
            return "bg-blue-100 text-blue-700";
        }
        return "bg-indigo-100 text-indigo-700";
    }
    if (direction === "down") {
        return "bg-red-100 text-red-700";
    }
    return "bg-slate-100 text-slate-700";
};
const onStaffFeeStatusClick = (payload) => {
    if (props.role !== "staff") {
        return;
    }

    const status = feeStatusByChartIndex[payload?.dataIndex ?? -1];
    if (!status) {
        return;
    }

    router.get(route("admin.fees.index"), { status });
};
const onStaffEnrollmentStatusClick = (payload) => {
    if (props.role !== "staff") {
        return;
    }

    const status = enrollmentStatusByChartIndex[payload?.dataIndex ?? -1];
    if (!status) {
        return;
    }

    router.get(route("admin.enrollments.index"), { status });
};
const onTeacherGradeStatusClick = () => {
    if (props.role !== "teacher") {
        return;
    }

    router.get(route("teacher.grades.index"));
};
const onTeacherAssignmentsChartClick = () => {
    if (props.role !== "teacher") {
        return;
    }

    router.get(route("teacher.assignments.index"));
};
const onStudentFeeStatusClick = () => {
    if (props.role !== "student") {
        return;
    }

    router.get(route("student.fees.index"));
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
                helper: "Unpaid fees for your account",
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

            <StudentDashboardSection
                v-if="role === 'student'"
                :user="user"
                :role="role"
                :stats="stats"
                :charts="charts"
                :insights="insights"
                :unread="unread"
                :quick-actions="quickActions"
                :format-currency="formatCurrency"
                :format-trend-percent="formatTrendPercent"
                :trend-badge-class="trendBadgeClass"
                :has-chart-data="hasChartData"
                :on-student-fee-status-click="onStudentFeeStatusClick"
            />

            <StaffDashboardSection
                v-else-if="role === 'staff'"
                :role="role"
                :stats="stats"
                :charts="charts"
                :insights="insights"
                :unread="unread"
                :quick-actions="quickActions"
                :alert-system-status="alertSystemStatus"
                :format-currency="formatCurrency"
                :format-trend-percent="formatTrendPercent"
                :trend-badge-class="trendBadgeClass"
                :has-chart-data="hasChartData"
                :on-staff-fee-status-click="onStaffFeeStatusClick"
                :on-staff-enrollment-status-click="onStaffEnrollmentStatusClick"
            />

            <TeacherDashboardSection
                v-else
                :role="role"
                :stats="stats"
                :charts="charts"
                :insights="insights"
                :unread="unread"
                :quick-actions="quickActions"
                :format-currency="formatCurrency"
                :format-trend-percent="formatTrendPercent"
                :trend-badge-class="trendBadgeClass"
                :has-chart-data="hasChartData"
                :on-teacher-grade-status-click="onTeacherGradeStatusClick"
                :on-teacher-assignments-chart-click="onTeacherAssignmentsChartClick"
            />
        </div>
    </AuthenticatedLayout>
</template>
