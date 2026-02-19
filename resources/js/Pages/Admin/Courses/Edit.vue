<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm, router } from "@inertiajs/vue3";

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
    globalThreshold: {
        type: Number,
        default: 75,
    },
});

const form = useForm({
    course_code: props.course.course_code,
    title: props.course.title,
    credits: props.course.credits,
    semester: props.course.semester,
    attendance_threshold: props.course.attendance_threshold ?? null,
    photo: null,
});

const semesterOptions = Array.from(
    { length: 8 },
    (_, index) => `Semester ${index + 1}`
);
if (
    props.course.semester &&
    !semesterOptions.includes(props.course.semester)
) {
    semesterOptions.unshift(props.course.semester);
}

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: "put",
    })).post(route("admin.courses.update", props.course.id), {
        forceFormData: true,
        onFinish: () => form.reset("photo"),
    });
};

const removePhoto = () => {
    if (confirm("Are you sure you want to remove this course photo?")) {
        router.delete(route("admin.courses.remove-photo", props.course.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Edit Course" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Course
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'Course Management',
                            href: route('admin.courses.index'),
                        },
                        { label: 'Edit Course' },
                    ]"
                />
            </div>
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
                                <select
                                    id="semester"
                                    v-model="form.semester"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.semester,
                                    }"
                                >
                                    <option value="" disabled>
                                        Select semester
                                    </option>
                                    <option
                                        v-for="semester in semesterOptions"
                                        :key="semester"
                                        :value="semester"
                                    >
                                        {{ semester }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.semester"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.semester }}
                                </p>
                            </div>

                            <!-- Attendance Threshold -->
                            <div>
                                <label
                                    for="attendance_threshold"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Attendance Threshold (%)
                                </label>
                                <input
                                    id="attendance_threshold"
                                    v-model="form.attendance_threshold"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.1"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.attendance_threshold,
                                    }"
                                />
                                <p
                                    v-if="form.errors.attendance_threshold"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.attendance_threshold }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Minimum attendance percentage for this course. Leave empty to use global threshold ({{ globalThreshold }}%).
                                </p>
                            </div>

                            <!-- Existing Photo Preview -->
                            <div v-if="course.photo_url" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Current Course Photo
                                    </span>
                                    <button
                                        type="button"
                                        @click="removePhoto"
                                        class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    >
                                        Remove Photo
                                    </button>
                                </div>
                                <img
                                    :src="course.photo_url"
                                    :alt="`Current photo for ${form.title}`"
                                    class="h-24 w-32 rounded-md object-cover border border-slate-200"
                                />
                            </div>

                            <!-- Course Photo (optional) -->
                            <div>
                                <label
                                    for="photo"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Replace Course Photo
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
                                        Updating...
                                    </span>
                                    <span v-else>Update Course</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
