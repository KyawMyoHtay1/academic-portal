<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps({
    enrollments: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    counts: {
        type: Object,
        default: () => ({}),
    },
});

const statusTabs = [
    { key: "all", label: "All" },
    { key: "pending", label: "Pending" },
    { key: "approved", label: "Approved" },
    { key: "rejected", label: "Rejected" },
    { key: "withdrawal_pending", label: "Withdrawal Pending" },
];

const activeStatus = ref(props.filters.status || "pending");
const query = ref(props.filters.search || "");

const stats = computed(() => ({
    total: props.counts.all ?? 0,
    pending: props.counts.pending ?? 0,
    approved: props.counts.approved ?? 0,
    rejected: props.counts.rejected ?? 0,
    withdrawal_pending: props.counts.withdrawal_pending ?? 0,
}));

// Client-side filtering on the current page (server handles main filtering)
const filteredEnrollments = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.enrollments?.data ?? [];
    if (!q) return list;
    return list.filter((e) => {
        const student = `${e.student_name ?? ""} ${e.student_no ?? ""} ${
            e.student_email ?? ""
        }`.toLowerCase();
        const course = `${e.course_code ?? ""} ${
            e.course_title ?? ""
        }`.toLowerCase();
        const programme = (e.programme ?? "").toLowerCase();
        return (
            student.includes(q) || course.includes(q) || programme.includes(q)
        );
    });
});

