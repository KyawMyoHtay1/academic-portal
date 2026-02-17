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
    submission: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    file: null,
    comments: "",
});

const fileInput = ref(null);
const isResubmitting = ref(false);

const hasScore = computed(
    () => props.submission?.score !== null && props.submission?.score !== undefined
);

const submissionStatusLabel = computed(() => {
    if (hasScore.value) return "Graded";
    if (props.submission) return "Submitted";
    if (props.assignment?.can_submit) return "Pending";
    return "Closed";
});

const submissionStatusClass = computed(() => {
    if (hasScore.value) return "bg-emerald-100 text-emerald-800";
    if (props.submission) return "bg-blue-100 text-blue-800";
    if (props.assignment?.can_submit) return "bg-amber-100 text-amber-800";
    return "bg-slate-100 text-slate-800";
});

const allowedTypesLabel = computed(() => {
    const types = props.assignment?.allowed_file_types;
    return Array.isArray(types) && types.length > 0
        ? types.join(", ").toUpperCase()
        : "PDF, DOC, DOCX";
});

const maxFileSizeMB = computed(() =>
    Math.round(Number(props.assignment?.max_file_size ?? 5120) / 1024)
);

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

const selectFile = () => {
    fileInput.value?.click();
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.file = file;
    }
};

const submit = () => {
    if (!form.file) {
        return;
    }

    form.post(route("student.assignments.submit", props.assignment.id), {
        forceFormData: true,
        onSuccess: () => {
            isResubmitting.value = false;
            form.reset();
        },
    });
};

const downloadSubmission = () => {
    if (props.submission?.id) {
        window.location.href = route("student.assignments.download", props.submission.id);
    }
};
</script>

