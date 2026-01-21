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
});

const searchQuery = ref("");
const gradingForm = useForm({ score: "", feedback: "" });
const activeGrading = ref(null);

const filtered = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return props.submissions;
    return props.submissions.filter((s) => {
        const name = (s.student?.full_name ?? "").toLowerCase();
        const no = (s.student?.student_no ?? "").toLowerCase();
        return name.includes(q) || no.includes(q);
    });
});

const startGrading = (submission) => {
    activeGrading.value = submission.id;
    gradingForm.score = submission.score ?? "";
    gradingForm.feedback = submission.feedback ?? "";
};

const cancelGrading = () => {
    activeGrading.value = null;
    gradingForm.reset();
};

const submitGrade = (submissionId) => {
    gradingForm.post(route("teacher.assignments.grade", submissionId), {
        preserveScroll: true,
        onSuccess: () => {
            activeGrading.value = null;
            gradingForm.reset();
        },
    });
};

const downloadFile = (submissionId) => {
    window.location.href = route("teacher.assignments.download", submissionId);
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
                <Link
                    :href="route('teacher.assignments.show', props.assignment.subject.id)"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Back
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 portal-card p-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Assignment</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ props.assignment.title }}</p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ props.assignment.subject.subject_code }} - Max score: {{ props.assignment.max_score }}
                    </p>
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
                                placeholder="Search by name or student number…"
                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>
                    </div>

                    <div v-if="filtered.length === 0" class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-500">
                        {{ searchQuery.trim() ? "No submissions match your search." : "No submissions yet." }}
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="submission in filtered"
                            :key="submission.id"
                            class="rounded-lg border border-slate-200 bg-white p-4"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1">
                                    <div class="h-10 w-10 overflow-hidden rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center">
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
                                            {{ submission.student?.student_no }} • Submitted: {{ submission.submitted_at }}
                                        </p>
                                        <p v-if="submission.comments" class="mt-2 text-sm text-slate-700">
                                            {{ submission.comments }}
                                        </p>
                                        <button
                                            @click="downloadFile(submission.id)"
                                            class="mt-2 inline-flex items-center rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200"
                                        >
                                            📎 {{ submission.original_filename }}
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
                                    <div v-if="submission.feedback" class="mt-2 max-w-xs rounded-md bg-slate-50 p-2 text-xs text-slate-700">
                                        {{ submission.feedback }}
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        Graded by {{ submission.graded_by }} on {{ submission.graded_at }}
                                    </div>
                                </div>
                            </div>

                            <!-- Grading Form -->
                            <div v-if="activeGrading === submission.id" class="mt-4 rounded-md border border-slate-200 bg-slate-50 p-4">
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

                            <!-- Grade Button -->
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
