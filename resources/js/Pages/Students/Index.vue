<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, onBeforeUnmount, ref, watch } from "vue";
import debounce from "lodash/debounce";

const props = defineProps({
    students: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    filterOptions: {
        type: Object,
        default: () => ({
            programmes: [],
            intakeYears: [],
            statuses: [],
        }),
    },
});

const query = ref(props.filters.search || "");
const programmeFilter = ref(props.filters.programme || "all");
const intakeYearFilter = ref(props.filters.intake_year || "all");
const statusFilter = ref(props.filters.status || "all");
const sortBy = ref(props.filters.sort_by || "student_no");
const sortDir = ref(props.filters.sort_dir || "asc");
const selectedIds = ref([]);

const rows = computed(() => props.students?.data ?? []);

const programmes = computed(() => props.filterOptions?.programmes ?? []);
const intakeYears = computed(() => props.filterOptions?.intakeYears ?? []);
const statuses = computed(() => props.filterOptions?.statuses ?? []);

const stats = computed(() => {
    const list = rows.value;
    const totalFromQuery = Number(props.students?.total ?? list.length);
    const programmeCount = programmes.value.length;
    const intakeCount = intakeYears.value.length;

    return {
        total: totalFromQuery,
        onPage: list.length,
        programmeCount,
        intakeCount,
    };
});

const hasActiveFilters = computed(
    () =>
        query.value.trim() !== "" ||
        programmeFilter.value !== "all" ||
        intakeYearFilter.value !== "all" ||
        statusFilter.value !== "all"
);

