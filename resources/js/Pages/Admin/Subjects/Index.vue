<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    subjects: {
        type: Array,
        required: true,
    },
});

const query = ref("");
const courseFilter = ref("all");
const teacherFilter = ref("all"); // teacher name contains

const courses = computed(() => {
    const set = new Set(
        (props.subjects ?? []).map((s) => s.course_code).filter(Boolean)
    );
    return Array.from(set).sort();
});

const stats = computed(() => {
    const list = props.subjects ?? [];
    const unassigned = list.filter((s) => !s.teachers?.length).length;
    return {
        total: list.length,
        courses: new Set(list.map((s) => s.course_code).filter(Boolean)).size,
        unassigned,
    };
});

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    const tf = teacherFilter.value.trim().toLowerCase();
    let list = props.subjects ?? [];

    if (courseFilter.value !== "all") {
        list = list.filter((s) => s.course_code === courseFilter.value);
    }

    if (tf && tf !== "all") {
        list = list.filter((s) => {
            const names = (s.teachers ?? []).map((t) => t.name).join(" ");
            return names.toLowerCase().includes(tf);
        });
    }

    if (q) {
        list = list.filter((s) => {
            const code = (s.subject_code ?? "").toLowerCase();
            const title = (s.title ?? "").toLowerCase();
            const course = `${s.course_code ?? ""} ${s.course_title ?? ""}`.toLowerCase();
            const teachers = (s.teachers ?? [])
                .map((t) => t.name)
                .join(" ")
                .toLowerCase();
            return (
                code.includes(q) ||
                title.includes(q) ||
                course.includes(q) ||
                teachers.includes(q)
            );
        });
    }

    return list;
});

const deleteSubject = (subjectId) => {
    if (
        !confirm(
            "Are you sure you want to delete this subject? This action cannot be undone."
        )
    ) {
        return;
    }

    router.delete(route("admin.subjects.destroy", subjectId), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Subject Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Subject Management
                </h2>
                <Link
                    :href="route('admin.subjects.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                >
                    Create Subject
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Subject Management' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Summary stats -->
                <div class="mb-6 grid gap-4 md:grid-cols-3">
                    <div class="portal-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Subjects
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ stats.total }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                            Courses covered
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ stats.courses }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-amber-50">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                            Not assigned
                        </p>
                        <p class="mt-2 text-2xl font-bold text-amber-900">
                            {{ stats.unassigned }}
                        </p>
                    </div>
                </div>

                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            All Subjects
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Manage subjects for courses
                        </p>
                    </div>

                    <!-- Filters -->
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <select
                                v-model="courseFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-56"
                            >
                                <option value="all">All courses</option>
                                <option v-for="c in courses" :key="c" :value="c">
                                    {{ c }}
                                </option>
                            </select>

                            <div class="relative w-full sm:w-80">
                                <input
                                    v-model="query"
                                    type="text"
                                    placeholder="Search code, title, course, teacher…"
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

                        <div class="text-xs text-slate-500">
                            Showing <span class="font-semibold text-slate-700">{{ filtered.length }}</span>
                            of <span class="font-semibold text-slate-700">{{ subjects.length }}</span>
                        </div>
                    </div>

                    <!-- Subjects Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Photo
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Subject Code
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Title
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Course
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Credits
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Assigned Teacher(s)
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr
                                    v-if="filtered.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="7"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{
                                            subjects.length === 0
                                                ? "No subjects found. Create your first subject to get started."
                                                : "No subjects match your filters."
                                        }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="subject in filtered"
                                    :key="subject.id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                >
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <div class="flex items-center">
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
                                        </div>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900"
                                    >
                                        {{ subject.subject_code }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-700"
                                    >
                                        {{ subject.title }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-600"
                                    >
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{
                                                subject.course_code
                                            }}</span>
                                            <span
                                                class="text-xs text-slate-500"
                                                >{{
                                                    subject.course_title
                                                }}</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ subject.credits || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-600"
                                    >
                                        <span
                                            v-if="
                                                subject.teachers &&
                                                subject.teachers.length > 0
                                            "
                                        >
                                            {{
                                                subject.teachers
                                                    .map((t) => t.name)
                                                    .join(", ")
                                            }}
                                        </span>
                                        <span v-else class="text-slate-400">
                                            Not Assigned
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.subjects.assign-teachers',
                                                        subject.id
                                                    )
                                                "
                                                class="rounded-md bg-portal-gold px-3 py-1.5 text-xs font-medium text-white hover:bg-portal-gold-dark focus:outline-none focus:ring-2 focus:ring-portal-gold focus:ring-offset-2"
                                            >
                                                Assign Teachers
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.subjects.edit',
                                                        subject.id
                                                    )
                                                "
                                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="
                                                    deleteSubject(subject.id)
                                                "
                                                class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                            >
                                                Delete
                                            </button>
                                        </div>
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
