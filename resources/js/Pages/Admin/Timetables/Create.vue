<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";

const props = defineProps({
    subjects: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    subject_id: "",
    day_of_week_list: [],
    start_time: "",
    end_time: "",
    location: "",
});

const dayOptions = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
];

const weekdayOptions = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

const selectedDayCount = computed(() => form.day_of_week_list.length);

const selectWeekdays = () => {
    form.day_of_week_list = [...weekdayOptions];
};

const selectAllDays = () => {
    form.day_of_week_list = [...dayOptions];
};

const clearSelectedDays = () => {
    form.day_of_week_list = [];
};

const submit = () => {
    form.post(route("admin.timetables.store"));
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
    () => [form.subject_id, form.day_of_week_list, form.start_time, form.end_time],
    () => {
        if (form.errors.conflict_details || form.errors.start_time || form.errors.day_of_week_list || form.errors.day_of_week) {
            form.clearErrors("conflict_details", "start_time", "day_of_week_list", "day_of_week");
        }
    },
    { deep: true }
);
</script>

<template>
    <Head title="Create Timetable Entry" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Create Timetable Entry
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Timetable Management', href: route('admin.timetables.index') },
                        { label: 'Create Entry' },
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

                            <!-- Days of week -->
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <label class="block text-sm font-medium text-slate-700">
                                    Days of Week
                                    <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button
                                            type="button"
                                            class="rounded-md border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                            @click="selectWeekdays"
                                        >
                                            Mon-Fri
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-md border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                            @click="selectAllDays"
                                        >
                                            All Days
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-md border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                            @click="clearSelectedDays"
                                        >
                                            Clear
                                        </button>
                                    </div>
                                </div>
                                <div
                                    class="mt-2 grid grid-cols-2 gap-2 rounded-md border p-3 sm:grid-cols-4"
                                    :class="{
                                        'border-red-300': form.errors.day_of_week_list || form.errors.day_of_week,
                                        'border-slate-200': !form.errors.day_of_week_list && !form.errors.day_of_week,
                                    }"
                                >
                                    <label
                                        v-for="day in dayOptions"
                                        :key="day"
                                        class="inline-flex items-center gap-2 text-sm text-slate-700"
                                    >
                                        <input
                                            v-model="form.day_of_week_list"
                                            type="checkbox"
                                            :value="day"
                                            class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                        />
                                        <span>{{ day }}</span>
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">
                                    Selected: {{ selectedDayCount }} day(s).
                                </p>
                                <p
                                    v-if="form.errors.day_of_week_list || form.errors.day_of_week"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.day_of_week_list || form.errors.day_of_week }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Conflict details will be shown if this schedule overlaps with another session.
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
                                    The selected time overlaps with the following timetable entries:
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
                                        >Creating...</span
                                    >
                                    <span v-else>Create {{ selectedDayCount > 1 ? "Entries" : "Entry" }}</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
