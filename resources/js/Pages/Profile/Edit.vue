<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    photo_url: {
        type: String,
        default: null,
    },
});

const page = usePage();

const roleLabel = computed(() => {
    const role = page.props.auth?.user?.role;
    if (!role) return "User";
    if (role === "student") return "Student";
    if (role === "teacher") return "Teacher";
    if (role === "staff") return "Staff";
    if (role === "admin") return "Administrator";
    return role.charAt(0).toUpperCase() + role.slice(1);
});

const roleDescription = computed(() => {
    const role = page.props.auth?.user?.role;
    if (role === "student") {
        return "Access to student portal features like courses, grades, timetable, and fees.";
    }
    if (role === "teacher") {
        return "Access to teaching tools such as timetables, attendance, and grading.";
    }
    if (role === "staff") {
        return "Access to administrative tools for managing courses, users, and academic data.";
    }
    if (role === "admin") {
        return "Full administrative access to platform management, users, and academic operations.";
    }
    return "Standard user profile.";
});

const accessTitle = computed(() => {
    const role = page.props.auth?.user?.role;
    if (role === "student") return "Student access";
    if (role === "teacher") return "Teaching access";
    if (role === "staff") return "Staff access";
    if (role === "admin") return "Administrator access";
    return "Portal access";
});

const formattedCreatedAt = computed(() => {
    const value = page.props.auth?.user?.created_at;
    if (!value) return null;
    const d = new Date(value);
    return d.toLocaleDateString(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
});

const formattedLastLogin = computed(() => {
    const value = page.props.auth?.user?.last_login_at;
    if (!value) return null;
    const d = new Date(value);
    return d.toLocaleString(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

const formattedUpdatedAt = computed(() => {
    const value = page.props.auth?.user?.updated_at;
    if (!value) return null;
    const d = new Date(value);
    return d.toLocaleString(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Profile' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Profile overview header -->
                <div
                    class="portal-card flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="h-16 w-16 overflow-hidden rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center"
                        >
                            <img
                                v-if="photo_url"
                                :src="photo_url"
                                :alt="`Photo for ${$page.props.auth.user.name}`"
                                class="h-full w-full object-cover"
                            />
                            <span
                                v-else
                                class="text-lg font-semibold text-slate-500"
                            >
                                {{
                                    $page.props.auth.user.name
                                        .charAt(0)
                                        .toUpperCase()
                                }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                {{ $page.props.auth.user.name }}
                            </h3>
                            <p class="text-sm text-slate-600">
                                {{ $page.props.auth.user.email }}
                            </p>
                            <p
                                class="mt-1 inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700"
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                ></span>
                                {{ roleLabel }}
                            </p>
                        </div>
                    </div>
                    <div class="text-xs text-slate-500 sm:text-right space-y-1">
                        <div>
                            <p class="font-semibold text-slate-600">
                                {{ accessTitle }}
                            </p>
                            <p class="mt-1">
                                {{ roleDescription }}
                            </p>
                        </div>
                        <div
                            class="mt-5 border-t border-slate-200 pt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-3"
                        >
                            <div v-if="formattedCreatedAt">
                                <p class="font-semibold text-slate-500">
                                    Account created
                                </p>
                                <p class="mt-0.5 text-slate-800">
                                    {{ formattedCreatedAt }}
                                </p>
                            </div>
                            <div v-if="formattedLastLogin">
                                <p class="font-semibold text-slate-500">
                                    Last login
                                </p>
                                <p class="mt-0.5 text-slate-800">
                                    {{ formattedLastLogin }}
                                </p>
                            </div>
                            <div v-if="formattedUpdatedAt">
                                <p class="font-semibold text-slate-500">
                                    Profile updated
                                </p>
                                <p class="mt-0.5 text-slate-800">
                                    {{ formattedUpdatedAt }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick links row -->
                <div
                    class="portal-card flex flex-wrap items-center gap-2 border border-slate-200 bg-slate-50/80 p-4 text-xs text-slate-700"
                >
                    <p class="mr-2 font-semibold uppercase tracking-wide">
                        Quick links
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <!-- Student shortcuts -->
                        <Link
                            v-if="$page.props.auth?.user?.role === 'student'"
                            :href="route('my-courses.index')"
                            class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 font-medium ring-1 ring-slate-200 hover:bg-slate-50"
                        >
                            <span>My Courses</span>
                        </Link>
                        <Link
                            v-if="$page.props.auth?.user?.role === 'student'"
                            :href="route('student.grades.index')"
                            class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 font-medium ring-1 ring-slate-200 hover:bg-slate-50"
                        >
                            <span>My Grades</span>
                        </Link>
                        <Link
                            v-if="$page.props.auth?.user?.role === 'student'"
                            :href="route('student.timetable.index')"
                            class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 font-medium ring-1 ring-slate-200 hover:bg-slate-50"
                        >
                            <span>My Timetable</span>
                        </Link>

                        <!-- Teacher shortcuts -->
                        <Link
                            v-if="$page.props.auth?.user?.role === 'teacher'"
                            :href="route('teacher.courses.index')"
                            class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 font-medium ring-1 ring-slate-200 hover:bg-slate-50"
                        >
                            <span>Teaching Subjects</span>
                        </Link>
                        <Link
                            v-if="$page.props.auth?.user?.role === 'teacher'"
                            :href="route('teacher.timetable.index')"
                            class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 font-medium ring-1 ring-slate-200 hover:bg-slate-50"
                        >
                            <span>My Timetable</span>
                        </Link>
                        <Link
                            v-if="$page.props.auth?.user?.role === 'teacher'"
                            :href="route('teacher.grades.index')"
                            class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 font-medium ring-1 ring-slate-200 hover:bg-slate-50"
                        >
                            <span>Grades</span>
                        </Link>

                        <!-- Staff/admin shortcuts -->
                        <Link
                            v-if="$page.props.auth?.user?.role === 'staff'"
                            :href="route('admin.courses.index')"
                            class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 font-medium ring-1 ring-slate-200 hover:bg-slate-50"
                        >
                            <span>Manage Courses</span>
                        </Link>
                        <Link
                            v-if="$page.props.auth?.user?.role === 'staff'"
                            :href="route('admin.subjects.index')"
                            class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 font-medium ring-1 ring-slate-200 hover:bg-slate-50"
                        >
                            <span>Manage Subjects</span>
                        </Link>
                        <Link
                            v-if="$page.props.auth?.user?.role === 'staff'"
                            :href="route('students.index')"
                            class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 font-medium ring-1 ring-slate-200 hover:bg-slate-50"
                        >
                            <span>Student Records</span>
                        </Link>
                    </div>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="props.mustVerifyEmail"
                        :status="props.status"
                        :photo-url="props.photo_url"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
