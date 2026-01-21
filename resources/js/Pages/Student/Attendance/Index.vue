<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";

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
    recentRecords: {
        type: Array,
        default: () => [],
    },
    message: {
        type: String,
        default: null,
    },
});

const searchCourses = ref("");
const searchSubjects = ref("");
const searchRecent = ref("");

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
    if (!q) return props.recentRecords;
    return props.recentRecords.filter((r) => {
        const hay = `${r.subject_code} ${r.subject_title} ${r.course_code} ${r.date}`.toLowerCase();
        return hay.includes(q);
    });
});
</script>

<template>
    <Head title="My Attendance" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                My Attendance Report
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'My Attendance' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Message -->
                <div v-if="message" class="mb-6 portal-card p-6">
                    <div class="rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200">
                        <p class="text-sm text-amber-800">{{ message }}</p>
                    </div>
                </div>

                <!-- Overall Statistics -->
                <div class="mb-6 grid gap-4 md:grid-cols-4">
                    <div class="portal-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Total Records
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ overall.total }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                            Present
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ overall.present }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-red-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-red-700">
                            Absent
                        </p>
                        <p class="mt-2 text-2xl font-bold text-red-900">
                            {{ overall.absent }}
                        </p>
                    </div>
                    <div
                        class="portal-card p-5"
                        :class="{
                            'bg-emerald-50': overall.rate >= 75,
                            'bg-amber-50': overall.rate >= 50 && overall.rate < 75,
                            'bg-red-50': overall.rate < 50,
                        }"
                    >
                        <p
                            class="text-xs font-semibold uppercase tracking-wide"
                            :class="{
                                'text-emerald-700': overall.rate >= 75,
                                'text-amber-700': overall.rate >= 50 && overall.rate < 75,
                                'text-red-700': overall.rate < 50,
                            }"
                        >
                            Attendance Rate
                        </p>
                        <p
                            class="mt-2 text-2xl font-bold"
                            :class="{
                                'text-emerald-900': overall.rate >= 75,
                                'text-amber-900': overall.rate >= 50 && overall.rate < 75,
                                'text-red-900': overall.rate < 50,
                            }"
                        >
                            {{ overall.rate }}%
                        </p>
                        <p
                            v-if="overall.rate < 75"
                            class="mt-1 text-xs"
                            :class="{
                                'text-amber-700': overall.rate >= 50,
                                'text-red-700': overall.rate < 50,
                            }"
                        >
                            Below 75% threshold
                        </p>
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
                                                'bg-emerald-100 text-emerald-800': course.rate >= 80,
                                                'bg-amber-100 text-amber-800': course.rate >= 60 && course.rate < 80,
                                                'bg-red-100 text-red-800': course.rate < 60,
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
                                                'bg-emerald-100 text-emerald-800': subject.rate >= 80,
                                                'bg-amber-100 text-amber-800': subject.rate >= 60 && subject.rate < 80,
                                                'bg-red-100 text-red-800': subject.rate < 60,
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
                                placeholder="Search by subject, course, date…"
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
                                        <div class="font-medium">{{ record.subject_code }}</div>
                                        <div class="text-xs text-slate-500">{{ record.subject_title }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ record.course_code }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm">
                                        <span
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize"
                                            :class="{
                                                'bg-emerald-100 text-emerald-800': record.status === 'present',
                                                'bg-red-100 text-red-800': record.status === 'absent',
                                            }"
                                        >
                                            {{ record.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="filteredRecent.length === 0">
                                    <td
                                        colspan="4"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{ searchRecent.trim() ? "No records match your search." : "No recent attendance records found." }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
