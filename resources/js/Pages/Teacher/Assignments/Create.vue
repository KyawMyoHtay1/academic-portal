<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    subject: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: "",
    description: "",
    due_date: "",
    due_time: "",
    max_score: 100,
    // Default to published so students can see it immediately (teacher can switch to draft if needed).
    status: "published",
    allowed_file_types: ["pdf", "doc", "docx"],
    max_file_size: 5120, // 5MB in KB
});

const submit = () => {
    form.post(route("teacher.assignments.store", props.subject.id));
};
</script>

<template>
    <Head title="Create Assignment" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Assignments', href: route('teacher.assignments.index') },
                        { label: props.subject.subject_code, href: route('teacher.assignments.show', props.subject.id) },
                        { label: 'Create Assignment' },
                    ]"
                />
            </div>
        </template>

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Create Assignment
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <div class="mb-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Subject</p>
                        <p class="mt-2 text-lg font-bold text-slate-900">
                            {{ subject.subject_code }} - {{ subject.title }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">{{ subject.course_code }}</p>
                    </div>

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
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
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
                                        :min="new Date().toISOString().split('T')[0]"
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    />
                                    <p v-if="form.errors.due_date" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.due_date }}
                                    </p>
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
                                    <p v-if="form.errors.due_time" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.due_time }}
                                    </p>
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
                                    <p v-if="form.errors.max_score" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.max_score }}
                                    </p>
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
                                    <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.status }}
                                    </p>
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
                                <p v-if="form.errors['allowed_file_types']" class="mt-1 text-sm text-red-600">
                                    {{ form.errors["allowed_file_types"] }}
                                </p>
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
                                <p class="mt-1 text-xs text-slate-500">
                                    Maximum file size in kilobytes (default: 5120 KB = 5 MB)
                                </p>
                                <p v-if="form.errors.max_file_size" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.max_file_size }}
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Link
                                    :href="route('teacher.assignments.show', props.subject.id)"
                                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Creating...</span>
                                    <span v-else>Create Assignment</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
