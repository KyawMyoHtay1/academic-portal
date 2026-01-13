<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, usePage } from "@inertiajs/vue3";
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
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
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
            <!-- Logged-in user summary with photo -->
            <div v-if="user" class="portal-card flex items-center gap-4 p-5">
                <div
                    class="h-14 w-14 overflow-hidden rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center"
                >
                    <img
                        v-if="user.photo"
                        :src="`/storage/${user.photo}`"
                        :alt="`Photo for ${user.name}`"
                        class="h-full w-full object-cover"
                    />
                    <span v-else class="text-base font-semibold text-slate-500">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">
                        {{ user.name }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ userRoleLabel || "User" }} &bull;
                        <span v-if="role === 'student'">
                            Student dashboard overview
                        </span>
                        <span v-else-if="role === 'teacher'">
                            Teacher dashboard overview
                        </span>
                        <span v-else-if="role === 'staff'">
                            Staff dashboard overview
                        </span>
                        <span v-else> Academic portal overview </span>
                    </p>
                </div>
            </div>

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

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="portal-card p-6 lg:col-span-2">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="portal-badge">
                                In line with project proposal
                            </p>
                            <h3
                                class="mt-3 text-lg font-semibold text-slate-900"
                            >
                                Welcome to the University Academic Portal
                            </h3>
                            <p class="mt-1 text-sm text-slate-600">
                                This dashboard will eventually bring together
                                Student Registration, Course Registration, Grade
                                Submission, Fee Payment, Timetable, Attendance
                                and Communication in one place.
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
                                Manage student records and course enrolments
                                online instead of paper forms.
                            </p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Grades & Fees
                            </p>
                            <p class="mt-1 text-xs text-slate-600">
                                Record grades, publish results and track student
                                fee payments digitally.
                            </p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Timetable, Attendance & Communication
                            </p>
                            <p class="mt-1 text-xs text-slate-600">
                                Provide online timetable access, attendance
                                tracking and notifications.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
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

                    <div class="portal-card p-5">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Next steps
                        </p>
                        <ul
                            class="mt-2 list-disc space-y-1 pl-5 text-xs text-slate-700"
                        >
                            <li>
                                Implement Student Registration & Course
                                Registration modules.
                            </li>
                            <li>
                                Design database tables for students, courses and
                                enrolments.
                            </li>
                            <li>Connect dashboard metrics to real data.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
