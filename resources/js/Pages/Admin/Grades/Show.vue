<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

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
const searchQuery = ref("");
const statusFilter = ref("pending");

const selectedGradeIds = ref([]);
const bulkAction = ref("approve");
const bulkReason = ref("");

const statusTabs = [
    { key: "pending", label: "Pending" },
    { key: "approved", label: "Approved" },
    { key: "rejected", label: "Rejected" },
    { key: "all", label: "All Reviewed" },
];

const approveForm = useForm({});
const rejectForm = useForm({ reason: "" });
const bulkForm = useForm({
    grade_ids: [],
    action: "approve",
    reason: "",
});

const filteredRows = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    let list = props.rows;

    if (statusFilter.value !== "all") {
        list = list.filter(
            (row) => row.grade && row.grade.status === statusFilter.value
        );
    } else {
        list = list.filter((row) => row.grade);
    }

    if (!q) {
        return list;
    }

    return list.filter((row) => {
        const name = (row.student?.full_name ?? "").toLowerCase();
        const studentNo = (row.student?.student_no ?? "").toLowerCase();

        return name.includes(q) || studentNo.includes(q);
    });
});

const visiblePendingGradeIds = computed(() =>
    filteredRows.value
        .filter((row) => row.grade?.status === "pending")
        .map((row) => row.grade.id)
);

const hasPendingInView = computed(() => visiblePendingGradeIds.value.length > 0);
const hasSelection = computed(() => selectedGradeIds.value.length > 0);
const selectedVisiblePendingCount = computed(() => {
    const selectedSet = new Set(selectedGradeIds.value);

    return visiblePendingGradeIds.value.filter((id) => selectedSet.has(id)).length;
});
const allVisiblePendingSelected = computed(
    () =>
        hasPendingInView.value &&
        selectedVisiblePendingCount.value === visiblePendingGradeIds.value.length
);

const isGradeSelected = (gradeId) => selectedGradeIds.value.includes(gradeId);

const toggleSelectGrade = (gradeId) => {
    if (isGradeSelected(gradeId)) {
        selectedGradeIds.value = selectedGradeIds.value.filter((id) => id !== gradeId);

        return;
    }

    selectedGradeIds.value = [...selectedGradeIds.value, gradeId];
};

const toggleSelectAllVisiblePending = () => {
    const selectedSet = new Set(selectedGradeIds.value);

    if (allVisiblePendingSelected.value) {
        visiblePendingGradeIds.value.forEach((id) => selectedSet.delete(id));
    } else {
        visiblePendingGradeIds.value.forEach((id) => selectedSet.add(id));
    }

    selectedGradeIds.value = Array.from(selectedSet);
};

const clearSelection = () => {
    selectedGradeIds.value = [];
};

const approve = (gradeId) => {
    approveForm.post(route("admin.grades.approve", gradeId), {
        preserveScroll: true,
    });
};

const reject = (gradeId) => {
    rejectForm.reason = (rejectReason.value[gradeId] ?? "").trim();
    rejectForm.post(route("admin.grades.reject", gradeId), {
        preserveScroll: true,
        onFinish: () => {
            rejectForm.reset("reason");
        },
    });
};

const submitBulkReview = () => {
    if (!hasSelection.value) {
        return;
    }

    bulkForm.grade_ids = [...selectedGradeIds.value];
    bulkForm.action = bulkAction.value;
    bulkForm.reason = bulkAction.value === "reject" ? bulkReason.value.trim() : "";

    bulkForm.post(route("admin.grades.bulk-review"), {
        preserveScroll: true,
        onSuccess: () => {
            selectedGradeIds.value = [];
            bulkReason.value = "";
        },
    });
};

watch([statusFilter, searchQuery], () => {
    const visibleSet = new Set(visiblePendingGradeIds.value);
    selectedGradeIds.value = selectedGradeIds.value.filter((id) => visibleSet.has(id));
});

const badgeClass = (status) => {
    if (status === "approved") return "bg-emerald-100 text-emerald-800";
    if (status === "rejected") return "bg-red-100 text-red-800";

    return "bg-amber-100 text-amber-800";
};

