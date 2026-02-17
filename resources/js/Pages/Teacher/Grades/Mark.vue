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
});

const form = useForm({
    grades: props.students.map((student) => ({
        student_id: student.id,
        score: student.score ?? "",
    })),
});

const query = ref("");
const expandedStudents = ref(new Set());
const showSubmitModal = ref(false);
const selectedStudent = ref(null);
const submitFinalForm = useForm({
    score: "",
    use_computed: false,
});

const toggleStudentExpansion = (studentId) => {
    if (expandedStudents.value.has(studentId)) {
        expandedStudents.value.delete(studentId);
    } else {
        expandedStudents.value.add(studentId);
    }
};

const openSubmitModal = (student) => {
    selectedStudent.value = student;
    submitFinalForm.score = student.computed_grade?.toFixed(2) || "";
    submitFinalForm.use_computed = student.computed_grade !== null;
    showSubmitModal.value = true;
};

const closeSubmitModal = () => {
    showSubmitModal.value = false;
    selectedStudent.value = null;
    submitFinalForm.reset();
};

const submitFinalGrade = () => {
    if (!selectedStudent.value) return;

    submitFinalForm.post(
        route("teacher.grades.submit-final", {
            subject: props.subject.id,
            student: selectedStudent.value.id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                closeSubmitModal();
            },
        },
    );
};

const hasNumericScore = (value) =>
    value !== null &&
    value !== undefined &&
    value !== "" &&
    !Number.isNaN(parseFloat(value));

const hasFinalScore = (student) =>
    student?.score !== null &&
    student?.score !== undefined &&
    student?.score !== "";

