<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    enrollments: {
        type: Array,
        required: true,
    },
    withdrawals: {
        type: Array,
        required: true,
    },
});

const activeTab = ref("enrollments"); // enrollments | withdrawals
const query = ref("");

const stats = computed(() => ({
    enrollments: props.enrollments?.length ?? 0,
    withdrawals: props.withdrawals?.length ?? 0,
}));

const filteredEnrollments = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.enrollments ?? [];
    if (!q) return list;
    return list.filter((e) => {
        const student = `${e.student_name ?? ""} ${e.student_no ?? ""} ${e.student_email ?? ""}`.toLowerCase();
        const course = `${e.course_code ?? ""} ${e.course_title ?? ""}`.toLowerCase();
        const programme = (e.programme ?? "").toLowerCase();
        return student.includes(q) || course.includes(q) || programme.includes(q);
    });
});

const filteredWithdrawals = computed(() => {
    const q = query.value.trim().toLowerCase();
    let list = props.withdrawals ?? [];
    if (!q) return list;
    return list.filter((e) => {
        const student = `${e.student_name ?? ""} ${e.student_no ?? ""} ${e.student_email ?? ""}`.toLowerCase();
        const course = `${e.course_code ?? ""} ${e.course_title ?? ""}`.toLowerCase();
        const programme = (e.programme ?? "").toLowerCase();
        return student.includes(q) || course.includes(q) || programme.includes(q);
    });
});

