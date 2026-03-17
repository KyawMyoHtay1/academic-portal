<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();
const MAX_PHOTO_BYTES = 2 * 1024 * 1024;
const MAX_DOCUMENT_BYTES = 5 * 1024 * 1024;

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    programmes: {
        type: Array,
        required: true,
    },
    duplicateHintStudents: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    user_id: "",
    student_no: "",
    dob: "",
    gender: "",
    nationality: "",
    phone: "",
    address: "",
    emergency_contact_name: "",
    emergency_contact_phone: "",
    programme: "",
    intake_year: "",
    previous_institution: "",
    previous_qualification: "",
    status: "active",
    notes: "",
    photo: null,
    id_card: null,
    transcript: null,
});
const clientPhotoError = ref("");
const clientIdCardError = ref("");
const clientTranscriptError = ref("");

const toMb = (bytes) => (bytes / (1024 * 1024)).toFixed(2);

const validatePhoto = (file) => {
    if (!file) return "";

    if (Number(file.size || 0) > MAX_PHOTO_BYTES) {
        return `Photo must be 2MB or less. Selected file is ${toMb(file.size)}MB.`;
    }

    return "";
};

const validateDocument = (file, label) => {
    if (!file) return "";

    if (Number(file.size || 0) > MAX_DOCUMENT_BYTES) {
        return `${label} must be 5MB or less. Selected file is ${toMb(file.size)}MB.`;
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

const onIdCardChange = (event) => {
    const file = event?.target?.files?.[0] ?? null;
    const error = validateDocument(file, "ID card");
    clientIdCardError.value = error;

    if (error) {
        form.id_card = null;
        if (event?.target) event.target.value = "";
        return;
    }

    form.id_card = file;
};

const onTranscriptChange = (event) => {
    const file = event?.target?.files?.[0] ?? null;
    const error = validateDocument(file, "Transcript");
    clientTranscriptError.value = error;

    if (error) {
        form.transcript = null;
        if (event?.target) event.target.value = "";
        return;
    }

    form.transcript = file;
};

const submit = () => {
    const photoErrorValue = validatePhoto(form.photo);
    const idCardErrorValue = validateDocument(form.id_card, "ID card");
    const transcriptErrorValue = validateDocument(form.transcript, "Transcript");
    clientPhotoError.value = photoErrorValue;
    clientIdCardError.value = idCardErrorValue;
    clientTranscriptError.value = transcriptErrorValue;

    if (photoErrorValue || idCardErrorValue || transcriptErrorValue) {
        return;
    }

    form.post(route("students.store"), {
        forceFormData: true,
    });
};

const normalize = (value) => String(value || "").trim().toLowerCase();

const emailPrefix = (value) => {
    const normalized = normalize(value);
    if (!normalized.includes("@")) return "";

    return normalized.split("@")[0] || "";
};

const duplicateWarnings = computed(() => {
    const name = normalize(selectedUser.value?.name);
    const email = normalize(selectedUser.value?.email);

    if (name === "" && email === "") {
        return [];
    }

    return (props.duplicateHintStudents || [])
        .map((student) => {
            const existingName = normalize(student?.full_name);
            const existingEmail = normalize(student?.email);
            const reasons = [];
            let score = 0;

            if (email !== "" && existingEmail === email) {
                reasons.push("same email");
                score += 100;
            }

            const newPrefix = emailPrefix(email);
            const existingPrefix = emailPrefix(existingEmail);
            if (
                email !== "" &&
                existingEmail !== "" &&
                newPrefix !== "" &&
                existingPrefix === newPrefix
            ) {
                reasons.push("same email prefix");
                score += 40;
            }

            if (name !== "" && existingName === name) {
                reasons.push("same full name");
                score += 70;
            } else if (
                name !== "" &&
                existingName !== "" &&
                (existingName.includes(name) || name.includes(existingName)) &&
                Math.min(name.length, existingName.length) >= 4
            ) {
                reasons.push("similar full name");
                score += 35;
            }

            if (score === 0) {
                return null;
            }

            return {
                id: student.id,
                student_no: student.student_no,
                full_name: student.full_name,
                email: student.email,
                reasons,
                score,
            };
        })
        .filter(Boolean)
        .sort((a, b) => b.score - a.score)
        .slice(0, 5);
});

const selectedUser = computed(() => {
    const id = String(form.user_id || "");
    if (!id) return null;
    return props.users.find((u) => String(u.id) === id) || null;
});

const getErrorMessage = (value) => (Array.isArray(value) ? value[0] : value || "");

const sharedUploadFallbackError = computed(() => {
    const fileError = getErrorMessage(page.props.errors?.file);
    if (fileError) return fileError;

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

const photoError = computed(() => {
    return (
        clientPhotoError.value ||
        getErrorMessage(form.errors.photo) ||
        getErrorMessage(page.props.errors?.photo) ||
        sharedUploadFallbackError.value ||
        ""
    );
});

const idCardError = computed(() => {
    return (
        clientIdCardError.value ||
        getErrorMessage(form.errors.id_card) ||
        getErrorMessage(page.props.errors?.id_card) ||
        sharedUploadFallbackError.value ||
        ""
    );
});

const transcriptError = computed(() => {
    return (
        clientTranscriptError.value ||
        getErrorMessage(form.errors.transcript) ||
        getErrorMessage(page.props.errors?.transcript) ||
        sharedUploadFallbackError.value ||
        ""
    );
});
</script>

<template>
    <Head title="Add Student" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Add Student
                </h2>
                <Link
                    :href="route('students.index')"
                    class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50"
                >
                    Back to list
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'Student Management',
                            href: route('students.index'),
                        },
                        { label: 'Add Student' },
                    ]"
                />
            </div>
        </template>

        <div class="portal-card p-6">
            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Linked User (login account)
                        </label>
                        <select
                            v-model="form.user_id"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            :disabled="props.users.length === 0"
                        >
                            <option value="">
                                {{
                                    props.users.length === 0
                                        ? "No users available (all have student records)"
                                        : "Select user"
                                }}
                            </option>
                            <option
                                v-for="user in props.users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <p
                            v-if="props.users.length === 0"
                            class="mt-1 text-xs text-amber-600"
                        >
                            All registered users already have student records.
                            New users will appear here after they register.
                        </p>
                        <p
                            v-if="form.errors.user_id"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.user_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Student Number
                            <span class="text-[11px] text-slate-500">
                                (leave blank to auto-generate)
                            </span>
                        </label>
                        <input
                            v-model="form.student_no"
                            type="text"
                            placeholder="Auto if left blank"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.student_no"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.student_no }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Full Name (from linked user)
                        </label>
                        <input
                            type="text"
                            :value="selectedUser?.name || ''"
                            disabled
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p class="mt-1 text-xs text-slate-500">
                            Name is managed in the user account.
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Date of Birth
                        </label>
                        <input
                            v-model="form.dob"
                            type="date"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.dob"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.dob }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Gender (optional)
                        </label>
                        <select
                            v-model="form.gender"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        >
                            <option value="">Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        <p
                            v-if="form.errors.gender"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.gender }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Nationality (optional)
                        </label>
                        <input
                            v-model="form.nationality"
                            type="text"
                            placeholder="e.g. Nigerian"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.nationality"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.nationality }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Email (from linked user)
                        </label>
                        <input
                            type="email"
                            :value="selectedUser?.email || ''"
                            disabled
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p class="mt-1 text-xs text-slate-500">
                            Email is managed in the user account.
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Phone
                        </label>
                        <input
                            v-model="form.phone"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.phone"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.phone }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="duplicateWarnings.length > 0"
                    class="rounded-lg border border-amber-300 bg-amber-50 p-3"
                >
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">
                        Duplicate warning
                    </p>
                    <p class="mt-1 text-xs text-amber-700">
                        Similar student records were found. Review these before saving a new profile.
                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-amber-700">
                        <li
                            v-for="warning in duplicateWarnings"
                            :key="warning.id"
                        >
                            {{ warning.full_name }} ({{ warning.student_no || "No student no" }}, {{ warning.email }}) - {{ warning.reasons.join(", ") }}
                        </li>
                    </ul>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                    >
                        Address (optional)
                    </label>
                    <textarea
                        v-model="form.address"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                    ></textarea>
                    <p
                        v-if="form.errors.address"
                        class="mt-1 text-xs text-red-600"
                    >
                        {{ form.errors.address }}
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Programme
                        </label>
                        <select
                            v-model="form.programme"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        >
                            <option value="">Select programme</option>
                            <option
                                v-for="programme in props.programmes"
                                :key="programme"
                                :value="programme"
                            >
                                {{ programme }}
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-slate-500">
                            Students will enroll in specific courses themselves
                            through the dashboard.
                        </p>
                        <p
                            v-if="form.errors.programme"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.programme }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Intake Year
                        </label>
                        <input
                            v-model="form.intake_year"
                            type="text"
                            placeholder="e.g. 2025"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.intake_year"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.intake_year }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Previous Institution
                        </label>
                        <input
                            v-model="form.previous_institution"
                            type="text"
                            placeholder="e.g. ABC Secondary School"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.previous_institution"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.previous_institution }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Previous Qualification
                        </label>
                        <input
                            v-model="form.previous_qualification"
                            type="text"
                            placeholder="e.g. WAEC / High School Diploma"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.previous_qualification"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.previous_qualification }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Emergency Contact Name (optional)
                        </label>
                        <input
                            v-model="form.emergency_contact_name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.emergency_contact_name"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.emergency_contact_name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Emergency Contact Phone (optional)
                        </label>
                        <input
                            v-model="form.emergency_contact_phone"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.emergency_contact_phone"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.emergency_contact_phone }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Student Status
                        </label>
                        <select
                            v-model="form.status"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        >
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="graduated">Graduated</option>
                        </select>
                        <p
                            v-if="form.errors.status"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                    >
                        Notes / Remarks (optional)
                    </label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                    ></textarea>
                    <p
                        v-if="form.errors.notes"
                        class="mt-1 text-xs text-red-600"
                    >
                        {{ form.errors.notes }}
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Photo (optional)
                            <span class="text-[11px] text-slate-500"
                                >(JPEG/PNG, max 2MB)</span
                            >
                        </label>
                        <input
                            type="file"
                            accept="image/jpeg,image/jpg,image/png"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-portal-navy file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-portal-navy/90 focus:border-portal-navy focus:ring-portal-navy"
                            @change="onPhotoChange"
                        />
                        <p
                            v-if="photoError"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ photoError }}
                        </p>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-6">
                    <h3
                        class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-700"
                    >
                        Required Documents
                    </h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label
                                class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                            >
                                ID Card
                                <span class="text-[11px] text-slate-500"
                                    >(PDF/JPEG/PNG, max 5MB)</span
                                >
                            </label>
                            <input
                                type="file"
                                accept=".pdf,image/jpeg,image/jpg,image/png"
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-portal-navy file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-portal-navy/90 focus:border-portal-navy focus:ring-portal-navy"
                                @change="onIdCardChange"
                            />
                            <p
                                v-if="idCardError"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ idCardError }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                            >
                                Academic Transcript
                                <span class="text-[11px] text-slate-500"
                                    >(PDF/JPEG/PNG, max 5MB)</span
                                >
                            </label>
                            <input
                                type="file"
                                accept=".pdf,image/jpeg,image/jpg,image/png"
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-portal-navy file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-portal-navy/90 focus:border-portal-navy focus:ring-portal-navy"
                                @change="onTranscriptChange"
                            />
                            <p
                                v-if="transcriptError"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ transcriptError }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-full bg-portal-navy px-5 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-portal-navy/60 hover:bg-portal-navy/90"
                        :disabled="form.processing"
                    >
                        Save student
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
