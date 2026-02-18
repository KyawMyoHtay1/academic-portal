<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    assignment: {
        type: Object,
        required: true,
    },
    submissions: {
        type: Array,
        required: true,
    },
    gradingTemplates: {
        type: Object,
        default: () => ({
            comment_templates: [],
            rubric_criteria: [],
        }),
    },
});

const searchQuery = ref("");
const selectedTemplate = ref("");
const gradingForm = useForm({ score: "", feedback: "", rubric: [] });
const activeGrading = ref(null);

const createRubricRows = () => {
    const criteria = props.gradingTemplates?.rubric_criteria ?? [];
    if (!Array.isArray(criteria) || criteria.length === 0) {
        return [];
    }

    return criteria.map((row) => ({
        criterion: row.criterion ?? "",
        max_score: row.max_score ?? null,
        score: "",
        comment: "",
    }));
};

const filtered = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return props.submissions;

    return props.submissions.filter((submission) => {
        const name = (submission.student?.full_name ?? "").toLowerCase();
        const studentNo = (submission.student?.student_no ?? "").toLowerCase();
        return name.includes(q) || studentNo.includes(q);
    });
});

const totalSubmissions = computed(() => Number(props.submissions?.length ?? 0));

const gradedSubmissions = computed(() =>
    Number(
        (props.submissions ?? []).filter(
            (submission) =>
                submission.status === "graded" ||
                (submission.score !== null && submission.score !== undefined)
        ).length
    )
);

