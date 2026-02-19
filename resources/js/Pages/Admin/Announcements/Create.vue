<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const announcementTemplates = [
    {
        key: "exam_notice",
        label: "Exam Notice",
        title: "Exam Schedule Notice",
        body: "Please review the updated exam timetable and prepare accordingly. Contact your lecturer or department office if you have timetable clashes.",
        priority: "important",
        require_ack: true,
        audience_roles: ["student"],
    },
    {
        key: "timetable_change",
        label: "Timetable Change",
        title: "Timetable Change Notification",
        body: "A timetable update has been applied. Please check your latest timetable and confirm the new day, time, and classroom details.",
        priority: "urgent",
        require_ack: true,
        audience_roles: ["student", "teacher"],
    },
    {
        key: "fee_deadline",
        label: "Fee Deadline",
        title: "Upcoming Fee Payment Deadline",
        body: "This is a reminder to clear any outstanding fee balance before the deadline. Please complete payment early to avoid service restrictions.",
        priority: "important",
        require_ack: false,
        audience_roles: ["student"],
    },
];

const form = useForm({
    title: "",
    body: "",
    priority: "info",
    pinned: false,
    require_ack: false,
    audience: {
        roles: ["all"],
    },
    publish_at: "",
    expires_at: "",
});
const selectedTemplate = ref("");

const submit = () => {
    form.post(route("admin.announcements.store"));
};

const applyTemplate = () => {
    const template = announcementTemplates.find(
        (item) => item.key === selectedTemplate.value
    );
    if (!template) {
        return;
    }

    form.title = template.title;
    form.body = template.body;
    form.priority = template.priority;
    form.require_ack = template.require_ack;
    form.audience = {
        roles: [...template.audience_roles],
    };
};
</script>

<template>
    <Head title="Create Announcement" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Create Announcement
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        {
                            label: 'Announcements',
                            href: route('admin.announcements.index'),
                        },
                        { label: 'Create Announcement' },
                    ]"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Role-scoped template
                                </p>
                                <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <select
                                        v-model="selectedTemplate"
                                        class="block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-64"
                                    >
                                        <option value="">Select template (optional)</option>
                                        <option
                                            v-for="template in announcementTemplates"
                                            :key="template.key"
                                            :value="template.key"
                                        >
                                            {{ template.label }}
                                        </option>
                                    </select>
                                    <button
                                        type="button"
                                        class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                        :disabled="!selectedTemplate"
                                        @click="applyTemplate"
                                    >
                                        Apply template
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.title,
                                    }"
                                />
                                <p
                                    v-if="form.errors.title"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Body <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    v-model="form.body"
                                    rows="6"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.body,
                                    }"
                                ></textarea>
                                <p
                                    v-if="form.errors.body"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.body }}
                                </p>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Priority
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.priority"
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    >
                                        <option value="info">Info</option>
                                        <option value="important">
                                            Important
                                        </option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                    <p
                                        v-if="form.errors.priority"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.priority }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Audience (roles)
                                    </label>
                                    <div class="mt-2 space-y-2 text-sm">
                                        <label
                                            class="flex items-center gap-2 text-slate-700"
                                        >
                                            <input
                                                type="checkbox"
                                                value="all"
                                                v-model="form.audience.roles"
                                                class="rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                            />
                                            All roles
                                        </label>
                                        <div
                                            class="grid grid-cols-3 gap-2 pl-6 text-xs text-slate-700"
                                        >
                                            <label
                                                class="flex items-center gap-2"
                                            >
                                                <input
                                                    type="checkbox"
                                                    value="student"
                                                    v-model="
                                                        form.audience.roles
                                                    "
                                                    class="rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                                />
                                                Students
                                            </label>
                                            <label
                                                class="flex items-center gap-2"
                                            >
                                                <input
                                                    type="checkbox"
                                                    value="teacher"
                                                    v-model="
                                                        form.audience.roles
                                                    "
                                                    class="rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                                />
                                                Teachers
                                            </label>
                                            <label
                                                class="flex items-center gap-2"
                                            >
                                                <input
                                                    type="checkbox"
                                                    value="staff"
                                                    v-model="
                                                        form.audience.roles
                                                    "
                                                    class="rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                                />
                                                Staff
                                            </label>
                                        </div>
                                    </div>
                                    <p
                                        v-if="form.errors['audience.roles']"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors["audience.roles"] }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Publish at (optional)
                                    </label>
                                    <input
                                        v-model="form.publish_at"
                                        type="datetime-local"
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    />
                                    <p
                                        v-if="form.errors.publish_at"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.publish_at }}
                                    </p>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700"
                                    >
                                        Expires at (optional)
                                    </label>
                                    <input
                                        v-model="form.expires_at"
                                        type="datetime-local"
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    />
                                    <p
                                        v-if="form.errors.expires_at"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.expires_at }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-3 md:grid-cols-3">
                                <label
                                    class="flex items-center gap-2 text-sm text-slate-700"
                                >
                                    <input
                                        v-model="form.pinned"
                                        type="checkbox"
                                        class="rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                    Pin announcement
                                </label>
                                <label
                                    class="flex items-center gap-2 text-sm text-slate-700"
                                >
                                    <input
                                        v-model="form.require_ack"
                                        type="checkbox"
                                        class="rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    />
                                    Require acknowledgement
                                </label>
                            </div>

                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
                                <Link
                                    :href="route('admin.announcements.index')"
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
                                    <span v-else>Create</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