const gradeEntries = computed(() => {
    const q = query.value.trim().toLowerCase();
    return form.grades
        .map((record) => {
            const student = props.students.find(
                (s) => s.id === record.student_id,
            );
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
    const list = props.students ?? [];
    const withScores = list.filter((s) => s.score != null && s.score !== "");
    const scores = withScores
        .map((s) => parseFloat(s.score))
        .filter((n) => !isNaN(n));

    const avg = scores.length
        ? (scores.reduce((a, b) => a + b, 0) / scores.length).toFixed(1)
        : null;
    const max = scores.length ? Math.max(...scores).toFixed(1) : null;
    const min = scores.length ? Math.min(...scores).toFixed(1) : null;

    return {
        total: list.length,
        graded: withScores.length,
        ungraded: list.length - withScores.length,
        average: avg,
        highest: max,
        lowest: min,
    };
});

const reviewBadge = (status) => {
    if (status === "draft")
        return { label: "Draft", class: "bg-slate-100 text-slate-700" };
    if (status === "approved")
        return { label: "Approved", class: "bg-emerald-100 text-emerald-800" };
    if (status === "rejected")
        return { label: "Rejected", class: "bg-red-100 text-red-800" };
    if (status === "pending")
        return {
            label: "Pending review",
            class: "bg-amber-100 text-amber-800",
        };
    return null;
};

const getGradeClass = (score) => {
    if (score == null || score === "") return "text-slate-400";
    const s = parseFloat(score);
    if (isNaN(s)) return "text-slate-400";
    // Grading scale: A: 80-100, B: 70-79, C: 60-69, D: 50-59, E: 40-49, F: 0-39
    if (s >= 80) return "text-emerald-700 font-semibold";
    if (s >= 70) return "text-blue-700 font-semibold";
    if (s >= 60) return "text-amber-700 font-semibold";
    if (s >= 50) return "text-yellow-700 font-semibold";
    if (s >= 40) return "text-orange-700 font-semibold";
    if (s >= 0) return "text-red-700 font-semibold";
    return "text-red-700 font-semibold";
};

const getGradeBadge = (score) => {
    if (score == null || score === "") return null;
    const s = parseFloat(score);
    if (isNaN(s)) return null;
    // Grading scale: A: 80-100, B: 70-79, C: 60-69, D: 50-59, E: 40-49, F: 0-39
    if (s >= 80)
        return { label: "A", class: "bg-emerald-100 text-emerald-800" };
    if (s >= 70) return { label: "B", class: "bg-blue-100 text-blue-800" };
    if (s >= 60) return { label: "C", class: "bg-amber-100 text-amber-800" };
    if (s >= 50) return { label: "D", class: "bg-yellow-100 text-yellow-800" };
    if (s >= 40) return { label: "E", class: "bg-orange-100 text-orange-800" };
    if (s >= 0) return { label: "F", class: "bg-red-100 text-red-800" };
    return null;
};

const submit = () => {
    form.post(route("teacher.grades.store", props.subject.id));
};
</script>

<template>
    <Head title="Enter Grades" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'Grades',
                            href: route('teacher.grades.index'),
                        },
                        { label: props.subject.title },
                    ]"
                />
            </div>
        </template>

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Enter Grades
                </h2>
                <Link
                    :href="route('teacher.grades.index')"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Back to Subjects
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                            {{ subject.course_code }} -
                            {{ subject.course_title }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                        >
                            Graded
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ stats.graded }}/{{ stats.total }}
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">
                            {{
                                stats.total > 0
                                    ? Math.round(
                                          (stats.graded / stats.total) * 100,
                                      )
                                    : 0
                            }}% complete
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-indigo-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-indigo-700"
                        >
                            Average
                        </p>
                        <p class="mt-2 text-2xl font-bold text-indigo-900">
                            {{ stats.average ?? "-" }}
                        </p>
                        <p class="mt-1 text-xs text-indigo-700">
                            {{ stats.highest ? `High: ${stats.highest}` : "" }}
                            {{ stats.lowest ? `Low: ${stats.lowest}` : "" }}
                        </p>
                    </div>
                </div>

                <div class="portal-card p-6">
                    <div class="mb-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Student Grades
                                </label>
                                <p class="mt-1 text-xs text-slate-500">
                                    Enter scores (0-100) for each student
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
                            <!-- Students Grades Table -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 mb-3"
                                >
                                    Student Grades
                                </label>

                                <div
                                    v-if="students.length === 0"
                                    class="rounded-md border border-slate-200 bg-slate-50 p-4 text-center text-sm text-slate-500"
                                >
                                    No students enrolled in this course.
                                </div>

                                <div
                                    v-else
                                    class="overflow-x-auto rounded-md border border-slate-200"
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
                                                    Score (0 - 100)
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Grade
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Review status
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="divide-y divide-slate-200 bg-white"
                                        >
                                            <template
                                                v-for="entry in gradeEntries"
                                                :key="entry.record.student_id"
                                            >
                                                <tr
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
                                                                    v-if="
                                                                        entry
                                                                            .student
                                                                            ?.photo
                                                                    "
                                                                    :src="`/storage/${entry.student.photo}`"
                                                                    :alt="`Photo for ${entry.student.full_name ?? 'Student'}`"
                                                                    class="h-full w-full object-cover"
                                                                />
                                                                <span
                                                                    v-else
                                                                    class="text-xs font-semibold text-slate-500"
                                                                >
                                                                    {{
                                                                        (
                                                                            entry
                                                                                .student
                                                                                ?.full_name ??
                                                                            "?"
                                                                        )
                                                                            .charAt(
                                                                                0,
                                                                            )
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
                                                                        entry
                                                                            .student
                                                                            ?.full_name ??
                                                                        "Unknown"
                                                                    }}
                                                                </span>
                                                                <span
                                                                    class="text-xs text-slate-500"
                                                                >
                                                                    {{
                                                                        entry
                                                                            .student
                                                                            ?.student_no ??
                                                                        "-"
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap px-4 py-4 text-center"
                                                    >
                                                        <div
                                                            class="flex items-center justify-center gap-2"
                                                        >
                                                            <input
                                                                v-model="
                                                                    entry.record
                                                                        .score
                                                                "
                                                                type="number"
                                                                min="0"
                                                                max="100"
                                                                step="0.01"
                                                                class="w-28 rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                                                :class="{
                                                                    'border-emerald-300 bg-emerald-50':
                                                                        hasNumericScore(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) >= 80,
                                                                    'border-blue-300 bg-blue-50':
                                                                        hasNumericScore(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) >=
                                                                            70 &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) < 80,
                                                                    'border-amber-300 bg-amber-50':
                                                                        hasNumericScore(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) >=
                                                                            60 &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) < 70,
                                                                    'border-yellow-300 bg-yellow-50':
                                                                        hasNumericScore(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) >=
                                                                            50 &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) < 60,
                                                                    'border-orange-300 bg-orange-50':
                                                                        hasNumericScore(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) >=
                                                                            40 &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) < 50,
                                                                    'border-red-300 bg-red-50':
                                                                        hasNumericScore(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) >=
                                                                            0 &&
                                                                        parseFloat(
                                                                            entry
                                                                                .record
                                                                                .score,
                                                                        ) < 40,
                                                                }"
                                                            />
                                                            <span
                                                                v-if="
                                                                    hasNumericScore(
                                                                        entry
                                                                            .record
                                                                            .score,
                                                                    )
                                                                "
                                                                class="text-xs font-medium"
                                                                :class="
                                                                    getGradeClass(
                                                                        entry
                                                                            .record
                                                                            .score,
                                                                    )
                                                                "
                                                            >
                                                                {{
                                                                    parseFloat(
                                                                        entry
                                                                            .record
                                                                            .score,
                                                                    ).toFixed(
                                                                        1,
                                                                    )
                                                                }}%
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap px-4 py-4 text-center"
                                                    >
                                                        <span
                                                            v-if="
                                                                getGradeBadge(
                                                                    entry.record
                                                                        .score,
                                                                )
                                                            "
                                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                                            :class="
                                                                getGradeBadge(
                                                                    entry.record
                                                                        .score,
                                                                ).class
                                                            "
                                                        >
                                                            {{
                                                                getGradeBadge(
                                                                    entry.record
                                                                        .score,
                                                                ).label
                                                            }}
                                                        </span>
                                                        <span
                                                            v-else
                                                            class="text-xs text-slate-400"
                                                        >
                                                            -
                                                        </span>
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap px-4 py-4 text-center"
                                                    >
                                                        <div
                                                            v-if="
                                                                reviewBadge(
                                                                    entry
                                                                        .student
                                                                        ?.status,
                                                                )
                                                            "
                                                            class="flex flex-col items-center gap-1"
                                                        >
                                                            <span
                                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                                                :class="
                                                                    reviewBadge(
                                                                        entry
                                                                            .student
                                                                            .status,
                                                                    ).class
                                                                "
                                                            >
                                                                {{
                                                                    reviewBadge(
                                                                        entry
                                                                            .student
                                                                            .status,
                                                                    ).label
                                                                }}
                                                            </span>
                                                            <span
                                                                v-if="
                                                                    entry
                                                                        .student
                                                                        ?.status ===
                                                                        'rejected' &&
                                                                    entry
                                                                        .student
                                                                        ?.rejection_reason
                                                                "
                                                                class="max-w-[14rem] truncate text-[11px] text-red-700"
                                                                :title="
                                                                    entry
                                                                        .student
                                                                        .rejection_reason
                                                                "
                                                            >
                                                                {{
                                                                    entry
                                                                        .student
                                                                        .rejection_reason
                                                                }}
                                                            </span>
                                                        </div>
                                                        <span
                                                            v-else
                                                            class="text-xs text-slate-400"
                                                            >-</span
                                                        >
                                                    </td>
                                                    <td
                                                        class="px-4 py-4 text-center"
                                                    >
                                                        <div
                                                            class="flex flex-col items-center justify-center gap-2 sm:flex-row sm:flex-wrap"
                                                        >
                                                            <button
                                                                v-if="
                                                                    entry
                                                                        .student
                                                                        ?.has_assignments
                                                                "
                                                                type="button"
                                                                @click="
                                                                    toggleStudentExpansion(
                                                                        entry
                                                                            .student
                                                                            .id,
                                                                    )
                                                                "
                                                                class="w-full rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 sm:w-auto"
                                                            >
                                                                <span
                                                                    v-if="
                                                                        expandedStudents.has(
                                                                            entry
                                                                                .student
                                                                                .id,
                                                                        )
                                                                    "
                                                                >
                                                                    Hide
                                                                    Assignments
                                                                </span>
                                                                <span v-else>
                                                                    View
                                                                    Assignments
                                                                </span>
                                                            </button>
                                                            <button
                                                                v-if="
                                                                    entry
                                                                        .student
                                                                        ?.computed_grade !==
                                                                        null ||
                                                                    hasFinalScore(
                                                                        entry.student,
                                                                    )
                                                                "
                                                                type="button"
                                                                @click="
                                                                    openSubmitModal(
                                                                        entry.student,
                                                                    )
                                                                "
                                                                class="w-full rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-indigo-700 sm:w-auto"
                                                            >
                                                                Submit Final
                                                                Grade
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!-- Assignment Breakdown Row -->
                                                <tr
                                                    v-if="
                                                        expandedStudents.has(
                                                            entry.student?.id,
                                                        ) &&
                                                        entry.student
                                                            ?.has_assignments
                                                    "
                                                    class="bg-slate-50"
                                                >
                                                    <td
                                                        colspan="5"
                                                        class="px-4 py-4"
                                                    >
                                                        <div class="space-y-3">
                                                            <div
                                                                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                                                            >
                                                                <h4
                                                                    class="text-sm font-semibold text-slate-900"
                                                                >
                                                                    Assignment
                                                                    Breakdown
                                                                </h4>
                                                                <div
                                                                    class="flex flex-wrap items-center gap-2 text-xs sm:gap-4"
                                                                >
                                                                    <span
                                                                        class="text-slate-600"
                                                                    >
                                                                        Computed
                                                                        Grade:
                                                                        <span
                                                                            v-if="
                                                                                entry
                                                                                    .student
                                                                                    ?.computed_grade !==
                                                                                null
                                                                            "
                                                                            class="ml-1 font-semibold"
                                                                            :class="
                                                                                getGradeClass(
                                                                                    entry
                                                                                        .student
                                                                                        .computed_grade,
                                                                                )
                                                                            "
                                                                        >
                                                                            {{
                                                                                entry.student.computed_grade.toFixed(
                                                                                    2,
                                                                                )
                                                                            }}%
                                                                        </span>
                                                                        <span
                                                                            v-else
                                                                            class="ml-1 text-slate-400"
                                                                        >
                                                                            Not
                                                                            available
                                                                        </span>
                                                                    </span>
                                                                    <span
                                                                        class="text-slate-600"
                                                                    >
                                                                        Graded:
                                                                        {{
                                                                            entry
                                                                                .student
                                                                                ?.graded_assignments
                                                                        }}/{{
                                                                            entry
                                                                                .student
                                                                                ?.total_assignments
                                                                        }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div
                                                                v-if="
                                                                    entry
                                                                        .student
                                                                        ?.assignment_breakdown
                                                                        ?.length >
                                                                    0
                                                                "
                                                                class="overflow-x-auto"
                                                            >
                                                                <table
                                                                    class="min-w-full divide-y divide-slate-200"
                                                                >
                                                                    <thead
                                                                        class="bg-white"
                                                                    >
                                                                        <tr>
                                                                            <th
                                                                                class="px-3 py-2 text-left text-xs font-medium text-slate-600"
                                                                            >
                                                                                Assignment
                                                                            </th>
                                                                            <th
                                                                                class="px-3 py-2 text-center text-xs font-medium text-slate-600"
                                                                            >
                                                                                Due
                                                                                Date
                                                                            </th>
                                                                            <th
                                                                                class="px-3 py-2 text-center text-xs font-medium text-slate-600"
                                                                            >
                                                                                Status
                                                                            </th>
                                                                            <th
                                                                                class="px-3 py-2 text-center text-xs font-medium text-slate-600"
                                                                            >
                                                                                Score
                                                                            </th>
                                                                            <th
                                                                                class="px-3 py-2 text-center text-xs font-medium text-slate-600"
                                                                            >
                                                                                Percentage
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody
                                                                        class="divide-y divide-slate-200 bg-white"
                                                                    >
                                                                        <tr
                                                                            v-for="assignment in entry
                                                                                .student
                                                                                .assignment_breakdown"
                                                                            :key="
                                                                                assignment.assignment_id
                                                                            "
                                                                        >
                                                                            <td
                                                                                class="px-3 py-2 text-sm text-slate-900"
                                                                            >
                                                                                {{
                                                                                    assignment.title
                                                                                }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-center text-xs text-slate-600"
                                                                            >
                                                                                {{
                                                                                    assignment.due_date
                                                                                }}
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-center"
                                                                            >
                                                                                <span
                                                                                    v-if="
                                                                                        assignment.graded
                                                                                    "
                                                                                    class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-800"
                                                                                >
                                                                                    Graded
                                                                                </span>
                                                                                <span
                                                                                    v-else-if="
                                                                                        assignment.submitted
                                                                                    "
                                                                                    class="inline-flex rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-800"
                                                                                >
                                                                                    Submitted
                                                                                </span>
                                                                                <span
                                                                                    v-else
                                                                                    class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600"
                                                                                >
                                                                                    Not
                                                                                    submitted
                                                                                </span>
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-center text-sm"
                                                                            >
                                                                                <span
                                                                                    v-if="
                                                                                        assignment.score !==
                                                                                        null
                                                                                    "
                                                                                    class="font-medium"
                                                                                    :class="
                                                                                        getGradeClass(
                                                                                            assignment.percentage,
                                                                                        )
                                                                                    "
                                                                                >
                                                                                    {{
                                                                                        assignment.score
                                                                                    }}/{{
                                                                                        assignment.max_score
                                                                                    }}
                                                                                </span>
                                                                                <span
                                                                                    v-else
                                                                                    class="text-slate-400"
                                                                                    >-</span
                                                                                >
                                                                            </td>
                                                                            <td
                                                                                class="px-3 py-2 text-center text-sm"
                                                                            >
                                                                                <span
                                                                                    v-if="
                                                                                        assignment.percentage !==
                                                                                        null
                                                                                    "
                                                                                    class="font-medium"
                                                                                    :class="
                                                                                        getGradeClass(
                                                                                            assignment.percentage,
                                                                                        )
                                                                                    "
                                                                                >
                                                                                    {{
                                                                                        assignment.percentage.toFixed(
                                                                                            1,
                                                                                        )
                                                                                    }}%
                                                                                </span>
                                                                                <span
                                                                                    v-else
                                                                                    class="text-slate-400"
                                                                                    >-</span
                                                                                >
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div
                                                                v-else
                                                                class="rounded-md bg-slate-50 p-3 text-center text-xs text-slate-500"
                                                            >
                                                                No assignments
                                                                found for this
                                                                subject.
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                            <tr
                                                v-if="gradeEntries.length === 0"
                                            >
                                                <td
                                                    colspan="5"
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
                                    v-if="form.errors.grades"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.grades }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
                                <Link
                                    :href="route('teacher.grades.index')"
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
                                    <span v-else>Save Draft Grades</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Submit Final Grade Modal -->
        <div
            v-if="showSubmitModal && selectedStudent"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0"
            >
                <div
                    class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity"
                    @click="closeSubmitModal"
                ></div>

                <span
                    class="hidden sm:inline-block sm:h-screen sm:align-middle"
                    aria-hidden="true"
                    >&#8203;</span
                >

                <div
                    class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle"
                >
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mt-3 text-center sm:mt-0 sm:text-left w-full"
                            >
                                <h3
                                    class="text-lg font-medium leading-6 text-slate-900"
                                    id="modal-title"
                                >
                                    Submit Final Grade
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <p class="text-sm text-slate-600">
                                            Student:
                                            <span class="font-semibold">{{
                                                selectedStudent.full_name
                                            }}</span>
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            {{ selectedStudent.student_no }}
                                        </p>
                                    </div>

                                    <div
                                        v-if="
                                            selectedStudent.computed_grade !==
                                            null
                                        "
                                        class="rounded-lg bg-indigo-50 p-3"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <span
                                                class="text-sm font-medium text-indigo-900"
                                            >
                                                Computed Grade from Assignments:
                                            </span>
                                            <span
                                                class="text-lg font-bold"
                                                :class="
                                                    getGradeClass(
                                                        selectedStudent.computed_grade,
                                                    )
                                                "
                                            >
                                                {{
                                                    selectedStudent.computed_grade.toFixed(
                                                        2,
                                                    )
                                                }}%
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-indigo-700">
                                            Based on
                                            {{
                                                selectedStudent.graded_assignments
                                            }}
                                            graded assignment(s)
                                        </p>
                                    </div>

                                    <div
                                        v-if="hasFinalScore(selectedStudent)"
                                        class="rounded-lg bg-emerald-50 p-3"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <span
                                                class="text-sm font-medium text-emerald-900"
                                            >
                                                Current Final Grade:
                                            </span>
                                            <span
                                                class="text-lg font-bold"
                                                :class="
                                                    getGradeClass(
                                                        selectedStudent.score,
                                                    )
                                                "
                                            >
                                                {{
                                                    Number(
                                                        selectedStudent.score,
                                                    ).toFixed(2)
                                                }}%
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            <span
                                                v-if="
                                                    reviewBadge(
                                                        selectedStudent.status,
                                                    )
                                                "
                                                class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold"
                                                :class="
                                                    reviewBadge(
                                                        selectedStudent.status,
                                                    ).class
                                                "
                                            >
                                                {{
                                                    reviewBadge(
                                                        selectedStudent.status,
                                                    ).label
                                                }}
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="flex items-center gap-2">
                                            <input
                                                v-model="
                                                    submitFinalForm.use_computed
                                                "
                                                type="checkbox"
                                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                :disabled="
                                                    selectedStudent.computed_grade ===
                                                    null
                                                "
                                            />
                                            <span
                                                class="text-sm text-slate-700"
                                            >
                                                Use computed grade from
                                                assignments
                                                <span
                                                    v-if="
                                                        selectedStudent.computed_grade ===
                                                        null
                                                    "
                                                    class="text-xs text-slate-400"
                                                >
                                                    (No graded assignments
                                                    available)
                                                </span>
                                            </span>
                                        </label>
                                    </div>

                                    <div v-if="!submitFinalForm.use_computed">
                                        <label
                                            for="final-score"
                                            class="block text-sm font-medium text-slate-700"
                                        >
                                            Enter Final Grade (0-100)
                                        </label>
                                        <input
                                            id="final-score"
                                            v-model="submitFinalForm.score"
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="0.01"
                                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{
                                                'border-red-300':
                                                    submitFinalForm.errors
                                                        .score,
                                            }"
                                        />
                                        <p
                                            v-if="submitFinalForm.errors.score"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ submitFinalForm.errors.score }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"
                    >
                        <button
                            type="button"
                            @click="submitFinalGrade"
                            :disabled="
                                submitFinalForm.processing ||
                                (!submitFinalForm.use_computed &&
                                    !hasNumericScore(submitFinalForm.score))
                            "
                            class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            <span v-if="submitFinalForm.processing"
                                >Submitting...</span
                            >
                            <span v-else>Submit for Approval</span>
                        </button>
                        <button
                            type="button"
                            @click="closeSubmitModal"
                            class="mt-3 inline-flex w-full justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-base font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
