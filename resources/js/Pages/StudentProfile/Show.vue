<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    student: {
        type: Object,
        default: null,
    },
    message: {
        type: String,
        default: null,
    },
});

const form = useForm({
    phone: props.student?.phone || "",
    photo: null,
});

const hasStudentRecord = computed(() => props.student !== null);

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: "patch",
    })).post(route("student.profile.update"), {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => form.reset("photo"),
    });
};
</script>

<template>
    <Head title="My Student Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Student Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <!-- No Student Record Message -->
                <div v-if="!hasStudentRecord" class="portal-card p-6">
                    <div
                        class="rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200"
                    >
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-amber-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-amber-800">
                                    Student Record Not Found
                                </h3>
                                <div class="mt-2 text-sm text-amber-700">
                                    <p>{{ message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Profile Form -->
                <div v-else class="space-y-6">
                    <!-- Profile header with photo -->
                    <div class="portal-card flex items-center gap-4 p-6">
                        <div
                            class="h-16 w-16 overflow-hidden rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center"
                        >
                            <img
                                v-if="student.photo_url"
                                :src="student.photo_url"
                                :alt="`Photo of ${student.full_name}`"
                                class="h-full w-full object-cover"
                            />
                            <span
                                v-else
                                class="text-lg font-semibold text-slate-500"
                            >
                                {{ student.full_name.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                {{ student.full_name }}
                            </h3>
                            <p class="text-sm text-slate-600">
                                {{ student.student_no }} •
                                {{ student.programme }}
                            </p>
                        </div>
                    </div>
                    <!-- Academic Information (Read-only) -->
                    <div class="portal-card p-6">
                        <h3 class="mb-4 text-lg font-semibold text-slate-900">
                            Academic Information
                        </h3>
                        <p class="mb-4 text-xs text-slate-500">
                            Core academic details cannot be modified by
                            students. Contact administration for changes.
                        </p>

                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Student Number
                                </dt>
                                <dd class="mt-1 text-sm text-slate-900">
                                    {{ student.student_no }}
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Full Name
                                </dt>
                                <dd class="mt-1 text-sm text-slate-900">
                                    {{ student.full_name }}
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Programme
                                </dt>
                                <dd class="mt-1 text-sm text-slate-900">
                                    {{ student.programme }}
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Intake Year
                                </dt>
                                <dd class="mt-1 text-sm text-slate-900">
                                    {{ student.intake_year }}
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Date of Birth
                                </dt>
                                <dd class="mt-1 text-sm text-slate-900">
                                    {{
                                        student.dob
                                            ? new Date(
                                                  student.dob
                                              ).toLocaleDateString()
                                            : "Not provided"
                                    }}
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Email
                                </dt>
                                <dd class="mt-1 text-sm text-slate-900">
                                    {{ student.email }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Contact Information (Editable) -->
                    <div class="portal-card p-6">
                        <h3 class="mb-4 text-lg font-semibold text-slate-900">
                            Contact Information
                        </h3>
                        <p class="mb-4 text-xs text-slate-500">
                            You can update your contact details and profile
                            photo below.
                        </p>

                        <form @submit.prevent="submit">
                            <div class="space-y-4">
                                <!-- Profile Photo Update -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Profile Photo
                                    </label>
                                    <div class="mt-2 flex items-center gap-6">
                                        <div
                                            v-if="student.photo_url"
                                            class="flex-shrink-0"
                                        >
                                            <img
                                                :src="student.photo_url"
                                                :alt="`Photo of ${student.full_name}`"
                                                class="h-20 w-20 rounded-full border-2 border-slate-200 object-cover"
                                            />
                                        </div>
                                        <div
                                            v-else
                                            class="flex h-20 w-20 items-center justify-center rounded-full border-2 border-slate-200 bg-slate-100"
                                        >
                                            <span
                                                class="text-xl font-semibold text-slate-500"
                                            >
                                                {{
                                                    student.full_name
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <input
                                                type="file"
                                                accept="image/jpeg,image/jpg,image/png"
                                                @change="
                                                    (e) =>
                                                        (form.photo =
                                                            e.target.files[0])
                                                "
                                                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-portal-navy file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-portal-navy-dark"
                                            />
                                            <p
                                                class="mt-1 text-xs text-slate-500"
                                            >
                                                JPEG or PNG, max 2MB. Leave
                                                empty to keep current photo.
                                            </p>
                                            <p
                                                v-if="form.errors.photo"
                                                class="mt-1 text-sm text-red-600"
                                            >
                                                {{ form.errors.photo }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        for="phone"
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Phone Number
                                    </label>
                                    <input
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                        :class="{
                                            'border-red-300 focus:border-red-500 focus:ring-red-500':
                                                form.errors.phone,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.phone"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.phone }}
                                    </p>
                                </div>

                                <div
                                    class="flex items-center justify-end gap-3"
                                >
                                    <button
                                        type="button"
                                        @click="form.reset()"
                                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 disabled:opacity-50"
                                    >
                                        <span v-if="form.processing">
                                            Saving...
                                        </span>
                                        <span v-else>Save Changes</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