const submissionPercent = computed(() => {
    if (totalSubmissions.value <= 0) return 0;

    return Math.min(
        100,
        Math.max(0, Math.round((gradedSubmissions.value / totalSubmissions.value) * 100))
    );
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

const startGrading = (submission) => {
    activeGrading.value = submission.id;
    gradingForm.score = submission.score ?? "";
    gradingForm.feedback = submission.feedback ?? "";
    gradingForm.rubric = createRubricRows();
    selectedTemplate.value = "";
};

const cancelGrading = () => {
    activeGrading.value = null;
    gradingForm.reset();
    gradingForm.rubric = [];
    selectedTemplate.value = "";
};

const submitGrade = (submissionId) => {
    gradingForm.post(route("teacher.assignments.grade", submissionId), {
        preserveScroll: true,
        onSuccess: () => {
            activeGrading.value = null;
            gradingForm.reset();
            gradingForm.rubric = [];
            selectedTemplate.value = "";
        },
    });
};

const downloadFile = (submissionId) => {
    window.location.href = route("teacher.assignments.download", submissionId);
};

const downloadAllSubmissions = () => {
    window.location.href = route("teacher.assignments.download-all", props.assignment.id);
};

const applyCommentTemplate = () => {
    const selected = selectedTemplate.value?.trim();
    if (!selected) {
        return;
    }

    const current = (gradingForm.feedback ?? "").trim();
    gradingForm.feedback = current === "" ? selected : `${current}\n\n${selected}`;
    selectedTemplate.value = "";
};
</script>

<template>
    <Head title="Assignment Submissions" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Assignments', href: route('teacher.assignments.index') },
                        {
                            label: props.assignment.subject.subject_code,
                            href: route('teacher.assignments.show', props.assignment.subject.id),
                        },
                        { label: 'Submissions' },
                    ]"
                />
            </div>
        </template>

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Assignment Submissions
                </h2>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="rounded-md border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 shadow-sm hover:bg-emerald-100"
                        @click="downloadAllSubmissions"
                    >
                        Download All (ZIP)
                    </button>
                    <Link
                        :href="route('teacher.assignments.show', props.assignment.subject.id)"
                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                    >
                        Back
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 portal-card p-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Assignment</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ props.assignment.title }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ props.assignment.subject.subject_code }}</p>

                    <div class="mt-4 grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                Due
                            </p>
                            <p class="mt-1 text-xs font-medium text-slate-800">
                                {{ formatDue(props.assignment) }}
                            </p>
                        </div>

                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                Max Score
                            </p>
                            <p class="mt-1 text-xs font-medium text-slate-800">
                                {{ props.assignment.max_score }} points
                            </p>
                        </div>

                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                Submissions
                            </p>
                            <p class="mt-1 text-xs font-medium text-slate-800">
                                {{ totalSubmissions }} total, {{ gradedSubmissions }} graded
                            </p>
                            <div class="mt-1 h-1.5 rounded-full bg-slate-200">
                                <div
                                    class="h-1.5 rounded-full bg-emerald-500"
                                    :style="{ width: `${submissionPercent}%` }"
                                ></div>
                            </div>
                        </div>

                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                Subject
                            </p>
                            <p class="mt-1 text-xs font-medium text-slate-800">
                                {{ props.assignment.subject.subject_code }} - {{ props.assignment.subject.title }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Student Submissions
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Review and grade student submissions
                            </p>
                        </div>
                        <div class="w-full sm:w-80">
                            <label class="block text-xs font-medium text-slate-600">Search Students</label>
                            <input
                                v-model="searchQuery"
                                type="search"
                                placeholder="Search by name or student number..."
                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>
                    </div>

                    <div
                        v-if="filtered.length === 0"
                        class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-500"
                    >
                        {{ searchQuery.trim() ? "No submissions match your search." : "No submissions yet." }}
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="submission in filtered"
                            :key="submission.id"
                            class="rounded-lg border border-slate-200 bg-white p-4"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex flex-1 items-start gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100"
                                    >
                                        <img
                                            v-if="submission.student?.photo"
                                            :src="`/storage/${submission.student.photo}`"
                                            :alt="`Photo for ${submission.student.full_name}`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span v-else class="text-xs font-semibold text-slate-500">
                                            {{ submission.student?.full_name?.charAt(0) ?? "?" }}
                                        </span>
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-semibold text-slate-900">
                                                {{ submission.student?.full_name }}
                                            </h3>
                                            <span
                                                v-if="submission.status === 'graded'"
                                                class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                            >
                                                Graded
                                            </span>
                                        </div>

                                        <p class="text-xs text-slate-500">
                                            {{ submission.student?.student_no }} - Submitted: {{ submission.submitted_at }}
                                        </p>

                                        <p v-if="submission.comments" class="mt-2 text-sm text-slate-700">
                                            {{ submission.comments }}
                                        </p>

                                        <button
                                            @click="downloadFile(submission.id)"
                                            class="mt-2 inline-flex items-center rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200"
                                        >
                                            Download file: {{ submission.original_filename }}
                                        </button>
                                    </div>
                                </div>

                                <div v-if="submission.status === 'graded'" class="text-right">
                                    <div class="text-sm font-semibold text-slate-900">
                                        Score: {{ submission.score }}/{{ props.assignment.max_score }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ submission.percentage }}%
                                    </div>
                                    <div
                                        v-if="submission.feedback"
                                        class="mt-2 max-w-xs whitespace-pre-line rounded-md bg-slate-50 p-2 text-xs text-slate-700"
                                    >
                                        {{ submission.feedback }}
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        Graded by {{ submission.graded_by }} on {{ submission.graded_at }}
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="activeGrading === submission.id"
                                class="mt-4 rounded-md border border-slate-200 bg-slate-50 p-4"
                            >
                                <form @submit.prevent="submitGrade(submission.id)">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-700">
                                                Score (out of {{ props.assignment.max_score }})
                                            </label>
                                            <input
                                                v-model.number="gradingForm.score"
                                                type="number"
                                                min="0"
                                                :max="props.assignment.max_score"
                                                step="0.01"
                                                required
                                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                            />
                                            <p v-if="gradingForm.errors.score" class="mt-1 text-xs text-red-600">
                                                {{ gradingForm.errors.score }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-slate-700">
                                                Feedback (optional)
                                            </label>
                                            <textarea
                                                v-model="gradingForm.feedback"
                                                rows="3"
                                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                            ></textarea>
                                        </div>

                                        <div
                                            v-if="(props.gradingTemplates?.comment_templates?.length ?? 0) > 0"
                                            class="rounded-md border border-slate-200 bg-white p-3"
                                        >
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Comment Templates
                                            </p>
                                            <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
                                                <select
                                                    v-model="selectedTemplate"
                                                    class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                                >
                                                    <option value="">Select a template...</option>
                                                    <option
                                                        v-for="template in props.gradingTemplates.comment_templates"
                                                        :key="template"
                                                        :value="template"
                                                    >
                                                        {{ template }}
                                                    </option>
                                                </select>
                                                <button
                                                    type="button"
                                                    class="rounded-md bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                                    @click="applyCommentTemplate"
                                                >
                                                    Insert
                                                </button>
                                            </div>
                                        </div>

                                        <div
                                            v-if="(gradingForm.rubric?.length ?? 0) > 0"
                                            class="rounded-md border border-indigo-200 bg-indigo-50 p-3"
                                        >
                                            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">
                                                Rubric Feedback (optional)
                                            </p>
                                            <p class="mt-1 text-[11px] text-indigo-700">
                                                You can score criteria and add short notes. This will be appended to feedback.
                                            </p>

                                            <div class="mt-3 space-y-2">
                                                <div
                                                    v-for="(row, idx) in gradingForm.rubric"
                                                    :key="`rubric-${idx}-${row.criterion}`"
                                                    class="grid gap-2 rounded-md border border-indigo-100 bg-white p-2 md:grid-cols-12"
                                                >
                                                    <div class="md:col-span-4">
                                                        <label class="block text-[11px] font-medium text-slate-600">
                                                            Criterion
                                                        </label>
                                                        <input
                                                            v-model="row.criterion"
                                                            type="text"
                                                            class="mt-1 block w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                                        />
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="block text-[11px] font-medium text-slate-600">
                                                            Score
                                                        </label>
                                                        <input
                                                            v-model.number="row.score"
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            class="mt-1 block w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                                        />
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="block text-[11px] font-medium text-slate-600">
                                                            Max
                                                        </label>
                                                        <input
                                                            v-model.number="row.max_score"
                                                            type="number"
                                                            min="0.01"
                                                            step="0.01"
                                                            class="mt-1 block w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                                        />
                                                    </div>
                                                    <div class="md:col-span-4">
                                                        <label class="block text-[11px] font-medium text-slate-600">
                                                            Comment
                                                        </label>
                                                        <input
                                                            v-model="row.comment"
                                                            type="text"
                                                            class="mt-1 block w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                @click="cancelGrading"
                                                class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                type="submit"
                                                :disabled="gradingForm.processing"
                                                class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700 disabled:opacity-50"
                                            >
                                                Submit Grade
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div v-else-if="submission.status !== 'graded'" class="mt-4 flex justify-end">
                                <button
                                    @click="startGrading(submission)"
                                    class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark"
                                >
                                    Grade Submission
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
