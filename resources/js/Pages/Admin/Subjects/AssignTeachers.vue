<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    subject: {
        type: Object,
        required: true,
    },
    teachers: {
        type: Array,
        required: true,
    },
    assignedTeacherIds: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    teacher_ids: props.assignedTeacherIds,
});

const submit = () => {
    form.put(route("admin.subjects.assign-teachers.update", props.subject.id));
};
</script>

<template>
    <Head title="Assign Teachers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Assign Teachers to Subject
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Subject Management', href: route('admin.subjects.index') },
                        { label: 'Assign Teachers' },
                    ]"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <div class="mb-6">
                        <p class="text-sm text-slate-600">
                            <span class="font-medium">Subject:</span>
                            {{ subject.subject_code }} - {{ subject.title }}
                        </p>
                        <p class="text-sm text-slate-600 mt-1">
                            <span class="font-medium">Course:</span>
                            {{ subject.course_code }} - {{ subject.course_title }}
                        </p>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 mb-3"
                                >
                                    Select Teachers
                                </label>

                                <div
                                    class="max-h-60 overflow-y-auto rounded-md border border-slate-300 bg-white"
                                >
                                    <div
                                        v-for="teacher in teachers"
                                        :key="teacher.id"
                                        class="flex items-center px-4 py-2 hover:bg-slate-50"
                                    >
                                        <input
                                            :id="`teacher-${teacher.id}`"
                                            v-model="form.teacher_ids"
                                            type="checkbox"
                                            :value="teacher.id"
                                            class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                        />
                                        <label
                                            :for="`teacher-${teacher.id}`"
                                            class="ml-3 flex-1 text-sm text-slate-700 cursor-pointer"
                                        >
                                            <div class="font-medium">
                                                {{ teacher.name }}
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ teacher.email }}
                                            </div>
                                        </label>
                                    </div>

                                    <div
                                        v-if="teachers.length === 0"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No teachers available. Create teacher
                                        accounts first.
                                    </div>
                                </div>

                                <p
                                    v-if="form.errors.teacher_ids"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.teacher_ids }}
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                                <Link
                                    :href="route('admin.subjects.index')"
                                    class="rounded-md bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{
                                        form.processing
                                            ? "Saving..."
                                            : "Save Assignments"
                                    }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
