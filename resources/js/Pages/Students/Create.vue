<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    users: Array,
});

const form = useForm({
    user_id: "",
    student_no: "",
    full_name: "",
    dob: "",
    email: "",
    phone: "",
    programme: "",
    intake_year: "",
    photo: null,
});

const submit = () => {
    form.post(route("students.store"), {
        forceFormData: true,
    });
};
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
                        </label>
                        <input
                            v-model="form.student_no"
                            type="text"
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
                            Full Name
                        </label>
                        <input
                            v-model="form.full_name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.full_name"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.full_name }}
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
                            Email
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
                        <p
                            v-if="form.errors.email"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.email }}
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

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide text-slate-600"
                        >
                            Programme
                        </label>
                        <input
                            v-model="form.programme"
                            type="text"
                            placeholder="e.g. BSc (Hons) Computing"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                        />
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
                            Photo (optional)
                            <span class="text-[11px] text-slate-500"
                                >(JPEG/PNG, max 2MB)</span
                            >
                        </label>
                        <input
                            type="file"
                            accept="image/jpeg,image/jpg,image/png"
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-portal-navy file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-portal-navy/90 focus:border-portal-navy focus:ring-portal-navy"
                            @change="(e) => (form.photo = e.target.files[0])"
                        />
                        <p
                            v-if="form.errors.photo"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.photo }}
                        </p>
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
