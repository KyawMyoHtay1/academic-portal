<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
    students: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    date: new Date().toISOString().split("T")[0], // Today's date as default
    attendance: props.students.map((student) => ({
        student_id: student.id,
        status: "present", // Default to present
    })),
});

const submit = () => {
    form.post(route("teacher.attendance.store", props.course.id));
};
</script>

<template>
    <Head title="Mark Attendance" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Mark Attendance
                </h2>
                <Link
                    :href="route('teacher.attendance.index')"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Back to Courses
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <div class="mb-6">
                        <p class="text-sm text-slate-600">
                            <span class="font-medium">Course:</span>
                            {{ course.course_code }} - {{ course.title }}
                        </p>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Date Selection -->
                            <div>
                                <label
                                    for="date"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="date"
                                    v-model="form.date"
                                    type="date"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.date,
                                    }"
                                />
                                <p
                                    v-if="form.errors.date"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.date }}
                                </p>
                            </div>

                            <!-- Students Attendance Table -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-3">
                                    Student Attendance
                                </label>

                                <div
                                    v-if="students.length === 0"
                                    class="rounded-md border border-slate-200 bg-slate-50 p-4 text-center text-sm text-slate-500"
                                >
                                    No students enrolled in this course.
                                </div>

                                <div
                                    v-else
                                    class="overflow-hidden rounded-md border border-slate-200"
                                >
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Student No.
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Name
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 bg-white">
                                            <tr
                                                v-for="(record, index) in form.attendance"
                                                :key="record.student_id"
                                                class="bg-white"
                                            >
                                                <td
                                                    class="whitespace-nowrap px-4 py-4 text-sm text-slate-900"
                                                >
                                                    {{
                                                        students.find(
                                                            (s) =>
                                                                s.id ===
                                                                record.student_id
                                                        )?.student_no
                                                    }}
                                                </td>
                                                <td
                                                    class="px-4 py-4 text-sm text-slate-700"
                                                >
                                                    {{
                                                        students.find(
                                                            (s) =>
                                                                s.id ===
                                                                record.student_id
                                                        )?.full_name
                                                    }}
                                                </td>
                                                <td
                                                    class="whitespace-nowrap px-4 py-4 text-center"
                                                >
                                                    <select
                                                        v-model="record.status"
                                                        class="rounded-md border-slate-300 text-sm focus:border-portal-navy focus:ring-portal-navy"
                                                    >
                                                        <option value="present">
                                                            Present
                                                        </option>
                                                        <option value="absent">
                                                            Absent
                                                        </option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p
                                    v-if="form.errors.attendance"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.attendance }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Link
                                    :href="route('teacher.attendance.index')"
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
                                        Saving...
                                    </span>
                                    <span v-else>Save Attendance</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

