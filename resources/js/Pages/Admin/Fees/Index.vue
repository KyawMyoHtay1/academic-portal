<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps({
    fees: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    latePayments: {
        type: Array,
        default: () => [],
    },
    latePaymentsCount: {
        type: Number,
        default: 0,
    },
});

const status = ref(props.filters.status || "all");
const search = ref(props.filters.search || "");

const entries = computed(() => props.fees?.data ?? []);

const stats = computed(() => {
    const list = entries.value;
    const total = list.reduce((sum, f) => sum + parseFloat(f.amount || 0), 0);
    const paid = list
        .filter((f) => f.status === "paid")
        .reduce((sum, f) => sum + parseFloat(f.amount || 0), 0);
    const pending = list
        .filter((f) => f.status === "pending")
        .reduce((sum, f) => sum + parseFloat(f.amount || 0), 0);
    const paymentPending = list.filter((f) => f.status === "payment_pending").length;

    return {
        total: total.toFixed(2),
        paid: paid.toFixed(2),
        pending: pending.toFixed(2),
        totalCount: list.length,
        paidCount: list.filter((f) => f.status === "paid").length,
        pendingCount: list.filter((f) => f.status === "pending").length,
        paymentPendingCount: paymentPending,
    };
});

const applyFilters = () => {
    router.get(
        route("admin.fees.index"),
        {
            status: status.value,
            search: search.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    );
};

watch(status, () => {
    applyFilters();
});

const deleteFee = (feeId) => {
    if (
        !confirm(
            "Are you sure you want to delete this fee? This action cannot be undone."
        )
    ) {
        return;
    }

    router.delete(route("admin.fees.destroy", feeId), {
        preserveScroll: true,
    });
};

const getStatusBadgeClass = (status) => {
    if (status === "paid") {
        return "bg-emerald-100 text-emerald-800";
    } else if (status === "payment_pending") {
        return "bg-blue-100 text-blue-800";
    } else {
        return "bg-amber-100 text-amber-800";
    }
};

const approvePayment = (feeId) => {
    if (
        !confirm("Are you sure you want to approve this payment confirmation?")
    ) {
        return;
    }

    router.post(
        route("admin.fees.approve-payment", feeId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const rejectPayment = (feeId) => {
    if (
        !confirm(
            "Are you sure you want to reject this payment confirmation? The fee status will revert to pending."
        )
    ) {
        return;
    }

    router.post(
        route("admin.fees.reject-payment", feeId),
        {},
        {
            preserveScroll: true,
        }
    );
};
</script>

<script>
import Pagination from "@/Components/Pagination.vue";

export default {
    components: {
        Pagination,
    },
};
</script>

<template>
    <Head title="Fee Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Fee Management
                </h2>
                <Link
                    :href="route('admin.fees.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                >
                    Create Fee
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Fee Management' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Summary Stats -->
                <div class="mb-6 grid gap-4 md:grid-cols-5">
                    <div class="portal-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Total Fees
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            £{{ stats.total }}
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            {{ stats.totalCount }} fee(s)
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                            Paid
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            £{{ stats.paid }}
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">
                            {{ stats.paidCount }} paid
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-amber-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                            Pending
                        </p>
                        <p class="mt-2 text-2xl font-bold text-amber-900">
                            £{{ stats.pending }}
                        </p>
                        <p class="mt-1 text-xs text-amber-700">
                            {{ stats.pendingCount }} pending
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-blue-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">
                            Awaiting Approval
                        </p>
                        <p class="mt-2 text-2xl font-bold text-blue-900">
                            {{ stats.paymentPendingCount }}
                        </p>
                        <p class="mt-1 text-xs text-blue-700">
                            Need review
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-red-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-red-700">
                            Late Payments
                        </p>
                        <p class="mt-2 text-2xl font-bold text-red-900">
                            {{ latePaymentsCount }}
                        </p>
                        <p class="mt-1 text-xs text-red-700">
                            Overdue
                        </p>
                    </div>
                </div>

                <!-- Late Payments Alert -->
                <div
                    v-if="latePaymentsCount > 0"
                    class="mb-6 rounded-lg border-l-4 border-red-500 bg-red-50 p-4"
                >
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-red-400"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ latePaymentsCount }} Late Payment(s) Detected
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>
                                    The following fees are overdue and require
                                    attention:
                                </p>
                                <ul class="mt-2 list-disc list-inside space-y-1">
                                    <li
                                        v-for="late in latePayments.slice(0, 5)"
                                        :key="late.id"
                                    >
                                        {{ late.student_name }} ({{ late.student_no }}) -
                                        £{{ parseFloat(late.amount).toFixed(2) }} -
                                        {{ Math.abs(Number(late.days_overdue ?? 0)).toFixed(0) }}
                                        day(s) overdue
                                    </li>
                                    <li
                                        v-if="latePayments.length > 5"
                                        class="font-semibold"
                                    >
                                        ... and
                                        {{ latePayments.length - 5 }} more
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            All Fees
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Manage student fees and payment records
                        </p>
                    </div>

                    <!-- Filters -->
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <select
                                v-model="status"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-48"
                            >
                                <option value="all">All statuses</option>
                                <option value="pending">Pending</option>
                                <option value="payment_pending">Payment Pending</option>
                                <option value="paid">Paid</option>
                            </select>

                            <div class="relative w-full sm:w-72">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search student, description, amount…"
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                                <button
                                    v-if="search"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="
                                        () => {
                                            search = '';
                                            applyFilters();
                                        }
                                    "
                                >
                                    <span class="sr-only">Clear</span>
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Fees Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Student No
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Student Name
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Amount
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Description
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
                                        Due Date
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Paid Date
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Processed By
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
                                    v-if="filtered.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="9"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{ entries.length === 0 ? 'No fees found. Create your first fee to get started.' : 'No fees match your filters.' }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="fee in entries"
                                    :key="fee.id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                    :class="{
                                        'bg-blue-50/50': fee.status === 'payment_pending',
                                    }"
                                >
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900"
                                    >
                                        {{ fee.student_no }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-700"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                            >
                                                <img
                                                    v-if="fee.student_photo"
                                                    :src="`/storage/${fee.student_photo}`"
                                                    :alt="`Photo for ${fee.student_name}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{
                                                        fee.student_name
                                                            .charAt(0)
                                                            .toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                            <span>{{ fee.student_name }}</span>
                                        </div>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        £{{ parseFloat(fee.amount).toFixed(2) }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ fee.description || "-" }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm"
                                    >
                                        <span
                                            :class="
                                                getStatusBadgeClass(fee.status)
                                            "
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-medium capitalize"
                                        >
                                            {{ fee.status.replace("_", " ") }}
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm"
                                        :class="{
                                            'text-red-600 font-semibold': fee.is_late,
                                            'text-slate-600': !fee.is_late,
                                        }"
                                    >
                                        {{ fee.due_date }}
                                        <span
                                            v-if="fee.is_late && fee.days_overdue !== null && fee.days_overdue !== undefined"
                                            class="ml-2 text-xs text-red-600"
                                        >
                                            ({{ Math.abs(Number(fee.days_overdue)).toFixed(0) }}d late)
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ fee.paid_date || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-600"
                                    >
                                        <template v-if="fee.status === 'paid' && (fee.processed_by || fee.payment_processed_at)">
                                            <div>{{ fee.processed_by || "—" }}</div>
                                            <div
                                                v-if="fee.payment_processed_at"
                                                class="text-xs text-slate-500"
                                            >
                                                {{ fee.payment_processed_at }}
                                            </div>
                                        </template>
                                        <span v-else>—</span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <template
                                                v-if="
                                                    fee.status ===
                                                    'payment_pending'
                                                "
                                            >
                                                <button
                                                    @click="
                                                        approvePayment(fee.id)
                                                    "
                                                    class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                                >
                                                    Approve Payment
                                                </button>
                                                <button
                                                    @click="
                                                        rejectPayment(fee.id)
                                                    "
                                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                >
                                                    Reject
                                                </button>
                                            </template>
                                            <template v-else>
                                                <a
                                                    v-if="fee.status === 'paid'"
                                                    :href="
                                                        route(
                                                            'admin.fees.receipt',
                                                            fee.id
                                                        )
                                                    "
                                                    target="_blank"
                                                    class="rounded-md bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                >
                                                    Receipt
                                                </a>
                                                <Link
                                                    :href="
                                                        route(
                                                            'admin.fees.edit',
                                                            fee.id
                                                        )
                                                    "
                                                    class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                                >
                                                    Edit
                                                </Link>
                                                <button
                                                    @click="deleteFee(fee.id)"
                                                    class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                >
                                                    Delete
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 border-t border-slate-200 px-4 py-3 sm:px-6">
                        <Pagination :links="fees.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
