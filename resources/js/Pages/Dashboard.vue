<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
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
                title: "My Courses",
                value: formatNumber(s.teachingCourses),
                helper: "Courses you are teaching",
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

        <div class="space-y-6">
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="card in cards"
                    :key="card.title"
                    class="portal-card p-5"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                    >
                        {{ card.title }}
                    </p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">
                        {{ card.value }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ card.helper }}
                    </p>
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
