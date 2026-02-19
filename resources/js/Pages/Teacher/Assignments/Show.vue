<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    subject: {
        type: Object,
        required: true,
    },
    assignments: {
        type: Array,
        required: true,
    },
});

const query = ref("");
const statusFilter = ref("all");

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.assignments ?? [];

    if (statusFilter.value !== "all") {
        list = list.filter((a) => a.status === statusFilter.value);
    }

    if (q) {
        list = list.filter((a) => {
            const title = (a.title ?? "").toLowerCase();
            const desc = (a.description ?? "").toLowerCase();
            return title.includes(q) || desc.includes(q);
        });
    }

    return list;
});

const deleteAssignment = (id) => {
    if (!confirm("Are you sure you want to delete this assignment? All submissions will also be deleted.")) {
        return;
    }
    router.delete(route("teacher.assignments.destroy", id), {
        preserveScroll: true,
    });
};

const publishAssignment = (id) => {
    if (!confirm("Publish this assignment? Students will be able to see and submit it.")) {
        return;
    }
    router.post(route("teacher.assignments.publish", id), {}, { preserveScroll: true });
};

const statusBadge = (status) => {
    if (status === "published") return "bg-emerald-100 text-emerald-800";
    if (status === "closed") return "bg-slate-100 text-slate-800";
    return "bg-amber-100 text-amber-800";
};

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

const submissionPercent = (assignment) =>
    Math.min(100, Math.max(0, Number(assignment?.submitted_percent ?? 0)));

const gradedPercent = (assignment) =>
    Math.min(100, Math.max(0, Number(assignment?.graded_percent ?? 0)));
</script>

<template>
    <Head title="Assignments" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Assignments', href: route('teacher.assignments.index') },
                        { label: subject.subject_code },
                    ]"
                />
            </div>
        </template>

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Assignments
                </h2>
                <Link
                    :href="route('teacher.assignments.create', subject.id)"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark"
                >
                    Create Assignment
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 portal-card p-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Subject</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">
                        {{ subject.subject_code }} - {{ subject.title }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ subject.course_code }} - {{ subject.course_title }}
                    </p>
                </div>

                <div class="portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                All Assignments
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Manage assignments for this subject
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <select
                                v-model="statusFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-40"
                            >
                                <option value="all">All status</option>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="closed">Closed</option>
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

                    <div v-if="filtered.length === 0" class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-500">
                        {{ query.trim() || statusFilter !== 'all' ? "No assignments match your filters." : "No assignments created yet." }}
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="assignment in filtered"
                            :key="assignment.id"
                            class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-semibold text-slate-900">
                                            {{ assignment.title }}
                                        </h3>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize"
                                            :class="statusBadge(assignment.status)"
                                        >
                                            {{ assignment.status }}
                                        </span>
                                        <span
                                            v-if="assignment.is_overdue"
                                            class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-800"
                                        >
                                            Overdue
                                        </span>
                                        <span
                                            v-if="assignment.missing_submissions_count > 0"
                                            class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold text-rose-800"
                                        >
                                            Missing {{ assignment.missing_submissions_count }}
                                        </span>
                                        <span
                                            v-if="assignment.late_submissions_count > 0"
                                            class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-800"
                                        >
                                            Late {{ assignment.late_submissions_count }}
                                        </span>
                                    </div>
                                    <p v-if="assignment.description" class="mt-1 text-sm text-slate-600 line-clamp-2">
                                        {{ assignment.description }}
                                    </p>

                                    <div class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-6">
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
                                                Submitted
                                            </p>
                                            <p class="mt-1 text-xs font-medium text-slate-800">
                                                {{ assignment.submissions_count }}/{{ assignment.expected_students_count }} ({{ submissionPercent(assignment) }}%)
                                            </p>
                                            <div class="mt-1 h-1.5 rounded-full bg-slate-200">
                                                <div
                                                    class="h-1.5 rounded-full bg-emerald-500"
                                                    :style="{ width: `${submissionPercent(assignment)}%` }"
                                                ></div>
                                            </div>
                                        </div>

                                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                                Graded
                                            </p>
                                            <p class="mt-1 text-xs font-medium text-slate-800">
                                                {{ assignment.graded_count }}/{{ assignment.expected_students_count }} ({{ gradedPercent(assignment) }}%)
                                            </p>
                                            <div class="mt-1 h-1.5 rounded-full bg-slate-200">
                                                <div
                                                    class="h-1.5 rounded-full bg-indigo-500"
                                                    :style="{ width: `${gradedPercent(assignment)}%` }"
                                                ></div>
                                            </div>
                                        </div>

                                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                                Missing
                                            </p>
                                            <p class="mt-1 text-xs font-medium text-rose-700">
                                                {{ assignment.missing_submissions_count }} student(s)
                                            </p>
                                        </div>

                                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                                Late
                                            </p>
                                            <p class="mt-1 text-xs font-medium text-amber-700">
                                                {{ assignment.late_submissions_count }} submission(s)
                                            </p>
                                        </div>

                                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                                Created By
                                            </p>
                                            <p class="mt-1 text-xs font-medium text-slate-800">
                                                {{ assignment.creator_name ?? "Unknown" }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="route('teacher.assignments.submissions', assignment.id)"
                                        class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200"
                                    >
                                        View Submissions
                                    </Link>
                                    <button
                                        v-if="assignment.status === 'draft'"
                                        type="button"
                                        @click="publishAssignment(assignment.id)"
                                        class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700"
                                    >
                                        Publish
                                    </button>
                                    <Link
                                        :href="route('teacher.assignments.edit', assignment.id)"
                                        class="rounded-md bg-blue-100 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-200"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteAssignment(assignment.id)"
                                        class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