const approveEnrollment = (enrollmentId) => {
    if (!confirm("Are you sure you want to approve this enrollment?")) {
        return;
    }

    router.post(
        route("admin.enrollments.approve", enrollmentId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const rejectEnrollment = (enrollmentId) => {
    if (
        !confirm(
            "Are you sure you want to reject this enrollment? The student can reapply later."
        )
    ) {
        return;
    }

    router.post(
        route("admin.enrollments.reject", enrollmentId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const approveWithdrawal = (enrollmentId) => {
    if (
        !confirm(
            "Are you sure you want to approve this withdrawal? The student will be removed from the course."
        )
    ) {
        return;
    }

    router.post(
        route("admin.enrollments.approve-withdrawal", enrollmentId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const rejectWithdrawal = (enrollmentId) => {
    if (
        !confirm(
            "Are you sure you want to reject this withdrawal? The student will remain enrolled in the course."
        )
    ) {
        return;
    }

    router.post(
        route("admin.enrollments.reject-withdrawal", enrollmentId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-GB", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
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

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Enrollment Management' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Summary + controls -->
                <div class="portal-card p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Requests overview
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                Pending enrollment and withdrawal approvals
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-slate-200 transition"
                                :class="activeTab === 'enrollments' ? 'bg-portal-navy text-white ring-portal-navy' : 'bg-white text-slate-700 hover:bg-slate-50'"
                                @click="activeTab = 'enrollments'"
                            >
                                Enrollments
                                <span class="ml-2 inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold text-white">
                                    {{ stats.enrollments }}
                                </span>
                            </button>
                            <button
                                type="button"
                                class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-slate-200 transition"
                                :class="activeTab === 'withdrawals' ? 'bg-portal-navy text-white ring-portal-navy' : 'bg-white text-slate-700 hover:bg-slate-50'"
                                @click="activeTab = 'withdrawals'"
                            >
                                Withdrawals
                                <span class="ml-2 inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold text-white">
                                    {{ stats.withdrawals }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="relative w-full sm:w-96">
                            <input
                                v-model="query"
                                type="text"
                                placeholder="Search student, course, programme…"
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
                </div>

                <!-- Pending Enrollments Section -->
                <div v-if="activeTab === 'enrollments'" class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Pending Enrollment Requests
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Review and approve or reject student course
                            enrollment requests
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
                                    v-if="filteredEnrollments.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="5"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{
                                            enrollments.length === 0
                                                ? "No pending enrollment requests. All enrollment requests have been processed."
                                                : "No enrollment requests match your search."
                                        }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="enrollment in filteredEnrollments"
                                    :key="enrollment.enrollment_id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                >
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                            >
                                                <img
                                                    v-if="
                                                        enrollment.student_photo
                                                    "
                                                    :src="`/storage/${enrollment.student_photo}`"
                                                    :alt="`Photo for ${enrollment.student_name}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{
                                                        enrollment.student_name
                                                            .charAt(0)
                                                            .toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-medium text-slate-900"
                                                >
                                                    {{
                                                        enrollment.student_name
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm text-slate-500"
                                                >
                                                    {{ enrollment.student_no }}
                                                </div>
                                                <div
                                                    class="text-xs text-slate-400"
                                                >
                                                    {{
                                                        enrollment.student_email
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                            >
                                                <img
                                                    v-if="
                                                        enrollment.course_photo
                                                    "
                                                    :src="`/storage/${enrollment.course_photo}`"
                                                    :alt="`Photo for ${enrollment.course_title}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{
                                                        enrollment.course_title
                                                            .charAt(0)
                                                            .toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-medium text-slate-900"
                                                >
                                                    {{ enrollment.course_code }}
                                                </div>
                                                <div
                                                    class="text-sm text-slate-600"
                                                >
                                                    {{
                                                        enrollment.course_title
                                                    }}
                                                </div>
                                                <div
                                                    class="text-xs text-slate-500"
                                                >
                                                    {{
                                                        enrollment.credits
                                                    }}
                                                    credits ·
                                                    {{ enrollment.semester }}
                                                </div>
                                            </div>
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
                                        {{
                                            formatDate(enrollment.requested_at)
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <button
                                                @click="
                                                    approveEnrollment(
                                                        enrollment.enrollment_id
                                                    )
                                                "
                                                class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                            >
                                                Approve
                                            </button>
                                            <button
                                                @click="
                                                    rejectEnrollment(
                                                        enrollment.enrollment_id
                                                    )
                                                "
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

                <!-- Pending Withdrawals Section -->
                <div v-else class="portal-card overflow-hidden p-6">
                    <div class="mb-4">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Pending Withdrawal Requests
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Review and approve or reject student course
                            withdrawal requests
                        </p>
                    </div>

                    <!-- Withdrawals Table -->
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
                                    v-if="filteredWithdrawals.length === 0"
                                    class="bg-white"
                                >
                                    <td
                                        colspan="5"
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        {{
                                            withdrawals.length === 0
                                                ? "No pending withdrawal requests. All withdrawal requests have been processed."
                                                : "No withdrawal requests match your search."
                                        }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="withdrawal in filteredWithdrawals"
                                    :key="withdrawal.enrollment_id"
                                    class="bg-white hover:bg-slate-50 transition-colors"
                                >
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                            >
                                                <img
                                                    v-if="
                                                        withdrawal.student_photo
                                                    "
                                                    :src="`/storage/${withdrawal.student_photo}`"
                                                    :alt="`Photo for ${withdrawal.student_name}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{
                                                        withdrawal.student_name
                                                            .charAt(0)
                                                            .toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-medium text-slate-900"
                                                >
                                                    {{
                                                        withdrawal.student_name
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm text-slate-500"
                                                >
                                                    {{ withdrawal.student_no }}
                                                </div>
                                                <div
                                                    class="text-xs text-slate-400"
                                                >
                                                    {{
                                                        withdrawal.student_email
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                            >
                                                <img
                                                    v-if="
                                                        withdrawal.course_photo
                                                    "
                                                    :src="`/storage/${withdrawal.course_photo}`"
                                                    :alt="`Photo for ${withdrawal.course_title}`"
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-xs font-semibold text-slate-500"
                                                >
                                                    {{
                                                        withdrawal.course_title
                                                            .charAt(0)
                                                            .toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-medium text-slate-900"
                                                >
                                                    {{ withdrawal.course_code }}
                                                </div>
                                                <div
                                                    class="text-sm text-slate-600"
                                                >
                                                    {{
                                                        withdrawal.course_title
                                                    }}
                                                </div>
                                                <div
                                                    class="text-xs text-slate-500"
                                                >
                                                    {{
                                                        withdrawal.credits
                                                    }}
                                                    credits ·
                                                    {{ withdrawal.semester }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{ withdrawal.programme || "-" }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"
                                    >
                                        {{
                                            formatDate(withdrawal.requested_at)
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm"
                                    >
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <button
                                                @click="
                                                    approveWithdrawal(
                                                        withdrawal.enrollment_id
                                                    )
                                                "
                                                class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                            >
                                                Approve
                                            </button>
                                            <button
                                                @click="
                                                    rejectWithdrawal(
                                                        withdrawal.enrollment_id
                                                    )
                                                "
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
