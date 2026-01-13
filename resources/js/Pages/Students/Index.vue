<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineProps({
    students: Object,
});

const deleteStudent = (id) => {
    if (
        !confirm(
            "Are you sure you want to delete this student record? This action cannot be undone."
        )
    ) {
        return;
    }

    router.delete(route("students.destroy", id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Students" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Students
                </h2>
                <Link
                    :href="route('students.create')"
                    class="rounded-full bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-portal-navy/60 hover:bg-portal-navy/90"
                >
                    Add student
                </Link>
            </div>
        </template>

        <div class="portal-card">
            <div class="border-b border-slate-200 px-6 py-4">
                <p class="text-sm text-slate-600">
                    Manage student academic records created for your University
                    Academic Portal project.
                </p>
            </div>

            <div class="overflow-x-auto px-4 py-4 sm:px-6">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Photo
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Student No
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Name
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Email
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Programme
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Intake Year
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="student in students.data" :key="student.id">
                            <td class="px-3 py-2">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="student.photo"
                                            :src="`/storage/${student.photo}`"
                                            :alt="`Photo for ${student.full_name}`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-xs font-semibold text-slate-500"
                                        >
                                            {{ student.full_name[0] }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2 font-medium text-slate-900">
                                {{ student.student_no }}
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ student.full_name }}
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ student.email }}
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ student.programme }}
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ student.intake_year }}
                            </td>
                            <td class="px-3 py-2 text-right text-slate-700">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Link
                                        :href="
                                            route('students.edit', student.id)
                                        "
                                        class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteStudent(student.id)"
                                        class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="students.data.length === 0">
                            <td
                                colspan="6"
                                class="px-3 py-6 text-center text-sm text-slate-500"
                            >
                                No students found yet. Use "Add student" to
                                create your first record.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
