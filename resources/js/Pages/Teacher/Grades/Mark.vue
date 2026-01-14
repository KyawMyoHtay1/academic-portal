<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
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
});

const form = useForm({
    grades: props.students.map((student) => ({
        student_id: student.id,
        score: student.score ?? "",
    })),
});

const query = ref("");

const gradeEntries = computed(() => {
    const q = query.value.trim().toLowerCase();
    return form.grades
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
    const list = props.students ?? [];
    const withScores = list.filter((s) => s.score != null && s.score !== "");
    const scores = withScores.map((s) => parseFloat(s.score)).filter((n) => !isNaN(n));
    
    const avg = scores.length ? (scores.reduce((a, b) => a + b, 0) / scores.length).toFixed(1) : null;
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

const getGradeClass = (score) => {
    if (score == null || score === "") return "text-slate-400";
    const s = parseFloat(score);
    if (isNaN(s)) return "text-slate-400";
    if (s >= 70) return "text-emerald-700 font-semibold";
    if (s >= 60) return "text-blue-700 font-semibold";
    if (s >= 50) return "text-amber-700 font-semibold";
    return "text-red-700 font-semibold";
};

const getGradeBadge = (score) => {
    if (score == null || score === "") return null;
    const s = parseFloat(score);
    if (isNaN(s)) return null;
    if (s >= 70) return { label: "A", class: "bg-emerald-100 text-emerald-800" };
    if (s >= 60) return { label: "B", class: "bg-blue-100 text-blue-800" };
    if (s >= 50) return { label: "C", class: "bg-amber-100 text-amber-800" };
    return { label: "F", class: "bg-red-100 text-red-800" };
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
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <!-- Subject header + stats -->
                <div class="mb-6 grid gap-4 md:grid-cols-4">
                    <div class="portal-card p-5 md:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
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
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                            Graded
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ stats.graded }}/{{ stats.total }}
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">
                            {{ stats.total > 0 ? Math.round((stats.graded / stats.total) * 100) : 0 }}% complete
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-indigo-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">
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
                                                    Score (0 - 100)
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Grade
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="divide-y divide-slate-200 bg-white"
                                        >
                                            <tr
                                                v-for="entry in gradeEntries"
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
                                                                :alt="`Photo for ${entry.student.full_name ?? 'Student'}`"
                                                                class="h-full w-full object-cover"
                                                            />
                                                            <span
                                                                v-else
                                                                class="text-xs font-semibold text-slate-500"
                                                            >
                                                                {{
                                                                    (entry.student?.full_name ?? "?").charAt(0).toUpperCase()
                                                                }}
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="flex flex-col"
                                                        >
                                                            <span
                                                                class="text-sm font-medium text-slate-900"
                                                            >
                                                                {{ entry.student?.full_name ?? "Unknown" }}
                                                            </span>
                                                            <span
                                                                class="text-xs text-slate-500"
                                                            >
                                                                {{ entry.student?.student_no ?? "-" }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="whitespace-nowrap px-4 py-4 text-center"
                                                >
                                                    <div class="flex items-center justify-center gap-2">
                                                        <input
                                                            v-model="entry.record.score"
                                                            type="number"
                                                            min="0"
                                                            max="100"
                                                            step="0.01"
                                                            class="w-28 rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                                            :class="{
                                                                'border-emerald-300 bg-emerald-50': entry.record.score && parseFloat(entry.record.score) >= 70,
                                                                'border-blue-300 bg-blue-50': entry.record.score && parseFloat(entry.record.score) >= 60 && parseFloat(entry.record.score) < 70,
                                                                'border-amber-300 bg-amber-50': entry.record.score && parseFloat(entry.record.score) >= 50 && parseFloat(entry.record.score) < 60,
                                                                'border-red-300 bg-red-50': entry.record.score && parseFloat(entry.record.score) < 50,
                                                            }"
                                                        />
                                                        <span
                                                            v-if="entry.record.score && !isNaN(parseFloat(entry.record.score))"
                                                            class="text-xs font-medium"
                                                            :class="getGradeClass(entry.record.score)"
                                                        >
                                                            {{ parseFloat(entry.record.score).toFixed(1) }}%
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-4 text-center">
                                                    <span
                                                        v-if="getGradeBadge(entry.record.score)"
                                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                                        :class="getGradeBadge(entry.record.score).class"
                                                    >
                                                        {{ getGradeBadge(entry.record.score).label }}
                                                    </span>
                                                    <span v-else class="text-xs text-slate-400">
                                                        -
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr v-if="gradeEntries.length === 0">
                                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">
                                                    {{ query.trim() ? "No students match your search." : "No students found." }}
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
                                    <span v-else>Save Grades</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
