<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { formatDateTimeLocal } from "@/utils/dateTime";
import { Head, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    assignments: {
        type: Array,
        required: true,
    },
    message: {
        type: String,
        default: null,
    },
});

const query = ref("");
const statusFilter = ref("all");

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.assignments ?? [];

    if (statusFilter.value === "pending") {
        list = list.filter((assignment) => !assignment.submission && assignment.can_submit);
    } else if (statusFilter.value === "submitted") {
        list = list.filter(
            (assignment) =>
                assignment.submission &&
                (assignment.submission.score === null || assignment.submission.score === undefined)
        );
    } else if (statusFilter.value === "graded") {
        list = list.filter(
            (assignment) =>
                assignment.submission?.score !== null && assignment.submission?.score !== undefined
        );
    }

    if (q) {
        list = list.filter((assignment) => {
            const title = (assignment.title ?? "").toLowerCase();
            const subject = `${assignment.subject?.subject_code ?? ""} ${assignment.subject?.title ?? ""}`.toLowerCase();
            const course = `${assignment.course?.course_code ?? ""} ${assignment.course?.title ?? ""}`.toLowerCase();
            return title.includes(q) || subject.includes(q) || course.includes(q);
        });
    }

    return list;
});

const formatDue = (assignment) => {
    if (!assignment?.due_date) return "-";

    const [year, month, day] = String(assignment.due_date)
        .split("-")
        .map((part) => parseInt(part, 10));

    const dueDate = new Date(year, month - 1, day);

    if (Number.isNaN(dueDate.getTime())) {
        return assignment.due_time
            ? `${assignment.due_date}, ${assignment.due_time}`
            : assignment.due_date;
    }

    const dateLabel = new Intl.DateTimeFormat(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
    }).format(dueDate);

    return assignment.due_time ? `${dateLabel}, ${assignment.due_time}` : dateLabel;
};

const hasScore = (assignment) =>
    assignment?.submission?.score !== null && assignment?.submission?.score !== undefined;
</script>

<template>
    <Head title="My Assignments" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                My Assignments
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'My Assignments' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="message" class="mb-6 portal-card p-6">
                    <div class="rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200">
                        <p class="text-sm text-amber-800">{{ message }}</p>
                    </div>
                </div>

                <div class="portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Published Assignments
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                View and submit assignments for your enrolled courses
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <select
                                v-model="statusFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-40"
                            >
                                <option value="all">All</option>
                                <option value="pending">Pending</option>
                                <option value="submitted">Submitted</option>
                                <option value="graded">Graded</option>
                            </select>
                            <div class="relative w-full sm:w-64">
                                <input
                                    v-model="query"
                                    type="search"
                                    placeholder="Search assignments..."
                                    class="block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="filtered.length === 0"
                        class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-500"
                    >
                        {{
                            query.trim() || statusFilter !== "all"
                                ? "No assignments match your filters."
                                : "No assignments available."
                        }}
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="assignment in filtered"
                            :key="assignment.id"
                            class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-semibold text-slate-900">
                                            {{ assignment.title }}
                                        </h3>
                                        <span
                                            v-if="assignment.is_overdue"
                                            class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-800"
                                        >
                                            Overdue
                                        </span>
                                        <span
                                            v-if="assignment.submission?.status === 'graded'"
                                            class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                        >
                                            Graded
                                        </span>
                                        <span
                                            v-else-if="assignment.submission"
                                            class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-800"
                                        >
                                            Submitted
                                        </span>
                                    </div>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ assignment.subject.subject_code }} - {{ assignment.subject.title }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ assignment.course.course_code }} - {{ assignment.course.title }}
                                    </p>

                                    <p
                                        v-if="assignment.description"
                                        class="mt-2 line-clamp-2 text-sm text-slate-700"
                                    >
                                        {{ assignment.description }}
                                    </p>

                                    <div class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                                Due
                                            </p>
                                            <p class="mt-1 text-xs font-medium text-slate-800">
                                                {{ formatDue(assignment) }}
                                            </p>
                                        </div>

                                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                                Max Score
                                            </p>
                                            <p class="mt-1 text-xs font-medium text-slate-800">
                                                {{ assignment.max_score }} points
                                            </p>
                                        </div>

                                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                                Submission
                                            </p>
                                            <p class="mt-1 text-xs font-medium text-slate-800">
                                                {{
                                                    assignment.submission
                                                        ? `Submitted: ${formatDateTimeLocal(assignment.submission.submitted_at)}`
                                                        : "Not submitted yet"
                                                }}
                                            </p>
                                        </div>

                                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                                Result
                                            </p>
                                            <p
                                                v-if="hasScore(assignment)"
                                                class="mt-1 text-xs font-semibold text-emerald-700"
                                            >
                                                {{ assignment.submission.score }}/{{ assignment.max_score }}
                                                ({{ assignment.submission.percentage }}%)
                                            </p>
                                            <p v-else class="mt-1 text-xs font-medium text-slate-800">
                                                {{ assignment.submission ? "Waiting for grading" : "-" }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2">
                                    <Link
                                        :href="route('student.assignments.show', assignment.id)"
                                        class="rounded-md bg-portal-navy px-3 py-2 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark"
                                    >
                                        {{ assignment.submission ? "View" : "Submit" }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