const activeFilterChips = computed(() => {
    const chips = [];
    if (programmeFilter.value !== "all") {
        chips.push({
            key: "programme",
            label: `Programme: ${programmeFilter.value}`,
        });
    }
    if (intakeYearFilter.value !== "all") {
        chips.push({
            key: "intake_year",
            label: `Intake: ${intakeYearFilter.value}`,
        });
    }
    if (statusFilter.value !== "all") {
        chips.push({
            key: "status",
            label: `Status: ${statusFilter.value}`,
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

const applyFilters = debounce(() => {
    router.get(
        route("students.index"),
        {
            search: query.value || null,
            programme: programmeFilter.value === "all" ? null : programmeFilter.value,
            intake_year: intakeYearFilter.value === "all" ? null : intakeYearFilter.value,
            status: statusFilter.value === "all" ? null : statusFilter.value,
            sort_by: sortBy.value,
            sort_dir: sortDir.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}, 300);

watch(
    () => [
        query.value,
        programmeFilter.value,
        intakeYearFilter.value,
        statusFilter.value,
        sortBy.value,
        sortDir.value,
    ],
    () => {
        applyFilters();
    }
);

watch(
    () => props.students?.data,
    () => {
        selectedIds.value = [];
    }
);

onBeforeUnmount(() => {
    applyFilters.cancel();
});

const deleteStudent = (id) => {
    if (
        !confirm(
            "Are you sure you want to delete this student record? This action cannot be undone."
        )
    ) {
        return;
    }

    router.delete(route("students.destroy", id), {
        preserveScroll: true,
    });
};

const toggleSort = (column) => {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === "asc" ? "desc" : "asc";
        return;
    }
    sortBy.value = column;
    sortDir.value = "asc";
};

const sortLabel = (column) => {
    if (sortBy.value !== column) return "sort";
    return sortDir.value === "asc" ? "asc" : "desc";
};

const clearFilters = () => {
    query.value = "";
    programmeFilter.value = "all";
    intakeYearFilter.value = "all";
    statusFilter.value = "all";
};

const removeFilterChip = (key) => {
    if (key === "programme") {
        programmeFilter.value = "all";
        return;
    }
    if (key === "intake_year") {
        intakeYearFilter.value = "all";
        return;
    }
    if (key === "status") {
        statusFilter.value = "all";
        return;
    }
    if (key === "search") {
        query.value = "";
    }
};

const allRowsSelected = computed(
    () => rows.value.length > 0 && rows.value.every((student) => selectedIds.value.includes(student.id))
);

const toggleSelectAll = () => {
    if (allRowsSelected.value) {
        selectedIds.value = [];
        return;
    }
    selectedIds.value = rows.value.map((student) => student.id);
};

const toggleRowSelection = (id) => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter((selectedId) => selectedId !== id);
        return;
    }
    selectedIds.value = [...selectedIds.value, id];
};

const bulkDeleteStudents = () => {
    if (selectedIds.value.length === 0) return;
    if (
        !confirm(
            `Delete ${selectedIds.value.length} selected student record(s)? This action cannot be undone.`
        )
    ) {
        return;
    }

    router.delete(route("students.bulk-destroy"), {
        data: { ids: selectedIds.value },
        preserveScroll: true,
        onSuccess: () => {
            selectedIds.value = [];
        },
    });
};
</script>

<script>
import Pagination from "@/Components/Pagination.vue";

export default {
    components: {
        Pagination,
    },
};
</script>

<template>
    <Head title="Students" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    Students
                </h2>
                <Link
                    :href="route('students.create')"
                    class="rounded-full bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-portal-navy/60 hover:bg-portal-navy/90"
                >
                    Add student
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Student Management' }]" />
            </div>
        </template>

        <div class="portal-card">
            <div class="border-b border-slate-200 px-6 py-4">
                <p class="text-sm text-slate-600">
                    Manage student academic records created for your University
                    Academic Portal project.
                </p>
            </div>

            <!-- Summary + filters -->
            <div class="px-4 pt-4 sm:px-6">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="portal-card p-5">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Students on this page
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ stats.total }}
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            {{ stats.onPage }} shown on this page
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-indigo-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-indigo-700"
                        >
                            Programmes
                        </p>
                        <p class="mt-2 text-2xl font-bold text-indigo-900">
                            {{ stats.programmeCount }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                        >
                            Intake years
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ stats.intakeCount }}
                        </p>
                    </div>
                </div>

                <div
                    class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <select
                            v-model="programmeFilter"
                            class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-64"
                        >
                            <option value="all">All programmes</option>
                            <option
                                v-for="p in programmes"
                                :key="p"
                                :value="p"
                            >
                                {{ p }}
                            </option>
                        </select>

                        <select
                            v-model="intakeYearFilter"
                            class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-40"
                        >
                            <option value="all">All intakes</option>
                            <option
                                v-for="y in intakeYears"
                                :key="y"
                                :value="y"
                            >
                                {{ y }}
                            </option>
                        </select>

                        <select
                            v-model="statusFilter"
                            class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:w-48"
                        >
                            <option value="all">All statuses</option>
                            <option
                                v-for="status in statuses"
                                :key="status"
                                :value="status"
                            >
                                {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                            </option>
                        </select>

                        <div class="relative w-full sm:w-72">
                            <input
                                v-model="query"
                                type="text"
                                placeholder="Search student no, name, email..."
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
                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        @click="clearFilters"
                    >
                        Clear all filters
                    </button>
                </div>

                <div
                    v-if="activeFilterChips.length > 0"
                    class="mt-3 flex flex-wrap items-center gap-2"
                >
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
                    v-if="selectedIds.length > 0"
                    class="mt-3 flex items-center justify-between rounded-lg border border-red-200 bg-red-50 px-3 py-2"
                >
                    <p class="text-xs font-semibold text-red-700">
                        {{ selectedIds.length }} selected
                    </p>
                    <button
                        type="button"
                        class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700"
                        @click="bulkDeleteStudents"
                    >
                        Bulk delete
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto px-4 py-4 sm:px-6">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                <input
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    :checked="allRowsSelected"
                                    :disabled="rows.length === 0"
                                    @change="toggleSelectAll"
                                />
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Photo
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 hover:text-slate-700"
                                    @click="toggleSort('student_no')"
                                >
                                    Student No
                                    <span class="text-[10px] text-slate-400">{{ sortLabel("student_no") }}</span>
                                </button>
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 hover:text-slate-700"
                                    @click="toggleSort('full_name')"
                                >
                                    Name
                                    <span class="text-[10px] text-slate-400">{{ sortLabel("full_name") }}</span>
                                </button>
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Email
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 hover:text-slate-700"
                                    @click="toggleSort('programme')"
                                >
                                    Programme
                                    <span class="text-[10px] text-slate-400">{{ sortLabel("programme") }}</span>
                                </button>
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 hover:text-slate-700"
                                    @click="toggleSort('intake_year')"
                                >
                                    Intake Year
                                    <span class="text-[10px] text-slate-400">{{ sortLabel("intake_year") }}</span>
                                </button>
                            </th>
                        <th
                            scope="col"
                            class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 hover:text-slate-700"
                                @click="toggleSort('status')"
                            >
                                Status
                                <span class="text-[10px] text-slate-400">{{ sortLabel("status") }}</span>
                            </button>
                        </th>
                            <th
                                scope="col"
                                class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="student in rows" :key="student.id">
                            <td class="px-3 py-2">
                                <input
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-portal-navy focus:ring-portal-navy"
                                    :checked="selectedIds.includes(student.id)"
                                    @change="toggleRowSelection(student.id)"
                                />
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="student.photo"
                                            :src="`/storage/${student.photo}`"
                                            :alt="`Photo for ${student.full_name}`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-xs font-semibold text-slate-500"
                                        >
                                            {{ student.full_name[0] }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2 font-medium text-slate-900">
                                {{ student.student_no }}
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ student.full_name }}
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ student.email }}
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ student.programme }}
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ student.intake_year }}
                            </td>
                        <td class="px-3 py-2 text-slate-700">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                :class="{
                                    'bg-emerald-100 text-emerald-800':
                                        student.status === 'active',
                                    'bg-amber-100 text-amber-800':
                                        student.status === 'suspended',
                                    'bg-indigo-100 text-indigo-800':
                                        student.status === 'graduated',
                                    'bg-slate-100 text-slate-700': !student.status,
                                }"
                            >
                                {{ student.status || 'N/A' }}
                            </span>
                        </td>
                            <td class="px-3 py-2 text-right text-slate-700">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Link
                                        :href="
                                            route('students.edit', student.id)
                                        "
                                        class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteStudent(student.id)"
                                        class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="rows.length === 0">
                            <td
                                colspan="9"
                                class="px-3 py-6 text-center text-sm text-slate-500"
                            >
                                {{
                                    "No students found. Try adjusting your search or filters."
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <Pagination :links="students.links" />
        </div>
    </AuthenticatedLayout>
</template>
