<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineProps({
    fees: {
        type: Object,
        required: true,
    },
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
        return "bg-green-100 text-green-800";
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
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-if="fees.data.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="8"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No fees found. Create your first fee to
                                        get started.
                                    </td>
                                </tr>
                                <tr
                                    v-for="fee in fees.data"
                                    :key="fee.id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
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
                    <div
                        v-if="fees.links && fees.links.length > 3"
                        class="mt-4 flex items-center justify-between border-t border-slate-200 px-4 py-3 sm:px-6"
                    >
                        <div class="flex flex-1 justify-between sm:hidden">
                            <Link
                                v-if="fees.prev_page_url"
                                :href="fees.prev_page_url"
                                class="relative inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                            >
                                Previous
                            </Link>
                            <Link
                                v-if="fees.next_page_url"
                                :href="fees.next_page_url"
                                class="relative ml-3 inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                            >
                                Next
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
