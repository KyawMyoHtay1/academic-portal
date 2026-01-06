<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const getRoleBadgeClass = (role) => {
    const classes = {
        student: "bg-blue-100 text-blue-800",
        teacher: "bg-purple-100 text-purple-800",
        staff: "bg-emerald-100 text-emerald-800",
    };
    return classes[role] || "bg-slate-100 text-slate-800";
};
</script>

<template>
    <Head title="User Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                User Management
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
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
                                <tr
                                    v-if="users.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="5"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No users found.
                                    </td>
                                </tr>
                                <tr
                                    v-for="user in users"
                                    :key="user.id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                >
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
                                            :class="getRoleBadgeClass(user.role)"
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
                                        <Link
                                            :href="route('admin.users.edit', user.id)"
                                            class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                        >
                                            Edit
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

