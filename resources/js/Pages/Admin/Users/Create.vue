<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    availableRoles: {
        type: Array,
        required: true,
    },
    duplicateHintUsers: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: "",
    email: "",
    role: "student",
    photo: null,
});

const submit = () => {
    form.post(route("admin.users.store"), {
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
    const name = normalize(form.name);
    const email = normalize(form.email);

    if (name === "" && email === "") {
        return [];
    }

    return (props.duplicateHintUsers || [])
        .map((user) => {
            const existingName = normalize(user?.name);
            const existingEmail = normalize(user?.email);
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
                reasons.push("same name");
                score += 70;
            } else if (
                name !== "" &&
                existingName !== "" &&
                (existingName.includes(name) || name.includes(existingName)) &&
                Math.min(name.length, existingName.length) >= 4
            ) {
                reasons.push("similar name");
                score += 35;
            }

            if (score === 0) {
                return null;
            }

            return {
                id: user.id,
                name: user.name,
                email: user.email,
                reasons,
                score,
            };
        })
        .filter(Boolean)
        .sort((a, b) => b.score - a.score)
        .slice(0, 5);
});
</script>

<template>
    <Head title="Create User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Create User
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'User Management',
                            href: route('admin.users.index'),
                        },
                        { label: 'Create User' },
                    ]"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label
                                    for="name"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.name,
                                    }"
                                />
                                <p
                                    v-if="form.errors.name"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label
                                    for="email"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.email,
                                    }"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <div
                                v-if="duplicateWarnings.length > 0"
                                class="rounded-lg border border-amber-300 bg-amber-50 p-3"
                            >
                                <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">
                                    Duplicate warning
                                </p>
                                <p class="mt-1 text-xs text-amber-700">
                                    Similar user records were found. Review before creating a new account.
                                </p>
                                <ul class="mt-2 space-y-1 text-xs text-amber-700">
                                    <li
                                        v-for="warning in duplicateWarnings"
                                        :key="warning.id"
                                    >
                                        {{ warning.name }} ({{ warning.email }}) - {{ warning.reasons.join(", ") }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Role -->
                            <div>
                                <label
                                    for="role"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.role,
                                    }"
                                >
                                    <option
                                        v-for="role in props.availableRoles"
                                        :key="role"
                                        :value="role"
                                    >
                                        {{ role }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.role"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.role }}
                                </p>
                            </div>

                            <!-- Photo (optional) -->
                            <div>
                                <label
                                    for="photo"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Profile Photo
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

                            <p class="text-xs text-slate-500">
                                A secure temporary password will be generated.
                                The user will receive verification and password
                                setup links by email.
                            </p>

                            <!-- Form Actions -->
                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
                                <Link
                                    :href="route('admin.users.index')"
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
                                    <span v-else>Create User</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
