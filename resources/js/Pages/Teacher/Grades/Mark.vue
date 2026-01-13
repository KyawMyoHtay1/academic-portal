<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";

const props = defineProps({
    subject: {
        type: Object,
        required: true,
    },
    students: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    grades: props.students.map((student) => ({
        student_id: student.id,
        score: student.score ?? "",
    })),
});

const submit = () => {
    form.post(route("teacher.grades.store", props.subject.id));
};
</script>

<template>
    <Head title="Enter Grades" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'Grades',
                            href: route('teacher.grades.index'),
                        },
                        { label: props.subject.title },
                    ]"
                />
            </div>
        </template>

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Enter Grades
                </h2>
                <Link
                    :href="route('teacher.grades.index')"
                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Back to Subjects
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <div class="mb-6">
                        <p class="text-sm text-slate-600">
                            <span class="font-medium">Subject:</span>
                            {{ subject.subject_code }} - {{ subject.title }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Course: {{ subject.course_code }} -
                            {{ subject.course_title }}
                        </p>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Students Grades Table -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 mb-3"
                                >
                                    Student Grades
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
                                    <table
                                        class="min-w-full divide-y divide-slate-200"
                                    >
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Student
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Score (0 - 100)
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="divide-y divide-slate-200 bg-white"
                                        >
                                            <tr
                                                v-for="(
                                                    record, index
                                                ) in form.grades"
                                                :key="record.student_id"
                                                class="bg-white"
                                            >
                                                <td
                                                    class="px-4 py-4 text-sm text-slate-700"
                                                >
                                                    <div
                                                        class="flex items-center gap-3"
                                                    >
                                                        <div
                                                            class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                                        >
                                                            <img
                                                                v-if="
                                                                    students[
                                                                        index
                                                                    ]?.photo
                                                                "
                                                                :src="`/storage/${students[index].photo}`"
                                                                :alt="`Photo for ${students[index].full_name}`"
                                                                class="h-full w-full object-cover"
                                                            />
                                                            <span
                                                                v-else
                                                                class="text-xs font-semibold text-slate-500"
                                                            >
                                                                {{
                                                                    students[
                                                                        index
                                                                    ]?.full_name
                                                                        .charAt(
                                                                            0
                                                                        )
                                                                        .toUpperCase()
                                                                }}
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="flex flex-col"
                                                        >
                                                            <span
                                                                class="text-sm font-medium text-slate-900"
                                                            >
                                                                {{
                                                                    students[
                                                                        index
                                                                    ]?.full_name
                                                                }}
                                                            </span>
                                                            <span
                                                                class="text-xs text-slate-500"
                                                            >
                                                                {{
                                                                    students[
                                                                        index
                                                                    ]
                                                                        ?.student_no
                                                                }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="whitespace-nowrap px-4 py-4 text-center"
                                                >
                                                    <input
                                                        v-model="record.score"
                                                        type="number"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        class="w-28 rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                                    />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p
                                    v-if="form.errors.grades"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.grades }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
                                <Link
                                    :href="route('teacher.grades.index')"
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
                                    <span v-else>Save Grades</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
