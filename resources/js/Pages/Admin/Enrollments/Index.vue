<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";

defineProps({
    enrollments: {
        type: Array,
        required: true,
    },
});

const approveEnrollment = (enrollmentId) => {
    if (
        !confirm(
            "Are you sure you want to approve this enrollment?"
        )
    ) {
        return;
    }

    router.post(route("admin.enrollments.approve", enrollmentId), {}, {
        preserveScroll: true,
    });
};

const rejectEnrollment = (enrollmentId) => {
    if (
        !confirm(
            "Are you sure you want to reject this enrollment? The student can reapply later."
        )
    ) {
        return;
    }

    router.post(route("admin.enrollments.reject", enrollmentId), {}, {
        preserveScroll: true,
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-GB', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Enrollment Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Enrollment Management
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Pending Enrollments
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Review and approve or reject student course enrollment requests
                        </p>
                    </div>

                    <!-- Enrollments Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Student
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
                                        Programme
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                    >
                                        Requested On
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
                                    v-if="enrollments.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="5"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No pending enrollments. All enrollment requests have been processed.
                                    </td>
                                </tr>
                                <tr
                                    v-for="enrollment in enrollments"
                                    :key="enrollment.enrollment_id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                >
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-slate-900">
                                            {{ enrollment.student_name }}
                                        </div>
                                        <div class="text-sm text-slate-500">
                                            {{ enrollment.student_no }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            {{ enrollment.student_email }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-slate-900">
                                            {{ enrollment.course_code }}
                                        </div>
                                        <div class="text-sm text-slate-600">
                                            {{ enrollment.course_title }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ enrollment.credits }} credits · {{ enrollment.semester }}
                                        </div>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ enrollment.programme || "-" }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ formatDate(enrollment.requested_at) }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                @click="approveEnrollment(enrollment.enrollment_id)"
                                                class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                            >
                                                Approve
                                            </button>
                                            <button
                                                @click="rejectEnrollment(enrollment.enrollment_id)"
                                                class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                            >
                                                Reject
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
