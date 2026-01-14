<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    fees: {
        type: Array,
        required: true,
    },
    message: {
        type: String,
        default: null,
    },
});

const viewMode = ref("cards"); // cards | table
const query = ref("");
const statusFilter = ref("all"); // all | pending | payment_pending | paid

const isOverdue = (dueDate) => {
    if (!dueDate) return false;
    const due = new Date(dueDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return due < today;
};

const stats = computed(() => {
    const list = props.fees ?? [];
    const total = list.reduce((sum, f) => sum + parseFloat(f.amount || 0), 0);
    const paid = list
        .filter((f) => f.status === "paid")
        .reduce((sum, f) => sum + parseFloat(f.amount || 0), 0);
    const pending = list
        .filter((f) => f.status === "pending")
        .reduce((sum, f) => sum + parseFloat(f.amount || 0), 0);
    const overdue = list.filter(
        (f) => f.status !== "paid" && isOverdue(f.due_date)
    ).length;

    return {
        total: total.toFixed(2),
        paid: paid.toFixed(2),
        pending: pending.toFixed(2),
        overdue,
        totalCount: list.length,
        paidCount: list.filter((f) => f.status === "paid").length,
        pendingCount: list.filter((f) => f.status === "pending").length,
    };
});

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.fees ?? [];

    if (statusFilter.value !== "all") {
        list = list.filter((f) => f.status === statusFilter.value);
    }

    if (q) {
        list = list.filter((f) => {
            const desc = (f.description ?? "").toLowerCase();
            const amount = String(f.amount ?? "").toLowerCase();
            return desc.includes(q) || amount.includes(q);
        });
    }

    return list;
});

const getStatusBadgeClass = (status) => {
    if (status === "paid") {
        return "bg-emerald-100 text-emerald-800";
    } else if (status === "payment_pending") {
        return "bg-blue-100 text-blue-800";
    } else {
        return "bg-amber-100 text-amber-800";
    }
};

