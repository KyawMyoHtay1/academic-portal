<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, onBeforeUnmount, ref, watch } from "vue";
import debounce from "lodash/debounce";

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
    overview: {
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
const sortBy = ref(props.filters.sort_by || "requested_at");
const sortDir = ref(props.filters.sort_dir || "desc");
const dateFrom = ref(props.filters.date_from || "");
const dateTo = ref(props.filters.date_to || "");
const quickViewEnrollment = ref(null);

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

const hasActiveFilters = computed(
    () =>
        query.value.trim() !== "" ||
        activeStatus.value !== "pending" ||
        dateFrom.value !== "" ||
        dateTo.value !== "" ||
        sortBy.value !== "requested_at" ||
        sortDir.value !== "desc"
);

const activeFilterChips = computed(() => {
    const chips = [];
    if (activeStatus.value !== "pending") {
        chips.push({
            key: "status",
            label: `Status: ${activeStatus.value}`,
        });
    }
    if (query.value.trim() !== "") {
        chips.push({
            key: "search",
            label: `Search: ${query.value.trim()}`,
        });
    }
    if (dateFrom.value !== "" || dateTo.value !== "") {
        chips.push({
            key: "date_range",
            label: `Date: ${dateFrom.value || "Any"} to ${dateTo.value || "Any"}`,
        });
    }
    return chips;
});

const applyFilters = () => {
    router.get(
        route("admin.enrollments.index"),
        {
            status: activeStatus.value,
            search: query.value || undefined,
            sort_by: sortBy.value,
            sort_dir: sortDir.value,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const debouncedApplyFilters = debounce(() => {
    applyFilters();
}, 300);

watch(
    () => activeStatus.value,
    () => {
        applyFilters();
    }
);

watch(
    () => query.value,
    () => {
        debouncedApplyFilters();
    }
);

watch(
    () => [sortBy.value, sortDir.value, dateFrom.value, dateTo.value],
    () => {
        applyFilters();
    }
);

onBeforeUnmount(() => {
    debouncedApplyFilters.cancel();
});

const toggleSort = (column) => {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === "asc" ? "desc" : "asc";
        return;
    }
    sortBy.value = column;
    sortDir.value = column === "requested_at" ? "desc" : "asc";
};

const sortLabel = (column) => {
    if (sortBy.value !== column) return "sort";
    return sortDir.value === "asc" ? "asc" : "desc";
};

const clearFilters = () => {
    activeStatus.value = "pending";
    query.value = "";
    sortBy.value = "requested_at";
    sortDir.value = "desc";
    dateFrom.value = "";
    dateTo.value = "";
};

const removeFilterChip = (key) => {
    if (key === "status") {
        activeStatus.value = "pending";
        return;
    }
    if (key === "search") {
        query.value = "";
        return;
    }
    if (key === "date_range") {
        dateFrom.value = "";
        dateTo.value = "";
    }
};

const exportUrl = (format) =>
    route("admin.enrollments.export", {
        format,
        status: activeStatus.value,
        search: query.value || undefined,
        sort_by: sortBy.value,
        sort_dir: sortDir.value,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    });

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

const openQuickView = (enrollment) => {
    quickViewEnrollment.value = enrollment;
};

const closeQuickView = () => {
    quickViewEnrollment.value = null;
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
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="portal-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Total Enrollments
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ overview.total ?? 0 }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                            Approvals Today
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ overview.approvals_today ?? 0 }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-blue-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">
                            Approvals This Week
                        </p>
                        <p class="mt-2 text-2xl font-bold text-blue-900">
                            {{ overview.approvals_week ?? 0 }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-rose-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">
                            Rejection Rate
                        </p>
                        <p class="mt-2 text-2xl font-bold text-rose-900">
                            {{ overview.rejection_rate ?? 0 }}%
                        </p>
                    </div>
                </div>

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
                            <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto sm:justify-end">
                                <div class="relative w-full sm:w-80">
                                    <input
                                        v-model="query"
                                        type="text"
                                        placeholder="Search student, course, programme..."
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
                                <input
                                    v-model="dateFrom"
                                    type="date"
                                    class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-40"
                                    title="From date"
                                />
                                <input
                                    v-model="dateTo"
                                    type="date"
                                    class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-40"
                                    title="To date"
                                />
                                <a
                                    :href="exportUrl('csv')"
                                    class="rounded-md border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100"
                                >
                                    Export CSV
                                </a>
                                <a
                                    :href="exportUrl('pdf')"
                                    class="rounded-md border border-blue-300 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                >
                                    Export PDF
                                </a>
                                <button
                                    v-if="hasActiveFilters"
                                    type="button"
                                    class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                    @click="clearFilters"
                                >
                                    Clear all
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="activeFilterChips.length > 0"
                        class="mt-4 flex flex-wrap items-center gap-2"
                    >
                        <span
                            v-for="chip in activeFilterChips"
                            :key="chip.key"
                            class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"
                        >
                            {{ chip.label }}
                            <button
                                type="button"
                                class="rounded px-1 text-slate-500 hover:bg-slate-200"
                                @click="removeFilterChip(chip.key)"
                            >
                                x
                            </button>
                        </span>
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
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 hover:text-slate-900"
                                            @click="toggleSort('student_name')"
                                        >
                                            Student
                                            <span class="text-[10px] text-slate-400">{{ sortLabel("student_name") }}</span>
                                        </button>
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 hover:text-slate-900"
                                            @click="toggleSort('course_code')"
                                        >
                                            Course
                                            <span class="text-[10px] text-slate-400">{{ sortLabel("course_code") }}</span>
                                        </button>
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 hover:text-slate-900"
                                            @click="toggleSort('programme')"
                                        >
                                            Programme
                                            <span class="text-[10px] text-slate-400">{{ sortLabel("programme") }}</span>
                                        </button>
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 hover:text-slate-900"
                                            @click="toggleSort('status')"
                                        >
                                            Status
                                            <span class="text-[10px] text-slate-400">{{ sortLabel("status") }}</span>
                                        </button>
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 hover:text-slate-900"
                                            @click="toggleSort('requested_at')"
                                        >
                                            Requested On
                                            <span class="text-[10px] text-slate-400">{{ sortLabel("requested_at") }}</span>
                                        </button>
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
                                                    credits |
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
                                            <button
                                                type="button"
                                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                                @click="openQuickView(enrollment)"
                                            >
                                                Quick view
                                            </button>
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

                <div
                    v-if="quickViewEnrollment"
                    class="fixed inset-0 z-40 flex"
                    role="dialog"
                    aria-modal="true"
                >
                    <button
                        type="button"
                        class="absolute inset-0 bg-slate-900/40"
                        @click="closeQuickView"
                    ></button>
                    <div
                        class="relative ml-auto h-full w-full max-w-md overflow-y-auto border-l border-slate-200 bg-white p-5 shadow-xl"
                    >
                        <div class="mb-4 flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Quick View
                                </p>
                                <h3 class="mt-1 text-lg font-semibold text-slate-900">
                                    Enrollment #{{ quickViewEnrollment.enrollment_id }}
                                </h3>
                            </div>
                            <button
                                type="button"
                                class="rounded-md border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                @click="closeQuickView"
                            >
                                Close
                            </button>
                        </div>

                        <div class="space-y-4 text-sm">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Student
                                </p>
                                <p class="mt-1 font-semibold text-slate-900">
                                    {{ quickViewEnrollment.student_name }}
                                </p>
                                <p class="text-slate-600">{{ quickViewEnrollment.student_no }}</p>
                                <p class="text-slate-500">{{ quickViewEnrollment.student_email }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Programme: {{ quickViewEnrollment.programme || "-" }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Course
                                </p>
                                <p class="mt-1 font-semibold text-slate-900">
                                    {{ quickViewEnrollment.course_code }} - {{ quickViewEnrollment.course_title }}
                                </p>
                                <p class="text-slate-600">
                                    Semester: {{ quickViewEnrollment.semester }} | Credits: {{ quickViewEnrollment.credits }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Status
                                </p>
                                <span
                                    class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize"
                                    :class="getStatusBadgeClass(quickViewEnrollment.status)"
                                >
                                    {{
                                        quickViewEnrollment.status === "withdrawal_pending"
                                            ? "Withdrawal Pending"
                                            : quickViewEnrollment.status
                                    }}
                                </span>
                                <p class="mt-2 text-xs text-slate-500">
                                    Requested on: {{ formatDate(quickViewEnrollment.requested_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