<template>
    <Head title="Assignment Details" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'My Assignments', href: route('student.assignments.index') },
                        { label: props.assignment.title },
                    ]"
                />
            </div>
        </template>

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Assignment Details
                </h2>
                <Link
                    :href="route('student.assignments.index')"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Back
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="mb-6 portal-card p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-xl font-bold text-slate-900">
                                    {{ props.assignment.title }}
                                </h3>
                                <span
                                    v-if="props.assignment.is_overdue"
                                    class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800"
                                >
                                    Overdue
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="submissionStatusClass"
                                >
                                    {{ submissionStatusLabel }}
                                </span>
                            </div>

                            <p class="mt-1 text-sm text-slate-600">
                                {{ props.assignment.subject.subject_code }} - {{ props.assignment.subject.title }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ props.assignment.course.course_code }} - {{ props.assignment.course.title }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Due</p>
                            <p class="mt-1 text-xs font-medium text-slate-800">
                                {{ formatDue(props.assignment) }}
                            </p>
                        </div>

                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Max Score</p>
                            <p class="mt-1 text-xs font-medium text-slate-800">
                                {{ props.assignment.max_score }} points
                            </p>
                        </div>

                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Submission</p>
                            <p class="mt-1 text-xs font-medium text-slate-800">
                                {{ props.submission ? `Submitted: ${props.submission.submitted_at}` : "Not submitted yet" }}
                            </p>
                        </div>

                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Result</p>
                            <p v-if="hasScore" class="mt-1 text-xs font-semibold text-emerald-700">
                                {{ props.submission.score }}/{{ props.assignment.max_score }}
                                ({{ props.submission.percentage }}%)
                            </p>
                            <p v-else class="mt-1 text-xs font-medium text-slate-800">
                                {{ props.submission ? "Waiting for grading" : "-" }}
                            </p>
                        </div>
                    </div>

                    <div v-if="props.assignment.description" class="mt-4 rounded-md bg-slate-50 p-4">
                        <p class="text-sm font-medium text-slate-700">Description</p>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-600">
                            {{ props.assignment.description }}
                        </p>
                    </div>
                </div>

                <div v-if="props.submission" class="mb-6 portal-card p-6">
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Your Submission
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Submitted on {{ props.submission.submitted_at }}
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-medium text-slate-700">Submitted File</p>
                            <button
                                @click="downloadSubmission"
                                class="mt-1 inline-flex items-center rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200"
                            >
                                Download file: {{ props.submission.original_filename }}
                            </button>
                        </div>

                        <div v-if="props.submission.comments">
                            <p class="text-xs font-medium text-slate-700">Your Comments</p>
                            <p class="mt-1 text-sm text-slate-600">{{ props.submission.comments }}</p>
                        </div>

                        <div v-if="hasScore" class="rounded-md bg-emerald-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                                Graded
                            </p>
                            <div class="mt-2">
                                <p class="text-2xl font-bold text-emerald-900">
                                    {{ props.submission.score }}/{{ props.assignment.max_score }}
                                </p>
                                <p class="text-sm text-emerald-700">
                                    {{ props.submission.percentage }}%
                                </p>
                            </div>
                            <div v-if="props.submission.feedback" class="mt-3">
                                <p class="text-xs font-medium text-emerald-800">Feedback</p>
                                <p class="mt-1 whitespace-pre-line text-sm text-emerald-700">
                                    {{ props.submission.feedback }}
                                </p>
                            </div>
                            <p v-if="props.submission.graded_at" class="mt-2 text-xs text-emerald-600">
                                Graded on {{ props.submission.graded_at }}{{ props.submission.grader ? ` by ${props.submission.grader}` : "" }}
                            </p>
                        </div>
                    </div>

                    <div v-if="props.assignment.can_submit" class="mt-6 rounded-md border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Resubmit (optional)</p>
                                <p class="mt-1 text-xs text-slate-600">
                                    You can replace your file until the due date/time. If already graded, resubmission will reset the grade.
                                </p>
                            </div>
                            <button
                                type="button"
                                class="rounded-md bg-portal-navy px-3 py-2 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark"
                                @click="isResubmitting = !isResubmitting"
                            >
                                {{ isResubmitting ? "Cancel" : "Resubmit" }}
                            </button>
                        </div>

                        <form v-if="isResubmitting" @submit.prevent="submit" class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">
                                    New Assignment File <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input
                                        ref="fileInput"
                                        type="file"
                                        @change="handleFileChange"
                                        :accept="props.assignment.allowed_file_types?.map((type) => `.${type}`).join(',')"
                                        class="hidden"
                                    />
                                    <button
                                        type="button"
                                        @click="selectFile"
                                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                    >
                                        Choose File
                                    </button>
                                    <span v-if="form.file" class="ml-3 text-sm text-slate-600">
                                        {{ form.file.name }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">
                                    Allowed types: {{ allowedTypesLabel }}
                                    <br />
                                    Max size: {{ maxFileSizeMB }} MB
                                </p>
                                <p v-if="form.errors.file" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.file }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">
                                    Comments (optional)
                                </label>
                                <textarea
                                    v-model="form.comments"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                ></textarea>
                                <p v-if="form.errors.comments" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.comments }}
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.file"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Submitting...</span>
                                    <span v-else>Submit New File</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div v-if="!props.submission && props.assignment.can_submit" class="portal-card p-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Submit Assignment
                    </p>
                    <p class="mt-1 text-sm text-slate-600">
                        Upload your assignment file before the due date
                    </p>

                    <form @submit.prevent="submit" class="mt-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">
                                Assignment File <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    @change="handleFileChange"
                                    :accept="props.assignment.allowed_file_types?.map((type) => `.${type}`).join(',')"
                                    class="hidden"
                                />
                                <button
                                    type="button"
                                    @click="selectFile"
                                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                >
                                    Choose File
                                </button>
                                <span v-if="form.file" class="ml-3 text-sm text-slate-600">
                                    {{ form.file.name }}
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">
                                Allowed types: {{ allowedTypesLabel }}
                                <br />
                                Max size: {{ maxFileSizeMB }} MB
                            </p>
                            <p v-if="form.errors.file" class="mt-1 text-sm text-red-600">
                                {{ form.errors.file }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">
                                Comments (optional)
                            </label>
                            <textarea
                                v-model="form.comments"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                            ></textarea>
                            <p v-if="form.errors.comments" class="mt-1 text-sm text-red-600">
                                {{ form.errors.comments }}
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <Link
                                :href="route('student.assignments.index')"
                                class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing || !form.file"
                                class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark disabled:opacity-50"
                            >
                                <span v-if="form.processing">Submitting...</span>
                                <span v-else>Submit Assignment</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div v-else-if="!props.assignment.can_submit" class="portal-card p-6">
                    <div class="rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200">
                        <p class="text-sm text-amber-800">
                            This assignment is no longer accepting submissions.
                            {{ props.assignment.is_overdue ? "The due date has passed." : "The assignment has been closed." }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
