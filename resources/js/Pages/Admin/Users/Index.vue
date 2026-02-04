<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const currentUserId = computed(() => page.props.auth?.user?.id ?? null);

const tabs = [
    { key: "all", label: "All" },
    { key: "student", label: "Students" },
    { key: "teacher", label: "Teachers" },
    { key: "staff", label: "Staff" },
];

const activeTab = ref(props.filters.role || "all");
const query = ref(props.filters.search || "");

const performSearch = debounce(() => {
    router.get(
        route("admin.users.index"),
        {
            search: query.value,
            role: activeTab.value === "all" ? null : activeTab.value,
        },
        { preserveState: true, preserveScroll: true, replace: true }
    );
}, 300);

watch(
    () => activeTab.value,
    (newValue) => {
        performSearch();
    }
);

watch(
    () => query.value,
    () => {
        performSearch();
    }
);

const getRoleBadgeClass = (role) => {
    const classes = {
        student: "bg-blue-100 text-blue-800",
        teacher: "bg-purple-100 text-purple-800",
        staff: "bg-emerald-100 text-emerald-800",
    };
    return classes[role] || "bg-slate-100 text-slate-800";
};

const deleteUser = (id) => {
    if (currentUserId.value && String(id) === String(currentUserId.value)) {
        alert("You cannot delete your own account.");
        return;
    }

    if (
        !confirm(
            "Are you sure you want to delete this user? This action cannot be undone."
        )
    ) {
        return;
    }

    router.delete(route("admin.users.destroy", id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="User Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    User Management
                </h2>
                <Link
                    :href="route('admin.users.create')"
                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                >
                    Add User
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'User Management' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Summary stats -->
                <div class="mb-6 grid gap-4 md:grid-cols-4">
                    <div class="portal-card p-5">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Total users
                        </p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ stats.total }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-blue-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-blue-700"
                        >
                            Students
                        </p>
                        <p class="mt-2 text-2xl font-bold text-blue-900">
                            {{ stats.students }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-purple-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-purple-700"
                        >
                            Teachers
                        </p>
                        <p class="mt-2 text-2xl font-bold text-purple-900">
                            {{ stats.teachers }}
                        </p>
                    </div>
                    <div class="portal-card p-5 bg-emerald-50">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                        >
                            Staff
                        </p>
                        <p class="mt-2 text-2xl font-bold text-emerald-900">
                            {{ stats.staff }}
                        </p>
                    </div>
                </div>

                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            All Users
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Manage user accounts and assign roles (student,
                            teacher, staff)
                        </p>
                    </div>

                    <!-- Search + role filters -->
                    <div
                        class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                v-for="t in tabs"
                                :key="t.key"
                                type="button"
                                class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-slate-200 transition"
                                :class="[
                                    activeTab === t.key
                                        ? 'bg-portal-navy text-white ring-portal-navy'
                                        : 'bg-white text-slate-700 hover:bg-slate-50',
                                ]"
                                @click="activeTab = t.key"
                            >
                                {{ t.label }}
                                <span
                                    v-if="t.key === 'all'"
                                    class="ml-2 inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold text-white"
                                >
                                    {{ stats.total }}
                                </span>
                                <span
                                    v-else-if="t.key === 'student'"
                                    class="ml-2 inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold text-white"
                                >
                                    {{ stats.students }}
                                </span>
                                <span
                                    v-else-if="t.key === 'teacher'"
                                    class="ml-2 inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold text-white"
                                >
                                    {{ stats.teachers }}
                                </span>
                                <span
                                    v-else-if="t.key === 'staff'"
                                    class="ml-2 inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold text-white"
                                >
                                    {{ stats.staff }}
                                </span>
                            </button>
                        </div>

                        <div class="relative w-full sm:w-96">
                            <input
                                v-model="query"
                                type="text"
                                placeholder="Search name, email, role…"
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

                    <!-- Users Table -->
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
                                        Name
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Email
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Role
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Created
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
                                <tr v-if="users.data.length === 0" class="bg-white">
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{
                                            users.data.length === 0
                                                ? "No users found."
                                                : "No users match your filters."
                                        }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="user in users.data"
                                    :key="user.id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                >
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 overflow-hidden rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center"
                                            >
                                                <img
                                                    v-if="user.photo"
                                                    :src="`/storage/${user.photo}`"
                                                    :alt="`Photo for ${user.name}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{ user.name[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900"
                                    >
                                        {{ user.name }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-slate-700"
                                    >
                                        {{ user.email }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm"
                                    >
                                        <span
                                            :class="
                                                getRoleBadgeClass(user.role)
                                            "
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-medium capitalize"
                                        >
                                            {{ user.role }}
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{
                                            new Date(
                                                user.created_at
                                            ).toLocaleDateString()
                                        }}
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
                                                        'admin.users.edit',
                                                        user.id
                                                    )
                                                "
                                                class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteUser(user.id)"
                                                class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                :disabled="
                                                    currentUserId &&
                                                    String(user.id) ===
                                                        String(currentUserId)
                                                "
                                                :class="{
                                                    'opacity-50 cursor-not-allowed':
                                                        currentUserId &&
                                                        String(user.id) ===
                                                            String(currentUserId),
                                                }"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 border-t border-slate-200 px-4 py-3 sm:px-6">
                        <Pagination :links="users.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
