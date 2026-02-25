<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    fee: {
        type: Object,
        required: true,
    },
    students: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    student_id: props.fee.student_id,
    amount: props.fee.amount,
    description: props.fee.description || "",
    status: props.fee.status,
    due_date: props.fee.due_date,
    paid_date: props.fee.paid_date || "",
});

const submit = () => {
    form.put(route("admin.fees.update", props.fee.id));
};
</script>

<template>
    <Head title="Edit Fee" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Edit Fee
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'Fee Management',
                            href: route('admin.fees.index'),
                        },
                        { label: 'Edit Fee' },
                    ]"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Student Selection -->
                            <div>
                                <label
                                    for="student_id"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Student <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="student_id"
                                    v-model="form.student_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.student_id,
                                    }"
                                >
                                    <option value="">Select student</option>
                                    <option
                                        v-for="student in students"
                                        :key="student.id"
                                        :value="student.id"
                                    >
                                        {{ student.student_no }} -
                                        {{ student.full_name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.student_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.student_id }}
                                </p>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label
                                    for="amount"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Amount (£)
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="amount"
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.amount,
                                    }"
                                />
                                <p
                                    v-if="form.errors.amount"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.amount }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label
                                    for="description"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Description
                                </label>
                                <input
                                    id="description"
                                    v-model="form.description"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.description,
                                    }"
                                />
                                <p
                                    v-if="form.errors.description"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label
                                    for="status"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.status,
                                    }"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="payment_pending">Payment Pending</option>
                                    <option value="failed">Failed</option>
                                    <option value="paid">Paid</option>
                                </select>
                                <p
                                    v-if="form.errors.status"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.status }}
                                </p>
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label
                                    for="due_date"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Due Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="due_date"
                                    v-model="form.due_date"
                                    type="date"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.due_date,
                                    }"
                                />
                                <p
                                    v-if="form.errors.due_date"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.due_date }}
                                </p>
                            </div>

                            <!-- Paid Date -->
                            <div v-if="form.status === 'paid'">
                                <label
                                    for="paid_date"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Paid Date
                                </label>
                                <input
                                    id="paid_date"
                                    v-model="form.paid_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.paid_date,
                                    }"
                                />
                                <p
                                    v-if="form.errors.paid_date"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.paid_date }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
                                <Link
                                    :href="route('admin.fees.index')"
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
                                        Updating...
                                    </span>
                                    <span v-else>Update Fee</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
