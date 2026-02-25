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
    teachers: {
        type: Array,
        default: () => [],
    },
});

const query = ref("");
const courseFilter = ref("all");
const teacherFilter = ref("all");
const selectedSubjectIds = ref([]);
const bulkTeacherIds = ref([]);
const bulkAssignProcessing = ref(false);

const courses = computed(() => {
    const set = new Set(
        (props.subjects ?? []).map((subject) => subject.course_code).filter(Boolean)
    );

    return Array.from(set).sort((a, b) => a.localeCompare(b));
});

const stats = computed(() => {
    const list = props.subjects ?? [];
    const unassigned = list.filter((subject) => !(subject.teachers ?? []).length).length;

    return {
        total: list.length,
        courses: new Set(list.map((subject) => subject.course_code).filter(Boolean)).size,
        unassigned,
    };
});

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = [...(props.subjects ?? [])];

    if (courseFilter.value !== "all") {
        list = list.filter((subject) => subject.course_code === courseFilter.value);
    }

    if (teacherFilter.value !== "all") {
        list = list.filter((subject) =>
            (subject.teachers ?? []).some(
                (teacher) => String(teacher.id) === String(teacherFilter.value)
            )
        );
    }

    if (q) {
        list = list.filter((subject) => {
            const code = (subject.subject_code ?? "").toLowerCase();
            const title = (subject.title ?? "").toLowerCase();
            const course = `${subject.course_code ?? ""} ${subject.course_title ?? ""}`.toLowerCase();
            const teachers = (subject.teachers ?? [])
                .map((teacher) => teacher.name)
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

const hasActiveFilters = computed(
    () =>
        query.value.trim() !== "" ||
        courseFilter.value !== "all" ||
        teacherFilter.value !== "all"
);

const activeFilterChips = computed(() => {
    const chips = [];

    if (courseFilter.value !== "all") {
        chips.push({
            key: "course",
            label: `Course: ${courseFilter.value}`,
        });
    }

    if (teacherFilter.value !== "all") {
        const teacher = (props.teachers ?? []).find(
            (row) => String(row.id) === String(teacherFilter.value)
        );
        chips.push({
            key: "teacher",
            label: `Teacher: ${teacher?.name ?? teacherFilter.value}`,
        });
    }

    if (query.value.trim() !== "") {
        chips.push({
            key: "search",
            label: `Search: ${query.value.trim()}`,
        });
    }

    return chips;
});

const visibleSubjectIds = computed(() => filtered.value.map((subject) => subject.id));

const allVisibleSelected = computed(
    () =>
        visibleSubjectIds.value.length > 0 &&
        visibleSubjectIds.value.every((id) => selectedSubjectIds.value.includes(id))
);

const selectedCount = computed(() => selectedSubjectIds.value.length);

const toggleSelectAllVisible = () => {
    if (allVisibleSelected.value) {
        const visibleIdSet = new Set(visibleSubjectIds.value);
        selectedSubjectIds.value = selectedSubjectIds.value.filter(
            (id) => !visibleIdSet.has(id)
        );
        return;
    }

    const merged = new Set(selectedSubjectIds.value);
    visibleSubjectIds.value.forEach((id) => merged.add(id));
    selectedSubjectIds.value = Array.from(merged);
};

const toggleSubjectSelection = (subjectId) => {
    if (selectedSubjectIds.value.includes(subjectId)) {
        selectedSubjectIds.value = selectedSubjectIds.value.filter((id) => id !== subjectId);
        return;
    }

    selectedSubjectIds.value = [...selectedSubjectIds.value, subjectId];
};

const clearFilters = () => {
    query.value = "";
    courseFilter.value = "all";
    teacherFilter.value = "all";
};

const removeFilterChip = (key) => {
    if (key === "course") {
        courseFilter.value = "all";
        return;
    }

    if (key === "teacher") {
        teacherFilter.value = "all";
        return;
    }

    if (key === "search") {
        query.value = "";
    }
};

const clearSelection = () => {
    selectedSubjectIds.value = [];
};

const assignTeachersInBulk = () => {
    if (selectedSubjectIds.value.length === 0) {
        return;
    }

    if (bulkTeacherIds.value.length === 0) {
        alert("Please select at least one teacher to assign.");
        return;
    }

    if (
        !confirm(
            `Assign ${bulkTeacherIds.value.length} teacher(s) to ${selectedSubjectIds.value.length} selected subject(s)? Existing assignments will be kept.`
        )
    ) {
        return;
    }

    bulkAssignProcessing.value = true;

    router.post(
        route("admin.subjects.assign-teachers.bulk"),
        {
            subject_ids: selectedSubjectIds.value,
            teacher_ids: bulkTeacherIds.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                selectedSubjectIds.value = [];
                bulkTeacherIds.value = [];
            },
            onFinish: () => {
                bulkAssignProcessing.value = false;
            },
        }
    );
};

const deleteSubject = (subjectId) => {
    if (!confirm("Are you sure you want to delete this subject? This action cannot be undone.")) {
        return;
    }

    router.delete(route("admin.subjects.destroy", subjectId), {
        preserveScroll: true,
    });
};

const formatUpdatedAt = (value) => {
    if (!value) return "-";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return String(value);

    return new Intl.DateTimeFormat("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    }).format(date);
};

const formatThreshold = (value) =>
    value === null || value === undefined ? "Global default" : `${value}%`;
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
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="portal-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Subjects
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ stats.total }}
                        </p>
                    </div>
                    <div class="portal-card bg-emerald-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                            Courses covered
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ stats.courses }}
                        </p>
                    </div>
                    <div class="portal-card bg-amber-50 p-5">
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
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            All Subjects
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Manage subjects, teachers, and bulk assignment workflow.
                        </p>
                    </div>

                    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="grid w-full gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            <select
                                v-model="courseFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All courses</option>
                                <option v-for="course in courses" :key="course" :value="course">
                                    {{ course }}
                                </option>
                            </select>

                            <select
                                v-model="teacherFilter"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                            >
                                <option value="all">All teachers</option>
                                <option
                                    v-for="teacher in teachers"
                                    :key="teacher.id"
                                    :value="teacher.id"
                                >
                                    {{ teacher.name }}
                                </option>
                            </select>

                            <div class="relative">
                                <input
                                    v-model="query"
                                    type="text"
                                    placeholder="Search code, title, course, teacher..."
                                    class="block w-full rounded-md border-slate-300 pr-9 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy"
                                />
                                <button
                                    v-if="query"
                                    type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 hover:bg-slate-100"
                                    @click="query = ''"
                                >
                                    <span class="sr-only">Clear</span>
                                    x
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                v-if="hasActiveFilters"
                                type="button"
                                class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                @click="clearFilters"
                            >
                                Clear all filters
                            </button>
                            <p class="text-xs text-slate-500">
                                Showing <span class="font-semibold text-slate-700">{{ filtered.length }}</span>
                                of <span class="font-semibold text-slate-700">{{ subjects.length }}</span>
                            </p>
                        </div>
                    </div>

                    <div v-if="activeFilterChips.length > 0" class="mb-4 flex flex-wrap items-center gap-2">
                        <span
                            v-for="chip in activeFilterChips"
                            :key="chip.key"
                            class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"
                        >
                            {{ chip.label }}
                            <button
                                type="button"
                                class="rounded px-1 text-slate-500 hover:bg-slate-200"
                                @click="removeFilterChip(chip.key)"
                            >
                                x
                            </button>
                        </span>
                    </div>

                    <div
                        v-if="selectedCount > 0"
                        class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4"
                    >
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                                    Bulk Assign Teachers
                                </p>
                                <p class="mt-1 text-sm text-amber-900">
                                    {{ selectedCount }} subject(s) selected.
                                </p>
                            </div>

                            <div class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto lg:items-center">
                                <select
                                    v-model="bulkTeacherIds"
                                    multiple
                                    class="min-h-24 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-72"
                                >
                                    <option
                                        v-for="teacher in teachers"
                                        :key="`bulk-teacher-${teacher.id}`"
                                        :value="teacher.id"
                                    >
                                        {{ teacher.name }} ({{ teacher.email }})
                                    </option>
                                </select>

                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="rounded-md bg-portal-gold px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-portal-gold-dark disabled:opacity-50"
                                        :disabled="bulkAssignProcessing || bulkTeacherIds.length === 0"
                                        @click="assignTeachersInBulk"
                                    >
                                        {{ bulkAssignProcessing ? "Assigning..." : "Assign selected teachers" }}
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-100"
                                        @click="clearSelection"
                                    >
                                        Clear selection
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="mt-2 text-[11px] text-amber-800">
                            This adds teachers to selected subjects and keeps existing assignments.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        <input
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                            :checked="allVisibleSelected"
                                            :disabled="visibleSubjectIds.length === 0"
                                            @change="toggleSelectAllVisible"
                                        />
                                    </th>
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
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Details
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
                                <tr v-if="filtered.length === 0" class="bg-white">
                                    <td colspan="9" class="px-4 py-8 text-center text-sm text-slate-500">
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
                                    class="bg-white transition-colors hover:bg-slate-50"
                                >
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <input
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                            :checked="selectedSubjectIds.includes(subject.id)"
                                            @change="toggleSubjectSelection(subject.id)"
                                        />
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-slate-100"
                                            >
                                                <img
                                                    v-if="subject.photo"
                                                    :src="`/storage/${subject.photo}`"
                                                    :alt="`Photo for ${subject.title}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span v-else class="text-xs font-semibold text-slate-500">
                                                    {{ subject.title?.[0] ?? "S" }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900">
                                        {{ subject.subject_code }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ subject.title }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ subject.course_code }}</span>
                                            <span class="text-xs text-slate-500">{{ subject.course_title }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">
                                        {{ subject.credits || "-" }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        <span v-if="subject.teachers && subject.teachers.length > 0">
                                            {{ subject.teachers.map((teacher) => teacher.name).join(", ") }}
                                        </span>
                                        <span v-else class="text-slate-400">
                                            Not Assigned
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-600">
                                        <div class="space-y-1">
                                            <p>
                                                Teachers:
                                                <span class="font-semibold text-slate-700">
                                                    {{ Number(subject.teacher_count ?? 0) }}
                                                </span>
                                            </p>
                                            <p>
                                                Assignments:
                                                <span class="font-semibold text-slate-700">
                                                    {{ Number(subject.published_assignments_count ?? 0) }}/{{ Number(subject.assignments_count ?? 0) }}
                                                </span>
                                                <span class="text-slate-500">published</span>
                                            </p>
                                            <p>
                                                Attendance threshold:
                                                <span class="font-semibold text-slate-700">
                                                    {{ formatThreshold(subject.attendance_threshold) }}
                                                </span>
                                            </p>
                                            <p>
                                                Updated:
                                                <span class="font-semibold text-slate-700">
                                                    {{ formatUpdatedAt(subject.updated_at) }}
                                                </span>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('admin.subjects.assign-teachers', subject.id)"
                                                class="rounded-md bg-portal-gold px-3 py-1.5 text-xs font-medium text-white hover:bg-portal-gold-dark focus:outline-none focus:ring-2 focus:ring-portal-gold focus:ring-offset-2"
                                            >
                                                Assign Teachers
                                            </Link>
                                            <Link
                                                :href="route('admin.subjects.edit', subject.id)"
                                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                @click="deleteSubject(subject.id)"
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
