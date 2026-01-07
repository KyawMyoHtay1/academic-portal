<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    announcements: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Announcements" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Announcements
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="space-y-4">
                    <div
                        v-if="announcements.length === 0"
                        class="portal-card p-6 text-center text-sm text-slate-500"
                    >
                        No announcements yet.
                    </div>

                    <div
                        v-for="announcement in announcements"
                        :key="announcement.id"
                        class="portal-card p-6"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">
                                    {{ announcement.title }}
                                </h3>
                                <p class="mt-1 text-xs text-slate-500">
                                    By {{ announcement.author }} · {{ announcement.created_at }}
                                </p>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-slate-700 whitespace-pre-line">
                            {{ announcement.body }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

