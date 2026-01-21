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
const courseFilter = ref("all");

const courses = computed(() => {
    const set = new Set(
        (props.subjects ?? []).map((s) => s.course_code).filter(Boolean)
    );
    return Array.from(set).sort();
});

const filteredSubjects = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.subjects ?? [];

    if (courseFilter.value !== "all") {
        list = list.filter((s) => s.course_code === courseFilter.value);
    }

    if (q) {
        list = list.filter((s) => {
            const code = (s.subject_code ?? "").toLowerCase();
            const title = (s.title ?? "").toLowerCase();
            const course = `${s.course_code ?? ""} ${s.course_title ?? ""}`.toLowerCase();
            return code.includes(q) || title.includes(q) || course.includes(q);
        });
    }

    return list;
});
</script>

<template>
    <Head title="Assignments" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Assignments
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Assignments' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            My Teaching Subjects
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Manage assignments for your subjects
                        </p>
                    </div>

                    <div v-if="subjects.length === 0" class="rounded-lg bg-slate-50 p-8 text-center">
                        <p class="text-sm text-slate-500">No subjects assigned to you.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <select
                                    v-model="courseFilter"
                                    class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-56"
                                >
                                    <option value="all">All courses</option>
                                    <option v-for="c in courses" :key="c" :value="c">
                                        {{ c }}
                                    </option>
                                </select>
                                <div class="relative w-full sm:w-72">
                                    <input
                                        v-model="query"
                                        type="text"
                                        placeholder="Search subject or course…"
                                        class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                    />
                                    <button
                                        v-if="query"
                                        type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                        @click="query = ''"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="filteredSubjects.length === 0" class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-500">
                            No subjects match your search.
                        </div>

                        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <Link
                                v-for="subject in filteredSubjects"
                                :key="subject.id"
                                :href="route('teacher.assignments.show', subject.id)"
                                class="group rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-portal-navy hover:shadow-md"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="h-10 w-10 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center">
                                            <img
                                                v-if="subject.photo"
                                                :src="`/storage/${subject.photo}`"
                                                :alt="`Photo for ${subject.title}`"
                                                class="h-full w-full object-cover"
                                            />
                                            <span v-else class="text-xs font-semibold text-slate-500">
                                                {{ subject.title[0] }}
                                            </span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-slate-900 group-hover:text-portal-navy">
                                                {{ subject.subject_code }}
                                            </h3>
                                            <p class="mt-1 text-sm text-slate-600">{{ subject.title }}</p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ subject.course_code }} - {{ subject.course_title }}
                                            </p>
                                            <div v-if="subject.assignments && subject.assignments.length > 0" class="mt-2 flex flex-wrap gap-1">
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-800">
                                                    {{ subject.assignments.length }} assignment(s)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <svg class="h-5 w-5 text-slate-400 group-hover:text-portal-navy" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
