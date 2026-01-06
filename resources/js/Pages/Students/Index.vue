<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    students: Object,
});
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
                    Manage student academic records created for your University Academic Portal project.
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
                        <tr
                            v-for="student in students.data"
                            :key="student.id"
                        >
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
                        </tr>
                        <tr v-if="students.data.length === 0">
                            <td
                                colspan="5"
                                class="px-3 py-6 text-center text-sm text-slate-500"
                            >
                                No students found yet. Use "Add student" to create your first record.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


