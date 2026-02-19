<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";

const props = defineProps({
    timetable: {
        type: Object,
        required: true,
    },
    subjects: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    subject_id: props.timetable.subject_id,
    day_of_week: props.timetable.day_of_week,
    start_time: props.timetable.start_time,
    end_time: props.timetable.end_time,
    location: props.timetable.location || "",
});

const submit = () => {
    form.put(route("admin.timetables.update", props.timetable.id));
};

const conflictDetails = computed(() => {
    const raw = form.errors.conflict_details;
    if (!raw) {
        return [];
    }

    if (Array.isArray(raw)) {
        return raw;
    }

    try {
        const parsed = JSON.parse(String(raw));
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
});

watch(
    () => [form.subject_id, form.day_of_week, form.start_time, form.end_time],
    () => {
        if (form.errors.conflict_details || form.errors.start_time) {
            form.clearErrors("conflict_details", "start_time");
        }
    }
);
</script>

<template>
    <Head title="Edit Timetable Entry" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Edit Timetable Entry
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Timetable Management', href: route('admin.timetables.index') },
                        { label: 'Edit Entry' },
                    ]"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Subject -->
                            <div>
                                <label
                                    for="subject_id"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="subject_id"
                                    v-model="form.subject_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.subject_id,
                                    }"
                                >
                                    <option value="">Select subject</option>
                                    <option
                                        v-for="subject in subjects"
                                        :key="subject.id"
                                        :value="subject.id"
                                    >
                                        {{ subject.subject_code }} -
                                        {{ subject.title }} ({{
                                            subject.course_code
                                        }})
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.subject_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.subject_id }}
                                </p>
                            </div>

                            <!-- Day of week -->
                            <div>
                                <label
                                    for="day_of_week"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Day of Week
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="day_of_week"
                                    v-model="form.day_of_week"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.day_of_week,
                                    }"
                                >
                                    <option value="">Select day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>
                                <p
                                    v-if="form.errors.day_of_week"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.day_of_week }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Conflict details will be shown if this update overlaps with another session.
                                </p>
                            </div>

                            <!-- Time -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        for="start_time"
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Start Time
                                        <span class="text-red-500">*</span>
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
                                        End Time
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="end_time"
                                        v-model="form.end_time"
                                        type="time"
                                        required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                            'border-red-300 focus:border-red-500 focus:ring-red-500':
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

                            <div
                                v-if="conflictDetails.length > 0"
                                class="rounded-lg border border-red-200 bg-red-50 p-4"
                            >
                                <p class="text-xs font-semibold uppercase tracking-wide text-red-700">
                                    Conflict Details
                                </p>
                                <p class="mt-1 text-xs text-red-700">
                                    This update overlaps with the following timetable entries:
                                </p>
                                <div class="mt-3 space-y-2">
                                    <div
                                        v-for="(item, index) in conflictDetails"
                                        :key="`${item.subject_code}-${item.time_range}-${index}`"
                                        class="rounded-md border border-red-200 bg-white px-3 py-2 text-xs text-slate-700"
                                    >
                                        <p class="font-semibold text-slate-900">
                                            {{ item.subject_code }} - {{ item.subject_title }}
                                        </p>
                                        <p class="mt-0.5 text-slate-600">
                                            {{ item.course_code }} - {{ item.course_title }}
                                        </p>
                                        <p class="mt-0.5 text-slate-600">
                                            {{ item.day_of_week }} | {{ item.time_range }} | {{ item.location }}
                                        </p>
                                    </div>
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
                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
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
                                    <span v-if="form.processing"
                                        >Updating...</span
                                    >
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
