<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    role: {
        type: String,
        required: true,
    },
    preferences: {
        type: Object,
        default: () => ({}),
    },
    status: {
        type: String,
        default: null,
    },
    attendanceAlerts: {
        type: Object,
        default: () => ({
            low_threshold: 75,
            cooldown_days: 7,
            can_manage_defaults: false,
        }),
    },
});

const canManageAttendanceAlertDefaults = Boolean(
    props.attendanceAlerts?.can_manage_defaults ?? false
);

const form = useForm({
    email_announcements: props.preferences.email_announcements ?? true,
    email_messages: props.preferences.email_messages ?? true,
    email_notifications: props.preferences.email_notifications ?? true,
    notify_timetable: props.preferences.notify_timetable ?? true,
    notify_attendance: props.preferences.notify_attendance ?? true,
    notify_grades: props.preferences.notify_grades ?? true,
    notify_grade_review: props.preferences.notify_grade_review ?? true,
    notify_fees: props.preferences.notify_fees ?? true,
    notify_messages: props.preferences.notify_messages ?? true,
    notify_assignments: props.preferences.notify_assignments ?? true,
    notify_announcements: props.preferences.notify_announcements ?? true,
    ...(canManageAttendanceAlertDefaults
        ? {
              attendance_low_threshold:
                  props.attendanceAlerts.low_threshold ?? 75,
              attendance_cooldown_days:
                  props.attendanceAlerts.cooldown_days ?? 7,
          }
        : {}),
});

const roleLabel = computed(() => {
    const r = props.role;
    if (r === "student") return "Student";
    if (r === "teacher") return "Teacher";
    if (r === "staff" || r === "admin") return "Staff";
    return r ? r.charAt(0).toUpperCase() + r.slice(1) : "User";
});

