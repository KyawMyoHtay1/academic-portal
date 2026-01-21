<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    subjects: {
        type: Array,
        required: true,
    },
});

const query = ref("");

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return props.subjects;
    return (props.subjects || []).filter((s) => {
        const hay = `${s.subject_code} ${s.title} ${s.course_code ?? ""} ${s.course_title ?? ""}`.toLowerCase();
        return hay.includes(q);
    });
});
</script>

<template>
    <Head title="Grade Reviews" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Grade Review & Approval
                </h2>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Admin' }, { label: 'Grade Reviews' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Pending submissions
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Review and approve or reject teacher-submitted grades.
                            </p>
                        </div>
                        <div class="w-full sm:w-80">
                            <label class="block text-xs font-medium text-slate-600">Search</label>
                            <input
                                v-model="query"
                                type="search"
                                placeholder="Search subject / course…"
                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            />
                        </div>
                    </div>

                    <div v-if="filtered.length === 0" class="rounded-md border border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-600">
                        No pending grade submissions found.
                    </div>

                    <div v-else class="overflow-hidden rounded-md border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Subject
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Course
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Pending
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-for="s in filtered" :key="s.id" class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm text-slate-900">
                                        <div class="font-medium">{{ s.subject_code }}</div>
                                        <div class="text-xs text-slate-500">{{ s.title }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        <div class="font-medium">{{ s.course_code }}</div>
                                        <div class="text-xs text-slate-500">{{ s.course_title }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                            {{ s.pending_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <Link
                                            :href="route('admin.grades.show', s.id)"
                                            class="inline-flex items-center rounded-md bg-portal-navy px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-portal-navy-dark"
                                        >
                                            Review
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

