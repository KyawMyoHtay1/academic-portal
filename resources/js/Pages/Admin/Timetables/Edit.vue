<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    timetable: {
        type: Object,
        required: true,
    },
    courses: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    course_id: props.timetable.course_id,
    day_of_week: props.timetable.day_of_week,
    start_time: props.timetable.start_time,
    end_time: props.timetable.end_time,
    location: props.timetable.location || "",
});

const submit = () => {
    form.put(route("admin.timetables.update", props.timetable.id));
};
</script>

<template>
    <Head title="Edit Timetable Entry" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Edit Timetable Entry
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Course -->
                            <div>
                                <label
                                    for="course_id"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Course <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="course_id"
                                    v-model="form.course_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.course_id,
                                    }"
                                >
                                    <option value="">Select course</option>
                                    <option
                                        v-for="course in courses"
                                        :key="course.id"
                                        :value="course.id"
                                    >
                                        {{ course.course_code }} - {{ course.title }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.course_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.course_id }}
                                </p>
                            </div>

                            <!-- Day of week -->
                            <div>
                                <label
                                    for="day_of_week"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Day of Week <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="day_of_week"
                                    v-model="form.day_of_week"
                                    type="text"
                                    placeholder="e.g., Monday"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.day_of_week,
                                    }"
                                />
                                <p
                                    v-if="form.errors.day_of_week"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.day_of_week }}
                                </p>
                            </div>

                            <!-- Time -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        for="start_time"
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Start Time <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="start_time"
                                        v-model="form.start_time"
                                        type="time"
                                        required
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                        :class="{
                                            'border-red-300 focus:border-red-500 focus:ring-red-500':
                                                form.errors.start_time,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.start_time"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.start_time }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="end_time"
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        End Time <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="end_time"
                                        v-model="form.end_time"
                                        type="time"
                                        required
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                        :class="{
                                            'border-red-300 focus-border-red-500 focus:ring-red-500':
                                                form.errors.end_time,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.end_time"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.end_time }}
                                    </p>
                                </div>
                            </div>

                            <!-- Location -->
                            <div>
                                <label
                                    for="location"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Location
                                </label>
                                <input
                                    id="location"
                                    v-model="form.location"
                                    type="text"
                                    placeholder="e.g., Room B201"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.location,
                                    }"
                                />
                                <p
                                    v-if="form.errors.location"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.location }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Link
                                    :href="route('admin.timetables.index')"
                                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Updating...</span>
                                    <span v-else>Update Entry</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


