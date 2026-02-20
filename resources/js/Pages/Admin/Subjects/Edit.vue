<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";

import { Head, Link, useForm, router, usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const page = usePage();
const MAX_PHOTO_BYTES = 2 * 1024 * 1024;
const ALLOWED_PHOTO_MIME_TYPES = ["image/jpeg", "image/jpg", "image/png"];
const ALLOWED_PHOTO_EXTENSIONS = ["jpg", "jpeg", "png"];

const props = defineProps({
    subject: {
        type: Object,
        required: true,
    },
    courses: {
        type: Array,
        required: true,
    },
    globalThreshold: {
        type: Number,
        default: 75,
    },
});

const form = useForm({
    course_id: props.subject.course_id,
    subject_code: props.subject.subject_code,
    title: props.subject.title,
    credits: props.subject.credits || "",
    description: props.subject.description || "",
    attendance_threshold: props.subject.attendance_threshold ?? null,
    photo: null,
});
const clientPhotoError = ref("");

const selectedCourse = computed(
    () => props.courses.find((c) => c.id === form.course_id) || null
);

const subjectCodePrefix = computed(() =>
    selectedCourse.value ? `${selectedCourse.value.course_code}-` : ""
);

// When the course changes, prefill the subject code prefix if appropriate
watch(
    () => form.course_id,
    (newId, oldId) => {
        const newCourse = props.courses.find((c) => c.id === newId) || null;
        const oldCourse = props.courses.find((c) => c.id === oldId) || null;

        if (!newCourse) return;

        const newPrefix = `${newCourse.course_code}-`;
        const oldPrefix = oldCourse ? `${oldCourse.course_code}-` : null;
        const current = form.subject_code || "";

        // Replace old prefix with new one, or just set if empty
        if (!current || (oldPrefix && current.startsWith(oldPrefix))) {
            // Remove old prefix if present
            const suffix =
                oldPrefix && current.startsWith(oldPrefix)
                    ? current.substring(oldPrefix.length)
                    : current;
            form.subject_code = newPrefix + suffix;
        } else if (!current.includes("-")) {
            // If just a number/text without prefix, prepend
             form.subject_code = newPrefix + current;
        }
    }
);

const submit = () => {
    const error = validatePhoto(form.photo);
    clientPhotoError.value = error;
    if (error) {
        return;
    }

    form.transform((data) => ({
        ...data,
        _method: "put",
    })).post(route("admin.subjects.update", props.subject.id), {
        forceFormData: true,
        onFinish: () => form.reset("photo"),
    });
};

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
        return "Subject photo must be a JPG or PNG image.";
    }

    if (Number(file.size || 0) > MAX_PHOTO_BYTES) {
        return `Subject photo must be 2MB or less. Selected file is ${toMb(file.size)}MB.`;
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

const removePhoto = () => {
    if (confirm("Are you sure you want to remove this subject photo?")) {
        router.delete(route("admin.subjects.remove-photo", props.subject.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Edit Subject" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Subject
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
                        { label: 'Edit Subject' },
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
                                    :placeholder="
                                        selectedCourse
                                            ? `${subjectCodePrefix}101`
                                            : 'e.g., CS101'
                                    "
                                />
                                <p class="mt-1 text-xs text-slate-500">
                                    When a course is selected, start the subject
                                    code with the course code, for example:
                                    <span class="font-semibold">
                                        {{ subjectCodePrefix }}101
                                    </span>
                                </p>
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
                                ></textarea>
                                <p
                                    v-if="form.errors.description"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.description }}
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
                                    Minimum attendance percentage for this subject. Leave empty to use course threshold or global threshold ({{ globalThreshold }}%).
                                </p>
                            </div>

                            <!-- Existing Photo Preview -->
                            <div v-if="subject.photo_url" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Current Subject Photo
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
                                    :src="subject.photo_url"
                                    :alt="`Current photo for ${form.title}`"
                                    class="h-24 w-32 rounded-md object-cover border border-slate-200"
                                />
                            </div>

                            <!-- Subject Photo (optional) -->
                            <div>
                                <label
                                    for="photo"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Replace Subject Photo
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
                                <Link
                                    :href="route('admin.subjects.index')"
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
                                    <span v-else>Update Subject</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
