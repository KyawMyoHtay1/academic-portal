<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";

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
});

const form = useForm({
    date: new Date().toISOString().split("T")[0], // Today's date as default
    attendance: props.students.map((student) => ({
        student_id: student.id,
        status: "present", // Default to present
    })),
});

const query = ref("");

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
                                    placeholder="Search students…"
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                                <button
                                    v-if="query"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="query = ''"
                                >
                                    <span class="sr-only">Clear</span>
                                    ✕
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
