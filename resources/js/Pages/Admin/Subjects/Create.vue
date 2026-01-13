<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    courses: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    course_id: "",
    subject_code: "",
    title: "",
    credits: "",
    description: "",
    photo: null,
});

const submit = () => {
    form.post(route("admin.subjects.store"), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create Subject" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New Subject
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'Subject Management',
                            href: route('admin.subjects.index'),
                        },
                        { label: 'Create Subject' },
                    ]"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Course -->
                            <div>
                                <label
                                    for="course_id"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Course <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="course_id"
                                    v-model="form.course_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.course_id,
                                    }"
                                >
                                    <option value="">Select course</option>
                                    <option
                                        v-for="course in courses"
                                        :key="course.id"
                                        :value="course.id"
                                    >
                                        {{ course.course_code }} -
                                        {{ course.title }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.course_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.course_id }}
                                </p>
                            </div>

                            <!-- Subject Code -->
                            <div>
                                <label
                                    for="subject_code"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Subject Code
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="subject_code"
                                    v-model="form.subject_code"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.subject_code,
                                    }"
                                    placeholder="e.g., CS101"
                                />
                                <p
                                    v-if="form.errors.subject_code"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.subject_code }}
                                </p>
                            </div>

                            <!-- Title -->
                            <div>
                                <label
                                    for="title"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Subject Title
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
                                    placeholder="e.g., Introduction to Programming"
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
                                    Credits
                                </label>
                                <input
                                    id="credits"
                                    v-model="form.credits"
                                    type="number"
                                    min="1"
                                    max="10"
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

                            <!-- Description -->
                            <div>
                                <label
                                    for="description"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.description,
                                    }"
                                    placeholder="Enter subject description..."
                                ></textarea>
                                <p
                                    v-if="form.errors.description"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Subject Photo (optional) -->
                            <div>
                                <label
                                    for="photo"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Subject Photo
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
                                    :href="route('admin.subjects.index')"
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
                                    <span v-else>Create Subject</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
