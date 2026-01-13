<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
    course_code: "",
    title: "",
    credits: "",
    semester: "",
    photo: null,
});

const submit = () => {
    form.post(route("admin.courses.store"), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create Course" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New Course
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Course Code -->
                            <div>
                                <label
                                    for="course_code"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Course Code
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="course_code"
                                    v-model="form.course_code"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.course_code,
                                    }"
                                    placeholder="e.g., COMP101"
                                />
                                <p
                                    v-if="form.errors.course_code"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.course_code }}
                                </p>
                            </div>

                            <!-- Title -->
                            <div>
                                <label
                                    for="title"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Course Title
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.title,
                                    }"
                                    placeholder="e.g., Introduction to Computing"
                                />
                                <p
                                    v-if="form.errors.title"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Credits -->
                            <div>
                                <label
                                    for="credits"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Credits <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="credits"
                                    v-model="form.credits"
                                    type="number"
                                    min="1"
                                    max="10"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.credits,
                                    }"
                                    placeholder="e.g., 3"
                                />
                                <p
                                    v-if="form.errors.credits"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.credits }}
                                </p>
                            </div>

                            <!-- Semester -->
                            <div>
                                <label
                                    for="semester"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Semester <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="semester"
                                    v-model="form.semester"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.semester,
                                    }"
                                    placeholder="e.g., Semester 1"
                                />
                                <p
                                    v-if="form.errors.semester"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.semester }}
                                </p>
                            </div>

                            <!-- Course Photo (optional) -->
                            <div>
                                <label
                                    for="photo"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Course Photo
                                    <span class="text-xs text-slate-500">
                                        (JPEG/PNG, max 2MB)
                                    </span>
                                </label>
                                <input
                                    id="photo"
                                    type="file"
                                    accept="image/jpeg,image/jpg,image/png"
                                    class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-portal-navy file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-portal-navy-dark"
                                    @change="
                                        (e) => (form.photo = e.target.files[0])
                                    "
                                />
                                <p
                                    v-if="form.errors.photo"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.photo }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
                                <a
                                    :href="route('admin.courses.index')"
                                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 disabled:opacity-50"
                                >
                                    <span v-if="form.processing">
                                        Creating...
                                    </span>
                                    <span v-else>Create Course</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
