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

const stats = computed(() => ({
    total: props.subjects?.length ?? 0,
    courses: new Set(
        (props.subjects ?? [])
            .map((s) => s.course_code)
            .filter(Boolean)
    ).size,
}));

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
            const course = `${s.course_code ?? ""} ${
                s.course_title ?? ""
            }`.toLowerCase();
            return (
                code.includes(q) || title.includes(q) || course.includes(q)
            );
        });
    }

    return list;
});
</script>

<template>
    <Head title="Grades" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Grades
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Grades' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Summary stats -->
                <div class="mb-6 grid gap-4 md:grid-cols-2">
                    <div class="portal-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Subjects assigned
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ stats.total }}
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            Subjects you can grade
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-indigo-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">
                            Courses covered
                        </p>
                        <p class="mt-2 text-2xl font-bold text-indigo-900">
                            {{ stats.courses }}
                        </p>
                        <p class="mt-1 text-xs text-indigo-700">
                            Unique courses
                        </p>
                    </div>
                </div>

                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Select Subject
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Choose a subject to enter or update grades for
                            enrolled students.
                        </p>
                    </div>

                    <div
                        v-if="subjects.length === 0"
                        class="rounded-lg bg-slate-50 p-8 text-center"
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
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                            />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">
                            No subjects assigned
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            You need to be assigned to courses with subjects
                            before you can enter grades.
                        </p>
                    </div>

                    <div v-else class="space-y-4">
                        <!-- Filters -->
                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <select
                                    v-model="courseFilter"
                                    class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-56"
                                >
                                    <option value="all">All courses</option>
                                    <option
                                        v-for="c in courses"
                                        :key="c"
                                        :value="c"
                                    >
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
                                        <span class="sr-only">Clear</span>
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <p class="text-xs text-slate-500">
                                Showing
                                <span
                                    class="font-semibold text-slate-700"
                                    >{{ filteredSubjects.length }}</span
                                >
                                of
                                <span
                                    class="font-semibold text-slate-700"
                                    >{{ subjects.length }}</span
                                >
                                subjects.
                            </p>
                        </div>

                        <!-- Subjects grid -->
                        <div
                            v-if="filteredSubjects.length === 0"
                            class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-500"
                        >
                            No subjects match your current search or filters.
                        </div>

                        <div
                            v-else
                            class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                        >
                        <Link
                            v-for="subject in filteredSubjects"
                            :key="subject.id"
                            :href="route('teacher.grades.show', subject.id)"
                            class="group rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-portal-navy hover:shadow-md"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3 flex-1">
                                    <div
                                        class="h-10 w-10 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="subject.photo"
                                            :src="`/storage/${subject.photo}`"
                                            :alt="`Photo for ${subject.title}`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-xs font-semibold text-slate-500"
                                        >
                                            {{ subject.title[0] }}
                                        </span>
                                    </div>
                                    <div>
                                        <h3
                                            class="text-lg font-semibold text-slate-900 group-hover:text-portal-navy"
                                        >
                                            {{ subject.subject_code }}
                                        </h3>
                                        <p class="mt-1 text-sm text-slate-600">
                                            {{ subject.title }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ subject.course_code }} -
                                            {{ subject.course_title }}
                                        </p>
                                    </div>
                                </div>
                                <svg
                                    class="h-5 w-5 text-slate-400 group-hover:text-portal-navy"
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
                            </div>
                        </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
