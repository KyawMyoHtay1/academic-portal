<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    announcements: {
        type: Array,
        required: true,
    },
});

const query = ref("");

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.announcements ?? [];

    if (q) {
        list = list.filter((a) => {
            const title = (a.title ?? "").toLowerCase();
            const body = (a.body ?? "").toLowerCase();
            const roles = (a.audience?.roles ?? []).join(" ").toLowerCase();
            return (
                title.includes(q) ||
                body.includes(q) ||
                roles.includes(q)
            );
        });
    }

    return list;
});
</script>

<template>
    <Head title="My Announcements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    My Announcements
                </h2>
                <Link
                    :href="route('teacher.announcements.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                >
                    Create Announcement
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'My Announcements' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Announcements you have created
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Share important information with your students and other roles.
                        </p>
                    </div>

                    <div class="mb-4 flex justify-end">
                        <div class="relative w-full sm:w-80">
                            <input
                                v-model="query"
                                type="text"
                                placeholder="Search title, body, roles…"
                                class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                            <button
                                v-if="query"
                                type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                @click="query = ''"
                            >
                                <span class="sr-only">Clear</span>
                                ✕
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-if="filtered.length === 0"
                            class="rounded-lg bg-slate-50 p-6 text-center text-sm text-slate-500"
                        >
                            You have not created any announcements yet.
                        </div>

                        <div
                            v-for="announcement in filtered"
                            :key="announcement.id"
                            class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            v-if="announcement.pinned"
                                            class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                        >
                                            📌 Pinned
                                        </span>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                            :class="{
                                                'bg-red-100 text-red-800': announcement.priority === 'urgent',
                                                'bg-amber-100 text-amber-800': announcement.priority === 'important',
                                                'bg-blue-100 text-blue-800': announcement.priority === 'info',
                                            }"
                                        >
                                            {{
                                                announcement.priority === "urgent"
                                                    ? "Urgent"
                                                    : announcement.priority === "important"
                                                    ? "Important"
                                                    : "Info"
                                            }}
                                        </span>
                                        <span
                                            v-if="announcement.require_ack"
                                            class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-800"
                                        >
                                            Ack required
                                        </span>
                                    </div>
                                    <h3 class="mt-1 text-base font-semibold text-slate-900">
                                        {{ announcement.title }}
                                    </h3>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ announcement.created_at }}
                                    </p>
                                    <div
                                        v-if="announcement.audience?.roles?.length"
                                        class="mt-2 flex flex-wrap gap-1"
                                    >
                                        <span
                                            v-for="r in announcement.audience.roles"
                                            :key="r"
                                            class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700"
                                        >
                                            {{ r === "all" ? "All roles" : r }}
                                        </span>
                                    </div>
                                    <p class="mt-3 text-sm text-slate-700 whitespace-pre-line">
                                        {{ announcement.body }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <Link
                                        :href="route('teacher.announcements.edit', announcement.id)"
                                        class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                    >
                                        Edit
                                    </Link>
                                    <Link
                                        :href="route('teacher.announcements.destroy', announcement.id)"
                                        method="delete"
                                        as="button"
                                        class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    >
                                        Delete
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

