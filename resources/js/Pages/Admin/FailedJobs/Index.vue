<script setup>
import { computed, ref, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    jobs: { type: Object, required: true },
    totalFailedCount: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },
});

const items = computed(() => [{ label: "Failed Jobs" }]);

const search = ref(props.filters?.q ?? "");
let searchTimer = null;

watch(
    search,
    (value) => {
        if (searchTimer) {
            window.clearTimeout(searchTimer);
        }

        searchTimer = window.setTimeout(() => {
            router.get(
                route("admin.failed-jobs.index"),
                { q: value || undefined },
                { preserveState: true, replace: true, preserveScroll: true }
            );
        }, 250);
    },
    { flush: "post" }
);

const hasFailedJobs = computed(() => props.totalFailedCount > 0);

function retryAll() {
    if (!hasFailedJobs.value) return;
    if (!window.confirm("Retry all failed jobs now?")) return;

    router.post(route("admin.failed-jobs.retry-all"), {}, { preserveScroll: true });
}

function flushAll() {
    if (!hasFailedJobs.value) return;
    if (!window.confirm("Delete all failed jobs from the failure log?")) return;

    router.post(route("admin.failed-jobs.flush"), {}, { preserveScroll: true });
}

function retryJob(id) {
    if (!window.confirm(`Retry failed job #${id}?`)) return;
    router.post(route("admin.failed-jobs.retry", id), {}, { preserveScroll: true });
}

function deleteJob(id) {
    if (!window.confirm(`Delete failed job #${id} from the failure log?`)) return;
    router.delete(route("admin.failed-jobs.destroy", id), { preserveScroll: true });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Failed Jobs" />

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Failed Jobs
                </h2>
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700"
                >
                    <span
                        class="h-2 w-2 rounded-full"
                        :class="totalFailedCount > 0 ? 'bg-amber-500' : 'bg-emerald-500'"
                    ></span>
                    <span>{{ totalFailedCount }} total</span>
                </div>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="items" />
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-sm text-slate-700">
                                    Monitor queue failures and recover jobs without terminal commands.
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="!hasFailedJobs"
                                    @click="retryAll"
                                >
                                    Retry all
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="!hasFailedJobs"
                                    @click="flushAll"
                                >
                                    Clear all
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 w-full sm:max-w-sm">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input
                                    v-model="search"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-9 text-sm text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                                    placeholder="Search ID, queue, job, exception..."
                                />
                                <button
                                    v-if="search"
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-700"
                                    @click="search = ''"
                                    aria-label="Clear search"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Failed Job
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Queue
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Exception
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Failed At
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-for="job in jobs.data" :key="job.id" class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-900">
                                            #{{ job.id }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-600">
                                            {{ job.job_name || "Unknown job" }}
                                        </p>
                                        <p v-if="job.uuid" class="mt-1 text-[11px] text-slate-500">
                                            UUID: {{ job.uuid }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-slate-900">
                                            {{ job.queue || "default" }}
                                        </p>
                                        <p class="text-xs text-slate-600">
                                            {{ job.connection || "unknown connection" }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="max-w-xl text-sm text-slate-700">
                                            {{ job.exception_preview || "No exception preview available." }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ new Date(job.failed_at).toLocaleString() }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100"
                                                @click="retryJob(job.id)"
                                            >
                                                Retry
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100"
                                                @click="deleteJob(job.id)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="jobs.data.length === 0">
                                    <td class="px-6 py-6 text-center text-sm text-slate-600" colspan="5">
                                        <span v-if="search">No failed jobs match "{{ search }}".</span>
                                        <span v-else>No failed jobs found.</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 border-t border-slate-200 px-6 py-4">
                        <Pagination :links="jobs.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

