<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
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

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Announcements' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <!-- Header Banner -->
                <div
                    class="mb-6 overflow-hidden rounded-lg bg-gradient-to-r from-portal-navy to-portal-navy-dark p-8 text-white shadow-lg"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm"
                        >
                            <svg
                                class="h-8 w-8"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">
                                University Announcements
                            </h3>
                            <p class="mt-1 text-sm text-white/90">
                                Stay updated with the latest news and important
                                information
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div
                        v-if="announcements.length === 0"
                        class="portal-card p-12 text-center"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                            />
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-slate-900">
                            No announcements yet
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Check back later for updates
                        </p>
                    </div>

                    <div
                        v-for="announcement in announcements"
                        :key="announcement.id"
                        class="portal-card overflow-hidden p-6 transition-shadow hover:shadow-lg"
                    >
                        <div class="flex items-start gap-4">
                            <!-- Author Avatar -->
                            <div class="flex-shrink-0">
                                <div
                                    class="h-12 w-12 overflow-hidden rounded-full border-2 border-slate-200 bg-slate-100 flex items-center justify-center"
                                >
                                    <img
                                        v-if="announcement.author_photo"
                                        :src="`/storage/${announcement.author_photo}`"
                                        :alt="`Photo of ${announcement.author}`"
                                        class="h-full w-full object-cover"
                                    />
                                    <span
                                        v-else
                                        class="text-sm font-semibold text-slate-500"
                                    >
                                        {{
                                            announcement.author
                                                .charAt(0)
                                                .toUpperCase()
                                        }}
                                    </span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <div class="flex-1">
                                        <h3
                                            class="text-lg font-semibold text-slate-900"
                                        >
                                            {{ announcement.title }}
                                        </h3>
                                        <div
                                            class="mt-2 flex items-center gap-2 text-xs text-slate-500"
                                        >
                                            <span class="font-medium">{{
                                                announcement.author
                                            }}</span>
                                            <span>·</span>
                                            <span>{{
                                                announcement.created_at
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-portal-navy/10"
                                        >
                                            <svg
                                                class="h-4 w-4 text-portal-navy"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <p
                                    class="mt-4 text-sm leading-relaxed text-slate-700 whitespace-pre-line"
                                >
                                    {{ announcement.body }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
