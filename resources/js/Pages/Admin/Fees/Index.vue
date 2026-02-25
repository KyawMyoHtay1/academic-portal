<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Pagination from "@/Components/Pagination.vue";
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
    financeSummary: {
        type: Object,
        default: () => ({
            total_billed: 0,
            total_collected: 0,
            total_outstanding: 0,
            aging: {
                "0_7": { count: 0, amount: 0 },
                "8_30": { count: 0, amount: 0 },
                "31_plus": { count: 0, amount: 0 },
            },
        }),
    },
    filteredSummary: {
        type: Object,
        default: () => ({ count: 0, amount: 0 }),
    },
});

const status = ref(props.filters.status || "all");
const search = ref(props.filters.search || "");
const overdueOnly = ref(Boolean(props.filters.overdue_only));
const dueBucket = ref(props.filters.due_bucket || "all");
const suppressAutoApply = ref(false);

const entries = computed(() => props.fees?.data ?? []);

const hasActiveFilters = computed(
    () =>
        status.value !== "all" ||
        search.value.trim() !== "" ||
        overdueOnly.value ||
        dueBucket.value !== "all"
);

const pageStats = computed(() => {
    const list = entries.value;

    return {
        count: list.length,
        paidCount: list.filter((f) => f.status === "paid").length,
        paymentPendingCount: list.filter((f) => f.status === "payment_pending").length,
        overdueCount: list.filter((f) => f.is_late).length,
    };
});

const formatCurrency = (amount) => `GBP ${Number(amount || 0).toFixed(2)}`;

