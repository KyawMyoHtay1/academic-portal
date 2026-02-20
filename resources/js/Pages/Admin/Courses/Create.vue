<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();
const MAX_PHOTO_BYTES = 2 * 1024 * 1024;
const ALLOWED_PHOTO_MIME_TYPES = ["image/jpeg", "image/jpg", "image/png"];
const ALLOWED_PHOTO_EXTENSIONS = ["jpg", "jpeg", "png"];

const props = defineProps({
    globalThreshold: {
        type: Number,
        default: 75,
    },
});

const form = useForm({
    course_code: "",
    title: "",
    credits: "",
    semester: "",
    attendance_threshold: null,
    photo: null,
});
const clientPhotoError = ref("");

const semesterOptions = Array.from(
    { length: 8 },
    (_, index) => `Semester ${index + 1}`
);

const toMb = (bytes) => (bytes / (1024 * 1024)).toFixed(2);

const validatePhoto = (file) => {
    if (!file) return "";

    const mimeType = String(file.type || "").toLowerCase();
    const extension = String(file.name || "")
        .split(".")
        .pop()
        ?.toLowerCase();
    const hasValidMime = mimeType !== "" && ALLOWED_PHOTO_MIME_TYPES.includes(mimeType);
    const hasValidExtension =
        typeof extension === "string" &&
        ALLOWED_PHOTO_EXTENSIONS.includes(extension);

    if (!hasValidMime && !hasValidExtension) {
        return "Course photo must be a JPG or PNG image.";
    }

    if (Number(file.size || 0) > MAX_PHOTO_BYTES) {
        return `Course photo must be 2MB or less. Selected file is ${toMb(file.size)}MB.`;
    }

    return "";
};

const onPhotoChange = (event) => {
    const file = event?.target?.files?.[0] ?? null;
    const error = validatePhoto(file);
    clientPhotoError.value = error;

    if (error) {
        form.photo = null;
        if (event?.target) event.target.value = "";
        return;
    }

    form.photo = file;
};

const getErrorMessage = (value) => (Array.isArray(value) ? value[0] : value || "");

const photoError = computed(() => {
    if (clientPhotoError.value) return clientPhotoError.value;

    const fe = getErrorMessage(form.errors.photo);
    if (fe) return fe;

    const pe = getErrorMessage(page.props.errors?.photo);
    if (pe) return pe;

    const fe2 = getErrorMessage(page.props.errors?.file);
    if (fe2) return fe2;

    const queryError =
        typeof window !== "undefined"
            ? new URLSearchParams(window.location.search).get("_upload_error")
            : null;
    if (queryError) return queryError;

    const flashError = page.props.flash?.error;
    if (typeof flashError === "string" && flashError.length > 0) {
        return flashError;
    }

    return "";
});

const submit = () => {
    const error = validatePhoto(form.photo);
    clientPhotoError.value = error;
    if (error) {
        return;
    }

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

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'Course Management',
                            href: route('admin.courses.index'),
                        },
                        { label: 'Create Course' },
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
                                    @change="onPhotoChange"
                                />
                                <p
                                    v-if="photoError"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ photoError }}
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
