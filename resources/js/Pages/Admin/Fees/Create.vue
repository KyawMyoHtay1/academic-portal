<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    students: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    student_id: "",
    amount: "",
    description: "",
    due_date: "",
});

const submit = () => {
    form.post(route("admin.fees.store"));
};
</script>

<template>
    <Head title="Create Fee" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Create Fee
            </h2>
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
                                    Amount (£) <span class="text-red-500">*</span>
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
                                    placeholder="e.g. Tuition Fee - Semester 1"
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

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end gap-3 pt-4">
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
                                        Creating...
                                    </span>
                                    <span v-else>Create Fee</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

