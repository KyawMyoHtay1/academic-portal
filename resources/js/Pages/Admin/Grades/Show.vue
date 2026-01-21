<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    subject: {
        type: Object,
        required: true,
    },
    rows: {
        type: Array,
        required: true,
    },
});

const rejectReason = ref({});

const approveForm = useForm({});
const rejectForm = useForm({ reason: "" });

const approve = (gradeId) => {
    approveForm.post(route("admin.grades.approve", gradeId), {
        preserveScroll: true,
    });
};

const reject = (gradeId) => {
    rejectForm.reason = rejectReason.value[gradeId] ?? "";
    rejectForm.post(route("admin.grades.reject", gradeId), {
        preserveScroll: true,
        onFinish: () => {
            rejectForm.reset("reason");
        },
    });
};

const badgeClass = (status) => {
    if (status === "approved") return "bg-emerald-100 text-emerald-800";
    if (status === "rejected") return "bg-red-100 text-red-800";
    return "bg-amber-100 text-amber-800";
};
</script>

<template>
    <Head title="Review Grades" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Admin' },
                        { label: 'Grade Reviews', href: route('admin.grades.index') },
                        { label: subject.subject_code },
                    ]"
                />
            </div>
        </template>

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Review Grades
                </h2>
                <Link
                    :href="route('admin.grades.index')"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Back
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <div class="mb-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Subject
                        </p>
                        <p class="mt-2 text-lg font-bold text-slate-900">
                            {{ subject.subject_code }} - {{ subject.title }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ subject.course_code }} - {{ subject.course_title }}
                        </p>
                    </div>

                    <div class="overflow-hidden rounded-md border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Student
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Score
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Submitted by
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-for="row in rows" :key="row.student.id" class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm text-slate-900">
                                        <div class="font-medium">{{ row.student.full_name }}</div>
                                        <div class="text-xs text-slate-500">{{ row.student.student_no }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-slate-900">
                                        <span v-if="row.grade?.score !== null && row.grade?.score !== undefined">
                                            {{ Number(row.grade.score).toFixed(2) }}
                                        </span>
                                        <span v-else class="text-slate-400">—</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            v-if="row.grade?.status"
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="badgeClass(row.grade.status)"
                                        >
                                            {{ row.grade.status }}
                                        </span>
                                        <span v-else class="text-xs text-slate-400">—</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        <div>{{ row.grade?.graded_by ?? "—" }}</div>
                                        <div class="text-xs text-slate-500">
                                            {{ row.grade?.submitted_at ?? "" }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div v-if="row.grade && row.grade.status === 'pending'" class="flex flex-col items-end gap-2">
                                            <div class="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    class="rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:opacity-50"
                                                    :disabled="approveForm.processing || rejectForm.processing"
                                                    @click="approve(row.grade.id)"
                                                >
                                                    Approve
                                                </button>
                                                <button
                                                    type="button"
                                                    class="rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
                                                    :disabled="approveForm.processing || rejectForm.processing"
                                                    @click="reject(row.grade.id)"
                                                >
                                                    Reject
                                                </button>
                                            </div>
                                            <input
                                                v-model="rejectReason[row.grade.id]"
                                                type="text"
                                                placeholder="Optional rejection reason…"
                                                class="w-72 rounded-md border-slate-300 text-xs shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                            />
                                        </div>
                                        <div v-else class="text-xs text-slate-500">
                                            <div v-if="row.grade?.reviewed_by">
                                                Reviewed by {{ row.grade.reviewed_by }}
                                            </div>
                                            <div v-if="row.grade?.reviewed_at">
                                                {{ row.grade.reviewed_at }}
                                            </div>
                                            <div v-if="row.grade?.rejection_reason" class="text-red-700">
                                                Reason: {{ row.grade.rejection_reason }}
                                            </div>
                                        </div>
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