const applyFilters = () => {
    router.get(
        route("admin.enrollments.index"),
        {
            status: activeStatus.value,
            search: query.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

watch(
    () => activeStatus.value,
    () => {
        applyFilters();
    }
);

const clearSearch = () => {
    if (!query.value) return;
    query.value = "";
    applyFilters();
};

const approveEnrollment = (enrollmentId) => {
    if (!confirm("Are you sure you want to approve this enrollment?")) {
        return;
    }

    router.post(
        route("admin.enrollments.approve", enrollmentId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const rejectEnrollment = (enrollmentId) => {
    if (
        !confirm(
            "Are you sure you want to reject this enrollment? The student can reapply later."
        )
    ) {
        return;
    }

    router.post(
        route("admin.enrollments.reject", enrollmentId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const approveWithdrawal = (enrollmentId) => {
    if (
        !confirm(
            "Are you sure you want to approve this withdrawal? The student will be removed from the course."
        )
    ) {
        return;
    }

    router.post(
        route("admin.enrollments.approve-withdrawal", enrollmentId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const rejectWithdrawal = (enrollmentId) => {
    if (
        !confirm(
            "Are you sure you want to reject this withdrawal? The student will remain enrolled in the course."
        )
    ) {
        return;
    }

    router.post(
        route("admin.enrollments.reject-withdrawal", enrollmentId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-GB", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case "pending":
            return "bg-amber-100 text-amber-800";
        case "approved":
            return "bg-emerald-100 text-emerald-800";
        case "rejected":
            return "bg-red-100 text-red-800";
        case "withdrawal_pending":
            return "bg-indigo-100 text-indigo-800";
        default:
            return "bg-slate-100 text-slate-700";
    }
};
</script>

<template>
    <Head title="Enrollment Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Enrollment Management
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Enrollment Management' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Summary + controls -->
                <div class="portal-card p-5">
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Requests overview
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                View and manage course enrollment and withdrawal
                                requests.
                            </p>
                        </div>
                        <div class="flex flex-col gap-3 sm:items-end">
                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    v-for="tab in statusTabs"
                                    :key="tab.key"
                                    type="button"
                                    class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-slate-200 transition"
                                    :class="
                                        activeStatus === tab.key
                                            ? 'bg-portal-navy text-white ring-portal-navy'
                                            : 'bg-white text-slate-700 hover:bg-slate-50'
                                    "
                                    @click="activeStatus = tab.key"
                                >
                                    {{ tab.label }}
                                    <span
                                        class="ml-2 inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold text-white"
                                    >
                                        {{
                                            tab.key === 'all'
                                                ? stats.total
                                                : stats[tab.key] ?? 0
                                        }}
                                    </span>
                                </button>
                            </div>
                            <div class="relative w-full sm:w-80">
                                <input
                                    v-model="query"
                                    type="text"
                                    placeholder="Search student, course, programme…"
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                    @keyup.enter="applyFilters"
                                />
                                <button
                                    v-if="query"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="clearSearch"
                                >
                                    <span class="sr-only">Clear</span>
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrollments Table -->
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Enrollment Requests
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Showing
                                <span
                                    class="font-semibold text-slate-800"
                                    >{{ filteredEnrollments.length }}</span
                                >
                                of
                                <span
                                    class="font-semibold text-slate-800"
                                    >{{ enrollments.total }}</span
                                >
                                records
                            </p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
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
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Course
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Programme
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Requested On
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-if="filteredEnrollments.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{
                                            enrollments.total === 0
                                                ? "No enrollment records found."
                                                : "No enrollment records match your filters."
                                        }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="enrollment in filteredEnrollments"
                                    :key="enrollment.enrollment_id"
                                    class="bg-white transition-colors hover:bg-slate-50"
                                >
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-slate-100"
                                            >
                                                <img
                                                    v-if="
                                                        enrollment.student_photo
                                                    "
                                                    :src="`/storage/${enrollment.student_photo}`"
                                                    :alt="`Photo for ${enrollment.student_name}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{
                                                        enrollment.student_name
                                                            ?.charAt(0)
                                                            ?.toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-medium text-slate-900"
                                                >
                                                    {{
                                                        enrollment.student_name
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm text-slate-500"
                                                >
                                                    {{ enrollment.student_no }}
                                                </div>
                                                <div
                                                    class="text-xs text-slate-400"
                                                >
                                                    {{
                                                        enrollment.student_email
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-slate-100"
                                            >
                                                <img
                                                    v-if="
                                                        enrollment.course_photo
                                                    "
                                                    :src="`/storage/${enrollment.course_photo}`"
                                                    :alt="`Photo for ${enrollment.course_title}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{
                                                        enrollment.course_title
                                                            ?.charAt(0)
                                                            ?.toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-medium text-slate-900"
                                                >
                                                    {{ enrollment.course_code }}
                                                </div>
                                                <div
                                                    class="text-sm text-slate-600"
                                                >
                                                    {{
                                                        enrollment.course_title
                                                    }}
                                                </div>
                                                <div
                                                    class="text-xs text-slate-500"
                                                >
                                                    {{
                                                        enrollment.credits
                                                    }}
                                                    credits ·
                                                    {{ enrollment.semester }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ enrollment.programme || "-" }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm"
                                    >
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize"
                                            :class="
                                                getStatusBadgeClass(
                                                    enrollment.status
                                                )
                                            "
                                        >
                                            {{
                                                enrollment.status ===
                                                "withdrawal_pending"
                                                    ? "Withdrawal Pending"
                                                    : enrollment.status
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{
                                            formatDate(enrollment.requested_at)
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <template
                                                v-if="
                                                    enrollment.status ===
                                                    'pending'
                                                "
                                            >
                                                <button
                                                    @click="
                                                        approveEnrollment(
                                                            enrollment.enrollment_id
                                                        )
                                                    "
                                                    class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                                >
                                                    Approve
                                                </button>
                                                <button
                                                    @click="
                                                        rejectEnrollment(
                                                            enrollment.enrollment_id
                                                        )
                                                    "
                                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                >
                                                    Reject
                                                </button>
                                            </template>
                                            <template
                                                v-else-if="
                                                    enrollment.status ===
                                                    'withdrawal_pending'
                                                "
                                            >
                                                <button
                                                    @click="
                                                        approveWithdrawal(
                                                            enrollment.enrollment_id
                                                        )
                                                    "
                                                    class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                                >
                                                    Approve Withdrawal
                                                </button>
                                                <button
                                                    @click="
                                                        rejectWithdrawal(
                                                            enrollment.enrollment_id
                                                        )
                                                    "
                                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                >
                                                    Reject Withdrawal
                                                </button>
                                            </template>
                                            <span
                                                v-else
                                                class="text-xs text-slate-400"
                                            >
                                                No actions
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="border-t border-slate-200 px-4 py-3 sm:px-6"
                        v-if="enrollments.links?.length"
                    >
                        <Pagination :links="enrollments.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
