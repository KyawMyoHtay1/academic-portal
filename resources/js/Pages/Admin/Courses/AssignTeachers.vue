<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    course: {
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
    form.put(route("admin.courses.assign-teachers.update", props.course.id));
};
</script>

<template>
    <Head title="Assign Teachers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Assign Teachers to Course
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Course Management', href: route('admin.courses.index') },
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
                            <span class="font-medium">Course:</span>
                            {{ course.course_code }} - {{ course.title }}
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
                                    v-if="teachers.length === 0"
                                    class="rounded-md border border-slate-200 bg-slate-50 p-4 text-center text-sm text-slate-500"
                                >
                                    No teachers available. Please create teacher
                                    accounts first.
                                </div>

                                <div
                                    v-else
                                    class="space-y-2 rounded-md border border-slate-200 bg-white p-4 max-h-96 overflow-y-auto"
                                >
                                    <label
                                        v-for="teacher in teachers"
                                        :key="teacher.id"
                                        class="flex items-center space-x-3 p-2 hover:bg-slate-50 rounded cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="teacher.id"
                                            v-model="form.teacher_ids"
                                            class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                        />
                                        <div class="flex-1">
                                            <span
                                                class="text-sm font-medium text-slate-900"
                                            >
                                                {{ teacher.name }}
                                            </span>
                                            <span
                                                class="ml-2 text-xs text-slate-500"
                                            >
                                                {{ teacher.email }}
                                            </span>
                                        </div>
                                    </label>
                                </div>

                                <p
                                    v-if="form.errors.teacher_ids"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.teacher_ids }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Link
                                    :href="route('admin.courses.index')"
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
                                        Assigning...
                                    </span>
                                    <span v-else>Assign Teachers</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