const exportUrl = (format) =>
    route("admin.grades.export", {
        subject: props.subject.id,
        format,
        status: statusFilter.value,
        search: searchQuery.value || undefined,
    });
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
                <div class="flex items-center gap-2">
                    <a
                        :href="exportUrl('csv')"
                        class="rounded-md border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 shadow-sm hover:bg-emerald-100"
                    >
                        Export CSV
                    </a>
                    <a
                        :href="exportUrl('pdf')"
                        class="rounded-md border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 shadow-sm hover:bg-blue-100"
                    >
                        Export PDF
                    </a>
                    <Link
                        :href="route('admin.grades.index')"
                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                    >
                        Back
                    </Link>
                </div>
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

                    <div
                        class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                    >
                        <div class="space-y-2">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Student Grades
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Use the tabs to switch between pending,
                                approved, and rejected grades. Only pending
                                grades can be approved or rejected.
                            </p>
                            <div class="inline-flex flex-wrap gap-2">
                                <button
                                    v-for="tab in statusTabs"
                                    :key="tab.key"
                                    type="button"
                                    class="rounded-full px-3 py-1 text-xs font-semibold transition"
                                    :class="
                                        statusFilter === tab.key
                                            ? 'bg-portal-navy text-white shadow'
                                            : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                    "
                                    @click="statusFilter = tab.key"
                                >
                                    {{ tab.label }}
                                </button>
                            </div>
                        </div>
                        <div class="w-full sm:w-80">
                            <label
                                class="block text-xs font-medium text-slate-600"
                                >Search Students</label
                            >
                            <input
                                v-model="searchQuery"
                                type="search"
                                placeholder="Search by student name or number..."
                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>
                    </div>

                    <div
                        class="mb-4 rounded-md border border-amber-200 bg-amber-50 p-4"
                    >
                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-amber-800"
                                >
                                    Bulk Review
                                </p>
                                <p class="mt-1 text-sm text-amber-900">
                                    Select pending grades and approve or reject
                                    in one action.
                                </p>
                                <p class="mt-1 text-xs text-amber-800">
                                    {{ selectedGradeIds.length }} selected
                                    {{
                                        hasPendingInView
                                            ? `(${selectedVisiblePendingCount}/${visiblePendingGradeIds.length} in view)`
                                            : ""
                                    }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="rounded-md border border-amber-300 bg-white px-3 py-2 text-xs font-semibold text-amber-800 shadow-sm hover:bg-amber-100 disabled:opacity-50"
                                :disabled="!hasPendingInView"
                                @click="toggleSelectAllVisiblePending"
                            >
                                {{
                                    allVisiblePendingSelected
                                        ? "Unselect visible pending"
                                        : "Select visible pending"
                                }}
                            </button>
                        </div>

                        <div
                            class="mt-3 flex flex-col gap-3 lg:flex-row lg:items-center"
                        >
                            <select
                                v-model="bulkAction"
                                class="rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy lg:w-44"
                            >
                                <option value="approve">Approve selected</option>
                                <option value="reject">Reject selected</option>
                            </select>

                            <input
                                v-if="bulkAction === 'reject'"
                                v-model="bulkReason"
                                type="text"
                                placeholder="Shared rejection reason (required)"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy lg:max-w-md"
                            />

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-portal-navy-dark disabled:opacity-50"
                                    :disabled="
                                        bulkForm.processing ||
                                        !hasSelection ||
                                        (bulkAction === 'reject' && !bulkReason.trim())
                                    "
                                    @click="submitBulkReview"
                                >
                                    Apply to Selected
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-100 disabled:opacity-50"
                                    :disabled="!hasSelection"
                                    @click="clearSelection"
                                >
                                    Clear Selection
                                </button>
                            </div>
                        </div>

                        <p
                            v-if="bulkAction === 'reject'"
                            class="mt-2 text-[11px] text-amber-800"
                        >
                            Rejection reason is required when bulk rejecting.
                        </p>
                        <p
                            v-if="bulkForm.errors.reason"
                            class="mt-2 text-xs text-red-600"
                        >
                            {{ bulkForm.errors.reason }}
                        </p>
                        <p
                            v-if="bulkForm.errors.grade_ids"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ bulkForm.errors.grade_ids }}
                        </p>
                    </div>

                    <div class="overflow-hidden rounded-md border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="w-12 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        <input
                                            v-if="hasPendingInView"
                                            type="checkbox"
                                            :checked="allVisiblePendingSelected"
                                            class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                            @change="toggleSelectAllVisiblePending"
                                        />
                                    </th>
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
                                <tr
                                    v-for="row in filteredRows"
                                    :key="row.student.id"
                                    class="hover:bg-slate-50"
                                >
                                    <td class="px-4 py-3 text-center">
                                        <input
                                            v-if="row.grade?.status === 'pending'"
                                            type="checkbox"
                                            :checked="isGradeSelected(row.grade.id)"
                                            class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                            @change="toggleSelectGrade(row.grade.id)"
                                        />
                                        <span v-else class="text-xs text-slate-300">-</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-900">
                                        <div class="font-medium">{{ row.student.full_name }}</div>
                                        <div class="text-xs text-slate-500">{{ row.student.student_no }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-slate-900">
                                        <span v-if="row.grade?.score !== null && row.grade?.score !== undefined">
                                            {{ Number(row.grade.score).toFixed(2) }}
                                        </span>
                                        <span v-else class="text-slate-400">-</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            v-if="row.grade?.status"
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="badgeClass(row.grade.status)"
                                        >
                                            {{ row.grade.status }}
                                        </span>
                                        <span v-else class="text-xs text-slate-400">-</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        <div>{{ row.grade?.graded_by ?? "-" }}</div>
                                        <div class="text-xs text-slate-500">
                                            {{ row.grade?.submitted_at ?? "" }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div
                                            v-if="row.grade && row.grade.status === 'pending'"
                                            class="flex flex-col items-end gap-2"
                                        >
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
                                                    :disabled="approveForm.processing || rejectForm.processing || !(rejectReason[row.grade.id] ?? '').trim()"
                                                    @click="reject(row.grade.id)"
                                                >
                                                    Reject
                                                </button>
                                            </div>
                                            <input
                                                v-model="rejectReason[row.grade.id]"
                                                type="text"
                                                placeholder="Rejection reason (required)"
                                                required
                                                class="w-72 rounded-md border-slate-300 text-xs shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                            />
                                            <p class="text-[11px] text-slate-500">
                                                Reason is required to reject.
                                            </p>
                                            <p
                                                v-if="rejectForm.errors.reason"
                                                class="text-[11px] text-red-600"
                                            >
                                                {{ rejectForm.errors.reason }}
                                            </p>
                                        </div>
                                        <div v-else class="text-xs text-slate-500">
                                            <div v-if="row.grade?.reviewed_by">
                                                Reviewed by {{ row.grade.reviewed_by }}
                                            </div>
                                            <div v-if="row.grade?.reviewed_at">
                                                {{ row.grade.reviewed_at }}
                                            </div>
                                            <div
                                                v-if="row.grade?.rejection_reason"
                                                class="text-red-700"
                                            >
                                                Reason: {{ row.grade.rejection_reason }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredRows.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{
                                            searchQuery.trim()
                                                ? "No students match your search."
                                                : "No students found."
                                        }}
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
