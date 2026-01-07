<script setup>
import { computed, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { Link, usePage } from "@inertiajs/vue3";

const showingMobileSidebar = ref(false);
const page = usePage();

const navigation = computed(() => {
    const user = page.props.auth?.user;
    const isStaff = user?.role === "staff";
    const isTeacher = user?.role === "teacher";

    const items = [
        {
            name: "Dashboard",
            href: route("dashboard"),
            active: route().current("dashboard"),
        },
    ];

    // Student features (show for students only)
    if (user?.role === "student") {
        items.push(
            {
                name: "My Profile",
                href: route("student.profile.show"),
                active: route().current("student.profile.*"),
            },
            {
                name: "Courses",
                href: route("courses.index"),
                active:
                    route().current("courses.*") && !route().current("admin.*"),
            },
            {
                name: "My Courses",
                href: route("my-courses.index"),
                active: route().current("my-courses.*"),
            },
            {
                name: "Grades",
                href: route("student.grades.index"),
                active: route().current("student.grades.*"),
            }
        );
    }

    // Teacher features
    if (isTeacher) {
        items.push(
            {
                name: "My Teaching Courses",
                href: route("teacher.courses.index"),
                active: route().current("teacher.courses.*"),
            },
            {
                name: "Mark Attendance",
                href: route("teacher.attendance.index"),
                active: route().current("teacher.attendance.*"),
            },
            {
                name: "Grades",
                href: route("teacher.grades.index"),
                active: route().current("teacher.grades.*"),
            }
        );
    }

    // Staff admin features
    if (isStaff) {
        items.push(
            {
                name: "Manage Courses",
                href: route("admin.courses.index"),
                active: route().current("admin.courses.*"),
            },
            {
                name: "Student Records",
                href: route("students.index"),
                active: route().current("students.*"),
            },
            {
                name: "Manage Users",
                href: route("admin.users.index"),
                active: route().current("admin.users.*"),
            }
        );
    }

    // Placeholders for future modules
    items.push(
        { name: "Grades", href: "/grades", active: false },
        { name: "Fees", href: "/fees", active: false },
        { name: "Timetable", href: "/timetable", active: false },
        { name: "Communication", href: "/communication", active: false }
    );

    return items;
});
</script>

<template>
    <div class="min-h-screen bg-portal-background">
        <div class="flex min-h-screen">
            <!-- Desktop sidebar -->
            <aside
                class="portal-gradient hidden w-64 flex-col border-r border-slate-800/40 text-slate-100 shadow-xl sm:flex"
            >
                <div class="flex items-center gap-3 px-6 py-5">
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center gap-3"
                    >
                        <ApplicationLogo
                            class="h-9 w-9 shrink-0 fill-current text-portal-gold"
                        />
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-300"
                            >
                                University Academic
                            </p>
                            <p class="text-sm font-semibold">Portal</p>
                        </div>
                    </Link>
                </div>

                <nav class="mt-4 flex-1 space-y-1 px-3 text-sm">
                    <template v-for="item in navigation" :key="item.name">
                        <Link
                            :href="item.href"
                            class="group flex items-center gap-2 rounded-lg px-3 py-2 font-medium transition"
                            :class="[
                                item.active
                                    ? 'bg-white/10 text-portal-gold'
                                    : 'text-slate-200 hover:bg-white/5 hover:text-white',
                            ]"
                        >
                            <span
                                class="h-1.5 w-1.5 rounded-full bg-portal-gold/70"
                            />
                            <span>{{ item.name }}</span>
                        </Link>
                    </template>
                </nav>

                <div
                    class="border-t border-slate-800/60 px-4 py-4 text-xs text-slate-300"
                >
                    <p class="font-medium">Logged in as</p>
                    <p class="truncate text-slate-100">
                        {{ $page.props.auth.user.name }}
                    </p>
                </div>
            </aside>

            <!-- Mobile sidebar -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showingMobileSidebar"
                    class="fixed inset-0 z-40 flex sm:hidden"
                >
                    <div
                        class="fixed inset-0 bg-slate-900/70"
                        @click="showingMobileSidebar = false"
                    />

                    <aside
                        class="portal-gradient relative flex w-64 flex-col border-r border-slate-800/40 text-slate-100 shadow-xl"
                    >
                        <div
                            class="flex items-center justify-between px-4 py-4"
                        >
                            <div class="flex items-center gap-2">
                                <ApplicationLogo
                                    class="h-8 w-8 fill-current text-portal-gold"
                                />
                                <span class="text-sm font-semibold">
                                    Academic Portal
                                </span>
                            </div>
                            <button
                                type="button"
                                class="rounded-md p-1 text-slate-200 hover:bg-white/10"
                                @click="showingMobileSidebar = false"
                            >
                                <span class="sr-only">Close navigation</span>
                                <svg
                                    class="h-5 w-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <nav class="mt-2 flex-1 space-y-1 px-3 text-sm">
                            <template
                                v-for="item in navigation"
                                :key="item.name"
                            >
                                <Link
                                    :href="item.href"
                                    class="group flex items-center gap-2 rounded-lg px-3 py-2 font-medium transition"
                                    :class="[
                                        item.active
                                            ? 'bg-white/10 text-portal-gold'
                                            : 'text-slate-200 hover:bg-white/5 hover:text-white',
                                    ]"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-portal-gold/70"
                                    />
                                    <span>{{ item.name }}</span>
                                </Link>
                            </template>
                        </nav>
                    </aside>
                </div>
            </transition>

            <!-- Main content area -->
            <div class="flex min-h-screen flex-1 flex-col">
                <!-- Top bar -->
                <header
                    class="flex items-center justify-between border-b border-slate-200 bg-white/80 px-4 py-3 shadow-sm backdrop-blur-sm sm:px-6 lg:px-8"
                >
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md p-2 text-slate-600 hover:bg-slate-100 sm:hidden"
                            @click="showingMobileSidebar = true"
                        >
                            <span class="sr-only">Open navigation</span>
                            <svg
                                class="h-6 w-6"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                University Academic Portal
                            </p>
                            <div
                                v-if="$slots.header"
                                class="mt-0.5 text-lg font-semibold leading-tight text-slate-900"
                            >
                                <slot name="header" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div
                            class="hidden items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 sm:flex"
                        >
                            <span class="h-2 w-2 rounded-full bg-emerald-500" />
                            Academic year in progress
                        </div>

                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50"
                                    >
                                        <span
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-portal-navy text-xs font-semibold text-white"
                                        >
                                            {{
                                                $page.props.auth.user.name
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                        <span class="hidden sm:inline">
                                            {{ $page.props.auth.user.name }}
                                        </span>
                                        <svg
                                            class="h-4 w-4 text-slate-400"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
