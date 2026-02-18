<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps({
    subject: {
        type: Object,
        required: true,
    },
    students: {
        type: Array,
        required: true,
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    totalSessions: {
        type: Number,
        default: 0,
    },
    sessionHistory: {
        type: Array,
        default: () => [],
    },
    sessionDetails: {
        type: Object,
        default: () => ({}),
    },
    historyFilters: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    date: new Date().toISOString().split("T")[0], // Today's date as default
    attendance: props.students.map((student) => ({
        student_id: student.id,
        status: "present", // Default to present
    })),
});

const query = ref("");
const historyDateFrom = ref(props.historyFilters?.date_from || "");
const historyDateTo = ref(props.historyFilters?.date_to || "");
const selectedHistoryDate = ref(props.sessionHistory?.[0]?.date ?? null);

const attendanceEntries = computed(() => {
    const q = query.value.trim().toLowerCase();
    return form.attendance
        .map((record) => {
            const student = props.students.find((s) => s.id === record.student_id);
            return { record, student };
        })
        .filter((entry) => {
            if (!q) return true;
            if (!entry.student) return false;
            const name = (entry.student.full_name ?? "").toLowerCase();
            const no = (entry.student.student_no ?? "").toLowerCase();
            return name.includes(q) || no.includes(q);
        });
});

const stats = computed(() => {
    const total = form.attendance.length;
    const present = form.attendance.filter((r) => r.status === "present").length;
    const absent = form.attendance.filter((r) => r.status === "absent").length;
    return {
        total,
        present,
        absent,
        presentPct: total > 0 ? Math.round((present / total) * 100) : 0,
    };
});

const markAll = (status) => {
    form.attendance.forEach((r) => {
        r.status = status;
    });
};

const submit = () => {
    form.post(route("teacher.attendance.store", props.subject.id));
};

const applyHistoryFilters = () => {
    router.get(
        route("teacher.attendance.show", props.subject.id),
        {
            date_from: historyDateFrom.value || undefined,
            date_to: historyDateTo.value || undefined,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    );
};

const clearHistoryFilters = () => {
    historyDateFrom.value = "";
    historyDateTo.value = "";
    applyHistoryFilters();
};

const selectedSessionRows = computed(() => {
    if (!selectedHistoryDate.value) return [];
    return props.sessionDetails?.[selectedHistoryDate.value] ?? [];
});

watch(
    () => props.sessionHistory,
    (history) => {
        if (!Array.isArray(history) || history.length === 0) {
            selectedHistoryDate.value = null;
            return;
        }

        const selectedExists = history.some(
            (session) => session.date === selectedHistoryDate.value
        );
        if (!selectedExists) {
            selectedHistoryDate.value = history[0].date;
        }
    }
);
</script>

<template>
    <Head title="Mark Attendance" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'Mark Attendance',
                            href: route('teacher.attendance.index'),
                        },
                        { label: props.subject.title },
                    ]"
                />
            </div>
        </template>

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Mark Attendance
                </h2>
                <Link
                    :href="route('teacher.attendance.index')"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Back to Subjects
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <!-- Subject header + stats -->
                <div class="mb-6 grid gap-4 md:grid-cols-4">
                    <div class="portal-card p-5 md:col-span-2">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Subject
                        </p>
                        <p class="mt-2 text-lg font-bold text-slate-900">
                            {{ subject.subject_code }}
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ subject.title }}
                        </p>
                        <p class="mt-2 text-xs text-slate-500">
                            {{ subject.course_code }} - {{ subject.course_title }}
                        </p>
                        <p
                            v-if="totalSessions > 0"
                            class="mt-2 text-xs text-slate-500"
                        >
                            Sessions held: {{ totalSessions }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                        >
                            Present
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ stats.present }}/{{ stats.total }}
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">
                            {{ stats.presentPct }}% present
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-rose-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-rose-700"
                        >
                            Absent
                        </p>
                        <p class="mt-2 text-2xl font-bold text-rose-900">
                            {{ stats.absent }}
                        </p>
                        <p class="mt-1 text-xs text-rose-700">
                            Mark absences carefully
                        </p>
                    </div>
                </div>

                <div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Session History
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Review past attendance sessions with optional date-range filtering.
                            </p>
                        </div>
                        <div class="grid w-full gap-2 sm:grid-cols-4 lg:w-auto">
                            <input
                                v-model="historyDateFrom"
                                type="date"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                            <input
                                v-model="historyDateTo"
                                type="date"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                            <button
                                type="button"
                                class="rounded-md bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800"
                                @click="applyHistoryFilters"
                            >
                                Apply
                            </button>
                            <button
                                type="button"
                                class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                @click="clearHistoryFilters"
                            >
                                Clear
                            </button>
                        </div>
                    </div>

                    <div v-if="sessionHistory.length === 0" class="mt-4 rounded-lg bg-slate-50 p-4 text-sm text-slate-500">
                        No attendance sessions found for the selected range.
                    </div>

                    <div v-else class="mt-4 grid gap-4 lg:grid-cols-2">
                        <div class="overflow-hidden rounded-lg border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Date</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Present</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Absent</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    <tr
                                        v-for="session in sessionHistory"
                                        :key="session.date"
                                        class="cursor-pointer hover:bg-slate-50"
                                        :class="{
                                            'bg-indigo-50/60': selectedHistoryDate === session.date,
                                        }"
                                        @click="selectedHistoryDate = session.date"
                                    >
                                        <td class="px-3 py-2 text-xs font-medium text-slate-800">
                                            {{ session.date }}
                                        </td>
                                        <td class="px-3 py-2 text-center text-xs text-emerald-700">
                                            {{ session.present }}
                                        </td>
                                        <td class="px-3 py-2 text-center text-xs text-rose-700">
                                            {{ session.absent }}
                                        </td>
                                        <td class="px-3 py-2 text-center text-xs font-semibold text-slate-800">
                                            {{ session.rate }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Session Detail
                            </p>
                            <p class="mt-1 text-sm text-slate-700">
                                <span v-if="selectedHistoryDate">
                                    {{ selectedHistoryDate }}
                                </span>
                                <span v-else>Select a session date</span>
                            </p>

                            <div
                                v-if="selectedSessionRows.length === 0"
                                class="mt-3 rounded-md bg-white p-3 text-xs text-slate-500"
                            >
                                No detailed entries available for this session.
                            </div>
                            <div v-else class="mt-3 max-h-56 overflow-auto rounded-md border border-slate-200 bg-white">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Student</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        <tr v-for="record in selectedSessionRows" :key="record.id">
                                            <td class="px-3 py-2 text-xs text-slate-700">
                                                {{ record.student_name }} ({{ record.student_no }})
                                            </td>
                                            <td class="px-3 py-2 text-center text-xs">
                                                <span
                                                    class="inline-flex rounded-full px-2 py-1 font-semibold capitalize"
                                                    :class="{
                                                        'bg-emerald-100 text-emerald-800': record.status === 'present',
                                                        'bg-rose-100 text-rose-800': record.status === 'absent',
                                                    }"
                                                >
                                                    {{ record.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="portal-card p-6">
                    <div class="mb-6">
                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                        >
                            <div class="flex-1">
                                <label
                                    for="date"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="date"
                                    v-model="form.date"
                                    type="date"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.date,
                                    }"
                                />
                                <p
                                    v-if="form.errors.date"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.date }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    class="rounded-md bg-emerald-100 px-3 py-2 text-xs font-medium text-emerald-800 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                    @click="markAll('present')"
                                >
                                    Mark all present
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md bg-rose-100 px-3 py-2 text-xs font-medium text-rose-800 hover:bg-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2"
                                    @click="markAll('absent')"
                                >
                                    Mark all absent
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-900">
                                    Student Attendance
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Quick-mark present/absent and save.
                                </p>
                            </div>
                            <div class="relative w-full sm:w-64">
                                <input
                                    v-model="query"
                                    type="text"
                                    placeholder="Search students..."
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                                <button
                                    v-if="query"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="query = ''"
                                >
                                    <span class="sr-only">Clear</span>
                                    x
                                </button>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Students Attendance Table -->
                            <div>
                                <div
                                    v-if="students.length === 0"
                                    class="rounded-md border border-slate-200 bg-slate-50 p-4 text-center text-sm text-slate-500"
                                >
                                    No students enrolled in this course.
                                </div>

                                <div
                                    v-else
                                    class="overflow-hidden rounded-md border border-slate-200"
                                >
                                    <table
                                        class="min-w-full divide-y divide-slate-200"
                                    >
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Student
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="divide-y divide-slate-200 bg-white"
                                        >
                                            <tr
                                                v-for="entry in attendanceEntries"
                                                :key="entry.record.student_id"
                                                class="bg-white hover:bg-slate-50 transition-colors"
                                            >
                                                <td
                                                    class="px-4 py-4 text-sm text-slate-700"
                                                >
                                                    <div
                                                        class="flex items-center gap-3"
                                                    >
                                                        <div
                                                            class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                                        >
                                                            <img
                                                                v-if="entry.student?.photo"
                                                                :src="`/storage/${entry.student.photo}`"
                                                                :alt="`Photo for ${entry.student.full_name}`"
                                                                class="h-full w-full object-cover"
                                                            />
                                                            <span
                                                                v-else
                                                                class="text-xs font-semibold text-slate-500"
                                                            >
                                                                {{
                                                                    (entry.student?.full_name ?? "?")
                                                                        .charAt(0)
                                                                        .toUpperCase()
                                                                }}
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="flex flex-col"
                                                        >
                                                            <span
                                                                class="text-sm font-medium text-slate-900"
                                                            >
                                                                {{
                                                                    entry.student?.full_name ??
                                                                    "Unknown"
                                                                }}
                                                            </span>
                                                            <span
                                                                class="text-xs text-slate-500"
                                                            >
                                                                {{
                                                    entry.student?.student_no ??
                                                    "-"
                                                                }}
                                                            </span>
                                            <span
                                                v-if="
                                                    summary[
                                                        entry.record.student_id
                                                    ]
                                                "
                                                class="mt-1 text-xs text-slate-500"
                                            >
                                                Overall:
                                                {{
                                                    summary[
                                                        entry.record.student_id
                                                    ].present
                                                }}
                                                /
                                                {{
                                                    summary[
                                                        entry.record.student_id
                                                    ].total
                                                }}
                                                (
                                                {{
                                                    summary[
                                                        entry.record.student_id
                                                    ].percentage
                                                }}% )
                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="whitespace-nowrap px-4 py-4 text-center"
                                                >
                                                    <div
                                                        class="inline-flex overflow-hidden rounded-md border border-slate-200"
                                                    >
                                                        <button
                                                            type="button"
                                                            class="px-3 py-1.5 text-xs font-semibold"
                                                            :class="
                                                                entry.record
                                                                    .status ===
                                                                'present'
                                                                    ? 'bg-emerald-100 text-emerald-800'
                                                                    : 'bg-white text-slate-700 hover:bg-slate-50'
                                                            "
                                                            @click="
                                                                entry.record.status =
                                                                    'present'
                                                            "
                                                        >
                                                            Present
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="px-3 py-1.5 text-xs font-semibold"
                                                            :class="
                                                                entry.record
                                                                    .status ===
                                                                'absent'
                                                                    ? 'bg-rose-100 text-rose-800'
                                                                    : 'bg-white text-slate-700 hover:bg-slate-50'
                                                            "
                                                            @click="
                                                                entry.record.status =
                                                                    'absent'
                                                            "
                                                        >
                                                            Absent
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr
                                                v-if="
                                                    students.length > 0 &&
                                                    attendanceEntries.length ===
                                                        0
                                                "
                                            >
                                                <td
                                                    colspan="2"
                                                    class="px-4 py-8 text-center text-sm text-slate-500"
                                                >
                                                    {{
                                                        query.trim()
                                                            ? "No students match your search."
                                                            : "No students found."
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p
                                    v-if="form.errors.attendance"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.attendance }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
                                <Link
                                    :href="route('teacher.attendance.index')"
                                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 disabled:opacity-50"
                                >
                                    <span v-if="form.processing">
                                        Saving...
                                    </span>
                                    <span v-else>Save Attendance</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