const submit = () => {
    form.patch(route("settings.update"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Settings
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Settings' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                <!-- Status message -->
                <div
                    v-if="status"
                    class="portal-card rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                >
                    {{ status }}
                </div>

                <!-- Account section -->
                <div class="portal-card overflow-hidden shadow sm:rounded-lg">
                    <div class="border-b border-slate-200 bg-slate-50/80 px-4 py-3 sm:px-6">
                        <h3 class="text-base font-semibold text-slate-900">
                            Account
                        </h3>
                        <p class="mt-1 text-sm text-slate-600">
                            Manage your profile, password, and account details.
                        </p>
                    </div>
                    <div class="px-4 py-4 sm:px-6">
                        <Link
                            :href="route('profile.edit')"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                        >
                            <span>Edit profile &amp; password</span>
                            <svg
                                class="h-4 w-4 text-slate-400"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </Link>
                        <p class="mt-2 text-xs text-slate-500">
                            Update your name, email, photo, and password.
                        </p>
                    </div>
                </div>

                <!-- Role-specific profile link (Student) -->
                <div
                    v-if="role === 'student'"
                    class="portal-card overflow-hidden shadow sm:rounded-lg"
                >
                    <div class="border-b border-slate-200 bg-slate-50/80 px-4 py-3 sm:px-6">
                        <h3 class="text-base font-semibold text-slate-900">
                            Student profile
                        </h3>
                        <p class="mt-1 text-sm text-slate-600">
                            View and edit your student record (programme, ID, etc.).
                        </p>
                    </div>
                    <div class="px-4 py-4 sm:px-6">
                        <Link
                            :href="route('student.profile.show')"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                        >
                            <span>Student profile</span>
                            <svg
                                class="h-4 w-4 text-slate-400"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Notifications section -->
                <div class="portal-card overflow-hidden shadow sm:rounded-lg">
                    <div class="border-b border-slate-200 bg-slate-50/80 px-4 py-3 sm:px-6">
                        <h3 class="text-base font-semibold text-slate-900">
                            Email notifications
                        </h3>
                        <p class="mt-1 text-sm text-slate-600">
                            Choose which emails you receive from the portal.
                        </p>
                    </div>
                    <form @submit.prevent="submit" class="px-4 py-4 sm:px-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                <div class="flex-1">
                                    <InputLabel
                                        for="email_announcements"
                                        value="Announcements"
                                        class="font-medium text-slate-900"
                                    />
                                    <p class="mt-0.5 text-sm text-slate-500">
                                        Receive emails when new announcements are published.
                                    </p>
                                </div>
                                <Checkbox
                                    id="email_announcements"
                                    v-model:checked="form.email_announcements"
                                    class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                />
                            </div>
                            <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                <div class="flex-1">
                                    <InputLabel
                                        for="email_messages"
                                        value="Messages"
                                        class="font-medium text-slate-900"
                                    />
                                    <p class="mt-0.5 text-sm text-slate-500">
                                        Receive emails when you get a new message.
                                    </p>
                                </div>
                                <Checkbox
                                    id="email_messages"
                                    v-model:checked="form.email_messages"
                                    class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                />
                            </div>
                            <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                <div class="flex-1">
                                    <InputLabel
                                        for="email_notifications"
                                        value="Portal notifications"
                                        class="font-medium text-slate-900"
                                    />
                                    <p class="mt-0.5 text-sm text-slate-500">
                                        Receive emails for grades, assignments, and other alerts.
                                    </p>
                                </div>
                                <Checkbox
                                    id="email_notifications"
                                    v-model:checked="form.email_notifications"
                                    class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                />
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.email_announcements" />
                        <InputError class="mt-2" :message="form.errors.email_messages" />
                        <InputError class="mt-2" :message="form.errors.email_notifications" />
                        <InputError class="mt-2" :message="form.errors.notify_timetable" />
                        <InputError class="mt-2" :message="form.errors.notify_attendance" />
                        <InputError class="mt-2" :message="form.errors.notify_grades" />
                        <InputError class="mt-2" :message="form.errors.notify_grade_review" />
                        <InputError class="mt-2" :message="form.errors.notify_fees" />
                        <InputError class="mt-2" :message="form.errors.notify_messages" />
                        <InputError class="mt-2" :message="form.errors.notify_assignments" />
                        <InputError class="mt-2" :message="form.errors.notify_announcements" />
                        <InputError class="mt-2" :message="form.errors.attendance_low_threshold" />
                        <InputError class="mt-2" :message="form.errors.attendance_cooldown_days" />

                        <div class="mt-6 border-t border-slate-200 pt-6">
                            <h4 class="text-sm font-semibold text-slate-900">
                                In-app notification categories
                            </h4>
                            <p class="mt-1 text-sm text-slate-500">
                                Control which modules create alerts in your notification center.
                            </p>

                            <div class="mt-4 space-y-4">
                                <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="flex-1">
                                        <InputLabel
                                            for="notify_timetable"
                                            value="Timetable updates"
                                            class="font-medium text-slate-900"
                                        />
                                        <p class="mt-0.5 text-sm text-slate-500">
                                            Changes to schedule entries and timetable updates.
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="notify_timetable"
                                        v-model:checked="form.notify_timetable"
                                        class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                </div>

                                <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="flex-1">
                                        <InputLabel
                                            for="notify_attendance"
                                            value="Attendance alerts"
                                            class="font-medium text-slate-900"
                                        />
                                        <p class="mt-0.5 text-sm text-slate-500">
                                            Attendance recording and low-attendance warnings.
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="notify_attendance"
                                        v-model:checked="form.notify_attendance"
                                        class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                </div>

                                <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="flex-1">
                                        <InputLabel
                                            for="notify_grades"
                                            value="Grade updates"
                                            class="font-medium text-slate-900"
                                        />
                                        <p class="mt-0.5 text-sm text-slate-500">
                                            Published grade changes for your records.
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="notify_grades"
                                        v-model:checked="form.notify_grades"
                                        class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                </div>

                                <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="flex-1">
                                        <InputLabel
                                            for="notify_grade_review"
                                            value="Grade review workflow"
                                            class="font-medium text-slate-900"
                                        />
                                        <p class="mt-0.5 text-sm text-slate-500">
                                            Grade review requests and review outcomes.
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="notify_grade_review"
                                        v-model:checked="form.notify_grade_review"
                                        class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                </div>

                                <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="flex-1">
                                        <InputLabel
                                            for="notify_assignments"
                                            value="Assignment alerts"
                                            class="font-medium text-slate-900"
                                        />
                                        <p class="mt-0.5 text-sm text-slate-500">
                                            Assignment publish and update announcements for your courses.
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="notify_assignments"
                                        v-model:checked="form.notify_assignments"
                                        class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                </div>

                                <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="flex-1">
                                        <InputLabel
                                            for="notify_announcements"
                                            value="Announcement alerts"
                                            class="font-medium text-slate-900"
                                        />
                                        <p class="mt-0.5 text-sm text-slate-500">
                                            New announcements and announcement updates targeted to you.
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="notify_announcements"
                                        v-model:checked="form.notify_announcements"
                                        class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                </div>

                                <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="flex-1">
                                        <InputLabel
                                            for="notify_messages"
                                            value="Message alerts"
                                            class="font-medium text-slate-900"
                                        />
                                        <p class="mt-0.5 text-sm text-slate-500">
                                            New direct messages sent to your inbox.
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="notify_messages"
                                        v-model:checked="form.notify_messages"
                                        class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                </div>

                                <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="flex-1">
                                        <InputLabel
                                            for="notify_fees"
                                            value="Fee alerts"
                                            class="font-medium text-slate-900"
                                        />
                                        <p class="mt-0.5 text-sm text-slate-500">
                                            Fee status updates and overdue reminders.
                                        </p>
                                    </div>
                                    <Checkbox
                                        id="notify_fees"
                                        v-model:checked="form.notify_fees"
                                        class="h-5 w-5 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="canManageAttendanceAlertDefaults"
                            class="mt-6 border-t border-slate-200 pt-6"
                        >
                            <h4 class="text-sm font-semibold text-slate-900">
                                Low-attendance alert defaults
                            </h4>
                            <p class="mt-1 text-sm text-slate-500">
                                Set global defaults used by attendance reports and alert jobs.
                            </p>

                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <div class="rounded-lg border border-slate-200 bg-white p-4">
                                    <InputLabel
                                        for="attendance_low_threshold"
                                        value="Default threshold (%)"
                                        class="font-medium text-slate-900"
                                    />
                                    <input
                                        id="attendance_low_threshold"
                                        v-model.number="form.attendance_low_threshold"
                                        type="number"
                                        min="1"
                                        max="100"
                                        step="0.1"
                                        class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                    />
                                    <p class="mt-1 text-xs text-slate-500">
                                        Used when no course/subject threshold override is set.
                                    </p>
                                </div>

                                <div class="rounded-lg border border-slate-200 bg-white p-4">
                                    <InputLabel
                                        for="attendance_cooldown_days"
                                        value="Default cooldown (days)"
                                        class="font-medium text-slate-900"
                                    />
                                    <input
                                        id="attendance_cooldown_days"
                                        v-model.number="form.attendance_cooldown_days"
                                        type="number"
                                        min="0"
                                        max="90"
                                        step="1"
                                        class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                    />
                                    <p class="mt-1 text-xs text-slate-500">
                                        Minimum wait before sending another low-attendance alert.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                Save preferences
                            </PrimaryButton>
                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-if="form.recentlySuccessful"
                                    class="text-sm text-slate-600"
                                >
                                    Saved.
                                </p>
                            </Transition>
                        </div>
                    </form>
                </div>

                <!-- Role badge -->
                <div
                    class="portal-card flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-600"
                >
                    <span class="font-medium text-slate-500">Logged in as</span>
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 font-medium text-slate-800"
                    >
                        {{ roleLabel }}
                    </span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