const applyFilters = () => {
    router.get(
        route("admin.fees.index"),
        {
            status: status.value,
            search: search.value || undefined,
            overdue_only: overdueOnly.value ? 1 : undefined,
            due_bucket: dueBucket.value !== "all" ? dueBucket.value : undefined,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    );
};

const exportUrl = (format) =>
    route("admin.fees.export", {
        format,
        status: status.value,
        search: search.value || undefined,
        overdue_only: overdueOnly.value ? 1 : undefined,
        due_bucket: dueBucket.value !== "all" ? dueBucket.value : undefined,
    });

watch([status, overdueOnly, dueBucket], () => {
    if (suppressAutoApply.value) {
        return;
    }

    applyFilters();
});

const clearFilters = () => {
    suppressAutoApply.value = true;
    status.value = "all";
    search.value = "";
    overdueOnly.value = false;
    dueBucket.value = "all";
    suppressAutoApply.value = false;
    applyFilters();
};

const deleteFee = (feeId) => {
    if (!confirm("Are you sure you want to delete this fee? This action cannot be undone.")) {
        return;
    }

    router.delete(route("admin.fees.destroy", feeId), {
        preserveScroll: true,
    });
};

const approvePayment = (feeId) => {
    if (!confirm("Are you sure you want to approve this payment confirmation?")) {
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
    if (!confirm("Are you sure you want to reject this payment confirmation? The fee status will revert to pending.")) {
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

const sendReminder = (feeId) => {
    if (!confirm("Send an overdue reminder to this student now?")) {
        return;
    }

    router.post(
        route("admin.fees.send-reminder", feeId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const sendFilteredOverdueReminders = () => {
    if (!confirm("Send overdue reminders for the current filtered result?")) {
        return;
    }

    router.post(
        route("admin.fees.send-overdue-reminders"),
        {
            status: status.value,
            search: search.value || undefined,
            overdue_only: overdueOnly.value ? 1 : undefined,
            due_bucket: dueBucket.value !== "all" ? dueBucket.value : undefined,
        },
        {
            preserveScroll: true,
        }
    );
};

const getStatusBadgeClass = (value) => {
    if (value === "paid") {
        return "bg-emerald-100 text-emerald-800";
    }
    if (value === "payment_pending") {
        return "bg-blue-100 text-blue-800";
    }

    return "bg-amber-100 text-amber-800";
};

const timelineEventClass = (action) => {
    if (action === "payment_approved") return "text-emerald-700";
    if (action === "payment_rejected") return "text-red-700";
    if (action === "reminder_sent") return "text-amber-700";

    return "text-slate-700";
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
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark"
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
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="portal-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Total billed
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ formatCurrency(financeSummary.total_billed) }}
                        </p>
                    </div>
                    <div class="portal-card bg-emerald-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                            Total collected
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ formatCurrency(financeSummary.total_collected) }}
                        </p>
                    </div>
                    <div class="portal-card bg-amber-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                            Total outstanding
                        </p>
                        <p class="mt-2 text-2xl font-bold text-amber-900">
                            {{ formatCurrency(financeSummary.total_outstanding) }}
                        </p>
                    </div>
                    <div class="portal-card bg-indigo-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">
                            Current filtered set
                        </p>
                        <p class="mt-2 text-2xl font-bold text-indigo-900">
                            {{ formatCurrency(filteredSummary.amount) }}
                        </p>
                        <p class="mt-1 text-xs text-indigo-700">
                            {{ filteredSummary.count }} fee(s)
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                            Overdue 0-7 days
                        </p>
                        <p class="mt-1 text-lg font-bold text-amber-900">
                            {{ formatCurrency(financeSummary.aging?.['0_7']?.amount) }}
                        </p>
                        <p class="text-xs text-amber-700">
                            {{ financeSummary.aging?.['0_7']?.count ?? 0 }} fee(s)
                        </p>
                    </div>
                    <div class="rounded-lg border border-orange-200 bg-orange-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-orange-700">
                            Overdue 8-30 days
                        </p>
                        <p class="mt-1 text-lg font-bold text-orange-900">
                            {{ formatCurrency(financeSummary.aging?.['8_30']?.amount) }}
                        </p>
                        <p class="text-xs text-orange-700">
                            {{ financeSummary.aging?.['8_30']?.count ?? 0 }} fee(s)
                        </p>
                    </div>
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-red-700">
                            Overdue 31+ days
                        </p>
                        <p class="mt-1 text-lg font-bold text-red-900">
                            {{ formatCurrency(financeSummary.aging?.['31_plus']?.amount) }}
                        </p>
                        <p class="text-xs text-red-700">
                            {{ financeSummary.aging?.['31_plus']?.count ?? 0 }} fee(s)
                        </p>
                    </div>
                </div>

                <div
                    v-if="latePaymentsCount > 0"
                    class="rounded-lg border-l-4 border-red-500 bg-red-50 p-4"
                >
                    <h3 class="text-sm font-semibold text-red-800">
                        {{ latePaymentsCount }} overdue fee(s) need attention
                    </h3>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                        <li
                            v-for="late in latePayments.slice(0, 6)"
                            :key="late.id"
                        >
                            {{ late.student_name }} ({{ late.student_no }}) -
                            {{ formatCurrency(late.amount) }} -
                            {{ Number(late.days_overdue ?? 0).toFixed(0) }} day(s) overdue
                        </li>
                        <li
                            v-if="latePayments.length > 6"
                            class="font-semibold"
                        >
                            ... and {{ latePayments.length - 6 }} more
                        </li>
                    </ul>
                </div>

                <div class="portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Filters
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Refine by status, overdue range, and search.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                class="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100"
                                @click="sendFilteredOverdueReminders"
                            >
                                Remind Filtered Overdue
                            </button>
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
                                class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                @click="clearFilters"
                            >
                                Clear all filters
                            </button>
                        </div>
                    </div>

                    <div class="grid gap-3 lg:grid-cols-5">
                        <select
                            v-model="status"
                            class="rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        >
                            <option value="all">All statuses</option>
                            <option value="pending">Pending</option>
                            <option value="payment_pending">Payment Pending</option>
                            <option value="paid">Paid</option>
                        </select>

                        <select
                            v-model="dueBucket"
                            class="rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        >
                            <option value="all">All overdue buckets</option>
                            <option value="0_7">Overdue 0-7 days</option>
                            <option value="8_30">Overdue 8-30 days</option>
                            <option value="31_plus">Overdue 31+ days</option>
                        </select>

                        <label class="flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                            <input
                                v-model="overdueOnly"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                            />
                            Overdue only
                        </label>

                        <div class="relative lg:col-span-2">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search student, description, amount..."
                                class="block w-full rounded-md border-slate-300 pr-20 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                @keydown.enter.prevent="applyFilters"
                            />
                            <button
                                type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md bg-portal-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-portal-navy-dark"
                                @click="applyFilters"
                            >
                                Search
                            </button>
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-slate-500">
                        Current page: {{ pageStats.count }} fee(s),
                        {{ pageStats.paymentPendingCount }} payment pending,
                        {{ pageStats.overdueCount }} overdue
                    </p>
                </div>

                <div class="portal-card p-6">
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Fee Records
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Includes payment audit timeline per fee.
                        </p>
                    </div>

                    <div class="overflow-x-auto pb-2">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Student No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Student Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Due Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Paid Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Processed By</th>
                                    <th class="min-w-[14rem] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">Audit Timeline</th>
                                    <th class="min-w-[16rem] px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-if="entries.length === 0">
                                    <td colspan="10" class="px-4 py-8 text-center text-sm text-slate-500">
                                        No fees match your current filters.
                                    </td>
                                </tr>

                                <tr
                                    v-for="fee in entries"
                                    :key="fee.id"
                                    class="transition-colors hover:bg-slate-50"
                                    :class="{ 'bg-blue-50/40': fee.status === 'payment_pending' }"
                                >
                                    <td class="px-4 py-4 text-sm font-medium text-slate-900">
                                        {{ fee.student_no }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-slate-100">
                                                <img
                                                    v-if="fee.student_photo"
                                                    :src="`/storage/${fee.student_photo}`"
                                                    :alt="`Photo for ${fee.student_name}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span v-else class="text-xs font-semibold text-slate-500">
                                                    {{ fee.student_name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                            <span>{{ fee.student_name }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700">
                                        {{ formatCurrency(fee.amount) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        {{ fee.description || "-" }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm">
                                        <span
                                            :class="getStatusBadgeClass(fee.status)"
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-medium capitalize"
                                        >
                                            {{ fee.status.replace("_", " ") }}
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm"
                                        :class="{
                                            'font-semibold text-red-600': fee.is_late,
                                            'text-slate-600': !fee.is_late,
                                        }"
                                    >
                                        {{ fee.due_date }}
                                        <span
                                            v-if="fee.is_late && fee.days_overdue !== null"
                                            class="ml-1 rounded bg-red-100 px-1.5 py-0.5 text-[11px] font-semibold text-red-700"
                                        >
                                            {{ Number(fee.days_overdue).toFixed(0) }}d late
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">
                                        {{ fee.paid_date || "-" }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        <template v-if="fee.status === 'paid' && (fee.processed_by || fee.payment_processed_at)">
                                            <div>{{ fee.processed_by || "-" }}</div>
                                            <div v-if="fee.payment_processed_at" class="text-xs text-slate-500">
                                                {{ fee.payment_processed_at }}
                                            </div>
                                        </template>
                                        <span v-else>-</span>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-600">
                                        <div class="max-w-xs space-y-1">
                                            <div
                                                v-for="event in (fee.timeline || []).slice(-4)"
                                                :key="`${fee.id}-${event.id ?? event.created_at}-${event.action}`"
                                                class="rounded bg-slate-50 px-2 py-1"
                                            >
                                                <p class="font-semibold" :class="timelineEventClass(event.action)">
                                                    {{ event.label }}
                                                </p>
                                                <p class="text-[11px] text-slate-500">
                                                    {{ event.created_at || '-' }}
                                                    <span v-if="event.performed_by"> - {{ event.performed_by }}</span>
                                                </p>
                                                <p v-if="event.note" class="text-[11px] text-slate-500">
                                                    {{ event.note }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="min-w-[16rem] px-4 py-4 text-right text-sm align-top">
                                        <div class="ml-auto flex w-fit flex-wrap items-center justify-end gap-2">
                                            <template v-if="fee.status === 'payment_pending'">
                                                <button
                                                    @click="approvePayment(fee.id)"
                                                    class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700"
                                                >
                                                    Approve Payment
                                                </button>
                                                <button
                                                    @click="rejectPayment(fee.id)"
                                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700"
                                                >
                                                    Reject
                                                </button>
                                            </template>

                                            <template v-else>
                                                <button
                                                    v-if="fee.is_late && fee.status !== 'paid'"
                                                    @click="sendReminder(fee.id)"
                                                    class="rounded-md bg-amber-100 px-3 py-1.5 text-xs font-medium text-amber-700 hover:bg-amber-200"
                                                >
                                                    Send Reminder
                                                </button>
                                                <a
                                                    v-if="fee.status === 'paid'"
                                                    :href="route('admin.fees.receipt', fee.id)"
                                                    target="_blank"
                                                    class="rounded-md bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-200"
                                                >
                                                    Receipt
                                                </a>
                                                <Link
                                                    :href="route('admin.fees.edit', fee.id)"
                                                    class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200"
                                                >
                                                    Edit
                                                </Link>
                                                <button
                                                    @click="deleteFee(fee.id)"
                                                    class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200"
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

                    <div class="mt-4 border-t border-slate-200 px-4 py-3 sm:px-6">
                        <Pagination :links="fees.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