const submitPayment = (feeId) => {
    if (
        !confirm(
            "Are you sure you want to submit payment confirmation for this fee? This will require admin approval."
        )
    ) {
        return;
    }

    router.post(route("student.fees.submit-payment", feeId), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="My Fees" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                My Fees
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Fees' },
                    ]"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- No Student Record Message -->
                <div v-if="message" class="portal-card p-6">
                    <div class="rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-amber-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3
                                    class="text-sm font-medium text-amber-800"
                                >
                                    Student Record Not Found
                                </h3>
                                <div class="mt-2 text-sm text-amber-700">
                                    <p>{{ message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fees List -->
                <div v-else>
                    <!-- Summary Stats -->
                    <div class="mb-6 grid gap-4 md:grid-cols-4">
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
                        <div class="portal-card p-5 bg-red-50">
                            <p class="text-xs font-semibold uppercase tracking-wide text-red-700">
                                Overdue
                            </p>
                            <p class="mt-2 text-2xl font-bold text-red-900">
                                {{ stats.overdue }}
                            </p>
                            <p class="mt-1 text-xs text-red-700">
                                Need attention
                            </p>
                        </div>
                    </div>

                    <div class="portal-card overflow-hidden p-6">
                        <div class="mb-4">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Fee Records
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                View your fee records and submit payment confirmations. Payment confirmations require admin approval.
                            </p>
                        </div>

                        <!-- Filters -->
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <select
                                    v-model="statusFilter"
                                    class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-48"
                                >
                                    <option value="all">All statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="payment_pending">Payment Pending</option>
                                    <option value="paid">Paid</option>
                                </select>

                                <div class="relative w-full sm:w-72">
                                    <input
                                        v-model="query"
                                        type="text"
                                        placeholder="Search description, amount…"
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

                            <div class="inline-flex rounded-md bg-slate-100 p-1">
                                <button
                                    type="button"
                                    class="rounded-md px-3 py-1.5 text-xs font-semibold"
                                    :class="viewMode === 'cards' ? 'bg-white text-slate-900 shadow' : 'text-slate-600 hover:text-slate-900'"
                                    @click="viewMode = 'cards'"
                                >
                                    Cards
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md px-3 py-1.5 text-xs font-semibold"
                                    :class="viewMode === 'table' ? 'bg-white text-slate-900 shadow' : 'text-slate-600 hover:text-slate-900'"
                                    @click="viewMode = 'table'"
                                >
                                    Table
                                </button>
                            </div>
                        </div>

                    <!-- Cards View -->
                    <div v-if="viewMode === 'cards' && filtered.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="fee in filtered"
                            :key="fee.id"
                            class="rounded-lg border-2 p-5 transition-shadow hover:shadow-lg"
                            :class="{
                                'border-emerald-200 bg-emerald-50/50': fee.status === 'paid',
                                'border-blue-200 bg-blue-50/50': fee.status === 'payment_pending',
                                'border-amber-200 bg-amber-50/50': fee.status === 'pending' && !isOverdue(fee.due_date),
                                'border-red-300 bg-red-50/50 ring-2 ring-red-200': fee.status === 'pending' && isOverdue(fee.due_date),
                            }"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            :class="getStatusBadgeClass(fee.status)"
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize"
                                        >
                                            {{ fee.status.replace("_", " ") }}
                                        </span>
                                        <span
                                            v-if="fee.status !== 'paid' && isOverdue(fee.due_date)"
                                            class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-[10px] font-bold text-red-800"
                                        >
                                            ⚠ Overdue
                                        </span>
                                    </div>
                                    <p class="mt-3 text-2xl font-bold text-slate-900">
                                        £{{ parseFloat(fee.amount).toFixed(2) }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-700">
                                        {{ fee.description || "No description" }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full"
                                        :class="{
                                            'bg-emerald-100': fee.status === 'paid',
                                            'bg-blue-100': fee.status === 'payment_pending',
                                            'bg-amber-100': fee.status === 'pending' && !isOverdue(fee.due_date),
                                            'bg-red-100': fee.status === 'pending' && isOverdue(fee.due_date),
                                        }"
                                    >
                                        <svg
                                            v-if="fee.status === 'paid'"
                                            class="h-6 w-6 text-emerald-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <svg
                                            v-else-if="fee.status === 'payment_pending'"
                                            class="h-6 w-6 text-blue-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            class="h-6 w-6"
                                            :class="{
                                                'text-amber-600': !isOverdue(fee.due_date),
                                                'text-red-600': isOverdue(fee.due_date),
                                            }"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-2 border-t border-slate-200 pt-4">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Due Date</span>
                                    <span
                                        class="font-medium"
                                        :class="{
                                            'text-red-700': isOverdue(fee.due_date) && fee.status !== 'paid',
                                            'text-slate-700': !isOverdue(fee.due_date) || fee.status === 'paid',
                                        }"
                                    >
                                        {{ fee.due_date }}
                                    </span>
                                </div>
                                <div v-if="fee.paid_date" class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Paid Date</span>
                                    <span class="font-medium text-emerald-700">{{ fee.paid_date }}</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <span
                                    v-if="fee.status === 'payment_pending'"
                                    class="inline-flex w-full items-center justify-center rounded-md bg-blue-100 px-3 py-2 text-xs font-semibold text-blue-800"
                                >
                                    Payment Pending Approval
                                </span>
                                <button
                                    v-else-if="fee.status === 'pending'"
                                    @click="submitPayment(fee.id)"
                                    class="w-full rounded-md bg-portal-navy px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Submit Payment
                                </button>
                                <span
                                    v-else
                                    class="inline-flex w-full items-center justify-center rounded-md bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-800"
                                >
                                    ✓ Paid
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Fees Table -->
                    <div v-if="viewMode === 'table' && filtered.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
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
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-for="fee in filtered"
                                    :key="fee.id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                    :class="{
                                        'bg-red-50/50': fee.status !== 'paid' && isOverdue(fee.due_date),
                                    }"
                                >
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900"
                                    >
                                        £{{ parseFloat(fee.amount).toFixed(2) }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-700"
                                    >
                                        {{ fee.description || "-" }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm"
                                    >
                                        <span
                                            :class="getStatusBadgeClass(fee.status)"
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-medium capitalize"
                                        >
                                            {{ fee.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ fee.due_date }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ fee.paid_date || "-" }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <span
                                            v-if="fee.status === 'payment_pending'"
                                            class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800"
                                        >
                                            Payment Pending
                                        </span>
                                        <button
                                            v-else-if="fee.status === 'pending'"
                                            @click="submitPayment(fee.id)"
                                            class="rounded-md bg-portal-navy px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                        >
                                            Submit Payment
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="filtered.length === 0"
                        class="rounded-lg bg-slate-50 p-8 text-center"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <h3
                            class="mt-2 text-sm font-medium text-slate-900"
                        >
                            {{ fees.length === 0 ? 'No fees found' : 'No fees match your filters' }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ fees.length === 0 ? "You don't have any fee records yet." : 'Try adjusting your filters or search terms.' }}
                        </p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

