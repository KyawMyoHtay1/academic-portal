<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    assignment: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.assignment.title,
    description: props.assignment.description ?? "",
    due_date: props.assignment.due_date,
    due_time: props.assignment.due_time ?? "",
    max_score: props.assignment.max_score,
    status: props.assignment.status,
    allowed_file_types: props.assignment.allowed_file_types ?? ["pdf", "doc", "docx"],
    max_file_size: props.assignment.max_file_size ?? 5120,
});

const submit = () => {
    form.put(route("teacher.assignments.update", props.assignment.id));
};
</script>

<template>
    <Head title="Edit Assignment" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Assignments', href: route('teacher.assignments.index') },
                        { label: props.assignment.subject.subject_code, href: route('teacher.assignments.show', props.assignment.subject.id) },
                        { label: 'Edit Assignment' },
                    ]"
                />
            </div>
        </template>

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Edit Assignment
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                />
                                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">
                                    Description
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                ></textarea>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">
                                        Due Date <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.due_date"
                                        type="date"
                                        required
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">
                                        Due Time (optional)
                                    </label>
                                    <input
                                        v-model="form.due_time"
                                        type="time"
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">
                                        Maximum Score <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model.number="form.max_score"
                                        type="number"
                                        min="1"
                                        max="1000"
                                        required
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.status"
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    >
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">
                                    Allowed File Types
                                </label>
                                <div class="mt-2 space-y-2 text-sm">
                                    <label v-for="type in ['pdf', 'doc', 'docx', 'txt', 'zip', 'rar']" :key="type" class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            :value="type"
                                            v-model="form.allowed_file_types"
                                            class="rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                        />
                                        <span class="uppercase">{{ type }}</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">
                                    Maximum File Size (KB)
                                </label>
                                <input
                                    v-model.number="form.max_file_size"
                                    type="number"
                                    min="1"
                                    max="10240"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                />
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Link
                                    :href="route('teacher.assignments.show', props.assignment.subject.id)"
                                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Updating...</span>
                                    <span v-else>Update Assignment</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
