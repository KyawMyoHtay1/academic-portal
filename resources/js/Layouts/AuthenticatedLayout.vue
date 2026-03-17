<script setup>
import { ref } from "vue";
import PortalLogo from "@/Components/PortalLogo.vue";
import GlobalSearch from "@/Components/GlobalSearch.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import GoogleTranslate from "@/Components/GoogleTranslate.vue";
import PageLoadingIndicator from "@/Components/PageLoadingIndicator.vue";
import GlobalToastStack from "@/Components/GlobalToastStack.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useAuthenticatedLayoutNavigation } from "@/composables/useAuthenticatedLayoutNavigation";
import { useAuthenticatedLayoutNotifications } from "@/composables/useAuthenticatedLayoutNotifications";

const showingMobileSidebar = ref(false);
const page = usePage();
const openNavGroups = ref({});

const { navigation, headerStatus, userRoleMeta } =
    useAuthenticatedLayoutNavigation(page);
const {
    unreadNotificationCount,
    notificationsPreview,
    isMarkingNotificationsRead,
    markAllNotificationsRead,
    openNotificationFromPreview,
    openNotificationCenter,
    truncateNotificationText,
} = useAuthenticatedLayoutNotifications(page);
</script>

<template>
    <div class="min-h-screen bg-portal-background">
        <PageLoadingIndicator />
        <GlobalToastStack />

        <div class="flex min-h-screen">
            <!-- Desktop sidebar -->
            <aside
                class="portal-gradient fixed left-0 top-0 z-20 hidden h-screen w-64 flex-col border-r border-slate-800/40 text-slate-100 shadow-xl sm:flex"
            >
                <div class="flex items-center gap-3 px-6 py-5">
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center gap-3 transition-opacity hover:opacity-90"
                    >
                        <PortalLogo class="h-9 w-9 shrink-0" />
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-300"
                            >
                                University Academic
                            </p>
                            <p class="text-sm font-semibold text-portal-gold">
                                Portal
                            </p>
                        </div>
                    </Link>
                </div>

                <nav class="mt-4 flex-1 space-y-1 px-3 text-sm overflow-y-auto">
                    <template v-for="item in navigation" :key="item.name">
                        <!-- Regular menu item -->
                        <template v-if="!item.isGroup">
                            <Link
                                :href="item.href"
                                class="group flex items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                :class="[
                                    item.active
                                        ? 'bg-white/10 text-portal-gold'
                                        : 'text-slate-200 hover:bg-white/5 hover:text-white',
                                ]"
                            >
                                <div class="flex items-center gap-3">
                                    <svg
                                        class="h-5 w-5 shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            :d="item.icon"
                                        />
                                    </svg>
                                    <span>{{ item.name }}</span>
                                </div>
                                <span
                                    v-if="item.badge && item.badge > 0"
                                    class="inline-flex items-center justify-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                >
                                    {{ item.badge }}
                                </span>
                            </Link>
                        </template>

                        <!-- Grouped menu items (dropdown) -->
                        <template v-else>
                            <div class="space-y-1">
                                <button
                                    type="button"
                                    class="group flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                    :class="[
                                        item.active
                                            ? 'bg-white/10 text-portal-gold'
                                            : 'text-slate-200 hover:bg-white/5 hover:text-white',
                                    ]"
                                    @click="
                                        openNavGroups[item.name] = !(
                                            openNavGroups[item.name] ??
                                            item.active
                                        )
                                    "
                                >
                                    <div class="flex items-center gap-3">
                                        <svg
                                            class="h-5 w-5 shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                :d="item.icon"
                                            />
                                        </svg>
                                        <span>{{ item.name }}</span>
                                    </div>
                                    <svg
                                        class="h-4 w-4 transition-transform duration-200"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        :class="[
                                            openNavGroups[item.name] ??
                                            item.active
                                                ? 'rotate-180'
                                                : '',
                                        ]"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>

                                <!-- Dropdown menu items -->
                                <transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="opacity-0 transform -translate-y-1"
                                    enter-to-class="opacity-100 transform translate-y-0"
                                    leave-active-class="transition duration-150 ease-in"
                                    leave-from-class="opacity-100 transform translate-y-0"
                                    leave-to-class="opacity-0 transform -translate-y-1"
                                >
                                    <div
                                        v-show="
                                            openNavGroups[item.name] ??
                                            item.active
                                        "
                                        class="ml-4 space-y-1 border-l-2 border-slate-700/50 pl-2"
                                    >
                                        <Link
                                            v-for="child in item.children"
                                            :key="child.name"
                                            :href="child.href"
                                            class="group flex items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                            :class="[
                                                child.active
                                                    ? 'bg-white/10 text-portal-gold'
                                                    : 'text-slate-300 hover:bg-white/5 hover:text-white',
                                            ]"
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <svg
                                                    class="h-4 w-4 shrink-0"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        :d="child.icon"
                                                    />
                                                </svg>
                                                <span class="text-xs">{{
                                                    child.name
                                                }}</span>
                                            </div>
                                            <span
                                                v-if="
                                                    child.badge &&
                                                    child.badge > 0
                                                "
                                                class="inline-flex items-center justify-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                            >
                                                {{ child.badge }}
                                            </span>
                                        </Link>
                                    </div>
                                </transition>
                            </div>
                        </template>
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
                                <PortalLogo class="h-8 w-8 shrink-0" />
                                <span class="text-sm font-semibold">
                                    University Academic Portal
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

                        <nav
                            class="mt-2 flex-1 space-y-1 px-3 text-sm overflow-y-auto"
                        >
                            <template
                                v-for="item in navigation"
                                :key="item.name"
                            >
                                <!-- Regular menu item -->
                                <template v-if="!item.isGroup">
                                    <Link
                                        :href="item.href"
                                        class="group flex items-center gap-2 rounded-lg px-3 py-2 font-medium transition"
                                        :class="[
                                            item.active
                                                ? 'bg-white/10 text-portal-gold'
                                                : 'text-slate-200 hover:bg-white/5 hover:text-white',
                                        ]"
                                    >
                                        <svg
                                            class="h-5 w-5 shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                :d="item.icon"
                                            />
                                        </svg>
                                        <span>{{ item.name }}</span>
                                        <span
                                            v-if="item.badge && item.badge > 0"
                                            class="ml-auto inline-flex items-center justify-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                        >
                                            {{ item.badge }}
                                        </span>
                                    </Link>
                                </template>

                                <!-- Grouped menu items (dropdown) -->
                                <template v-else>
                                    <div class="space-y-1">
                                        <button
                                            type="button"
                                            class="group flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                            :class="[
                                                item.active
                                                    ? 'bg-white/10 text-portal-gold'
                                                    : 'text-slate-200 hover:bg-white/5 hover:text-white',
                                            ]"
                                            @click="
                                                openNavGroups[item.name] = !(
                                                    openNavGroups[item.name] ??
                                                    item.active
                                                )
                                            "
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <svg
                                                    class="h-5 w-5 shrink-0"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        :d="item.icon"
                                                    />
                                                </svg>
                                                <span>{{ item.name }}</span>
                                            </div>
                                            <svg
                                                class="h-4 w-4 transition-transform duration-200"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                                :class="[
                                                    openNavGroups[item.name] ??
                                                    item.active
                                                        ? 'rotate-180'
                                                        : '',
                                                ]"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>

                                        <!-- Dropdown menu items -->
                                        <transition
                                            enter-active-class="transition duration-200 ease-out"
                                            enter-from-class="opacity-0 transform -translate-y-1"
                                            enter-to-class="opacity-100 transform translate-y-0"
                                            leave-active-class="transition duration-150 ease-in"
                                            leave-from-class="opacity-100 transform translate-y-0"
                                            leave-to-class="opacity-0 transform -translate-y-1"
                                        >
                                            <div
                                                v-show="
                                                    openNavGroups[item.name] ??
                                                    item.active
                                                "
                                                class="ml-4 space-y-1 border-l-2 border-slate-700/50 pl-2"
                                            >
                                                <Link
                                                    v-for="child in item.children"
                                                    :key="child.name"
                                                    :href="child.href"
                                                    class="group flex items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                                    :class="[
                                                        child.active
                                                            ? 'bg-white/10 text-portal-gold'
                                                            : 'text-slate-300 hover:bg-white/5 hover:text-white',
                                                    ]"
                                                >
                                                    <div
                                                        class="flex items-center gap-2"
                                                    >
                                                        <svg
                                                            class="h-4 w-4 shrink-0"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                :d="child.icon"
                                                            />
                                                        </svg>
                                                        <span class="text-xs">{{
                                                            child.name
                                                        }}</span>
                                                    </div>
                                                    <span
                                                        v-if="
                                                            child.badge &&
                                                            child.badge > 0
                                                        "
                                                        class="inline-flex items-center justify-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                                    >
                                                        {{ child.badge }}
                                                    </span>
                                                </Link>
                                            </div>
                                        </transition>
                                    </div>
                                </template>
                            </template>
                        </nav>
                    </aside>
                </div>
            </transition>

            <!-- Main content area -->
            <div class="flex min-h-screen flex-1 flex-col sm:ml-64">
                <!-- Top bar -->
                <header
                    class="sticky top-0 z-30 flex flex-wrap items-center gap-2 border-b border-slate-200 bg-white/80 px-4 py-2 shadow-sm backdrop-blur-sm md:flex-nowrap md:px-6 lg:px-8"
                >
                    <div class="order-1 flex min-w-0 items-center gap-2 md:gap-3">
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

                        <div class="min-w-0">
                            <p
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 md:text-xs"
                            >
                                <span class="md:hidden">UAP</span>
                                <span class="hidden md:inline"
                                    >University Academic Portal</span
                                >
                            </p>
                        </div>
                    </div>

                    <div class="order-2 ml-auto flex items-center gap-2 md:order-3 md:ml-2 md:gap-4">
                        <!-- Google Translate -->
                        <GoogleTranslate />

                        <!-- Notification dropdown -->
                        <Dropdown
                            align="right"
                            width="80"
                            content-classes="overflow-hidden rounded-xl bg-white py-0"
                        >
                            <template #trigger>
                                <button
                                    type="button"
                                    class="relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm hover:bg-slate-50"
                                >
                                    <span class="sr-only">
                                        Open notifications
                                    </span>
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
                                            stroke-width="1.8"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1"
                                        />
                                    </svg>
                                    <span
                                        v-if="unreadNotificationCount > 0"
                                        class="absolute -right-0.5 -top-0.5 inline-flex min-w-[1.1rem] items-center justify-center rounded-full bg-portal-gold px-1 text-[10px] font-semibold text-slate-900 ring-2 ring-white"
                                    >
                                        {{
                                            unreadNotificationCount > 9
                                                ? "9+"
                                                : unreadNotificationCount
                                        }}
                                    </span>
                                </button>
                            </template>

                            <template #content>
                                <div class="max-h-[30rem]">
                                    <div
                                        class="flex items-center justify-between border-b border-slate-200 px-4 py-3"
                                    >
                                        <p
                                            class="text-sm font-semibold text-slate-900"
                                        >
                                            Notifications
                                        </p>
                                        <button
                                            type="button"
                                            v-if="unreadNotificationCount > 0"
                                            @click="markAllNotificationsRead"
                                            :disabled="isMarkingNotificationsRead"
                                            class="text-xs font-semibold text-portal-navy hover:text-portal-navy-dark"
                                        >
                                            {{
                                                isMarkingNotificationsRead
                                                    ? "Marking..."
                                                    : "Mark all read"
                                            }}
                                        </button>
                                    </div>

                                    <div
                                        v-if="notificationsPreview.length > 0"
                                        class="max-h-80 overflow-y-auto"
                                    >
                                        <button
                                            type="button"
                                            v-for="notification in notificationsPreview"
                                            :key="notification.id"
                                            @click="
                                                openNotificationFromPreview(
                                                    notification
                                                )
                                            "
                                            class="block w-full border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50"
                                            :class="
                                                notification.read_at
                                                    ? 'bg-white'
                                                    : 'bg-emerald-50/40'
                                            "
                                        >
                                            <div
                                                class="flex items-start justify-between gap-3"
                                            >
                                                <p
                                                    class="text-sm font-semibold text-slate-900"
                                                >
                                                    {{ notification.title }}
                                                </p>
                                                <span
                                                    v-if="!notification.read_at"
                                                    class="mt-1 h-2 w-2 flex-shrink-0 rounded-full bg-emerald-500"
                                                />
                                            </div>
                                            <p
                                                class="mt-1 text-xs text-slate-600"
                                            >
                                                {{
                                                    truncateNotificationText(
                                                        notification.message
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="mt-1 text-[11px] text-slate-500"
                                            >
                                                {{
                                                    notification.created_label ||
                                                    notification.created_at
                                                }}
                                            </p>
                                        </button>
                                    </div>

                                    <div
                                        v-else
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No unread notifications.
                                    </div>

                                    <div
                                        class="border-t border-slate-200 px-4 py-2"
                                    >
                                        <button
                                            type="button"
                                            @click="openNotificationCenter"
                                            class="text-xs font-semibold text-portal-navy hover:text-portal-navy-dark"
                                        >
                                            Open notification center
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </Dropdown>

                        <!-- User dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-full bg-white p-1.5 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50"
                                    >
                                        <span
                                            class="relative inline-flex h-8 w-8 items-center justify-center rounded-full bg-portal-navy text-xs font-semibold text-white overflow-hidden"
                                        >
                                            <img
                                                v-if="
                                                    $page.props.auth.user.photo
                                                "
                                                :src="`/storage/${$page.props.auth.user.photo}`"
                                                :alt="`Photo for ${$page.props.auth.user.name}`"
                                                class="h-full w-full object-cover"
                                            />
                                            <span v-else>
                                                {{
                                                    $page.props.auth.user.name
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                            <span
                                                class="absolute -bottom-0.5 -right-0.5 h-2 w-2 rounded-full border border-white"
                                                :class="userRoleMeta.dotClass"
                                            />
                                        </span>
                                        <svg
                                            class="hidden h-4 w-4 text-slate-400 md:block"
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
                                    <div
                                        class="border-b border-slate-100 px-4 py-3"
                                    >
                                        <p
                                            class="text-sm font-semibold text-slate-900"
                                        >
                                            {{ $page.props.auth.user.name }}
                                        </p>
                                        <p
                                            v-if="userRoleMeta.label"
                                            class="mt-0.5 text-xs text-slate-500"
                                        >
                                            {{ userRoleMeta.label }}
                                        </p>
                                    </div>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink :href="route('settings.index')">
                                        Settings
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

                    <div class="order-3 w-full md:order-2 md:ml-auto md:w-auto">
                        <!-- Global search -->
                        <GlobalSearch />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    <!-- Page header & context -->
                    <div
                        v-if="$slots.header || $slots.breadcrumb"
                        class="mb-4 space-y-2"
                    >
                        <div
                            v-if="$slots.header || headerStatus"
                            class="flex flex-wrap items-center justify-between gap-3"
                        >
                            <div
                                v-if="$slots.header"
                                class="text-xl font-semibold leading-tight text-slate-900"
                            >
                                <slot name="header" />
                            </div>
                            <div
                                v-if="headerStatus"
                                class="flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                :class="userRoleMeta.statusClasses"
                            >
                                <span
                                    class="h-2 w-2 rounded-full"
                                    :class="userRoleMeta.dotClass"
                                />
                                {{ headerStatus }}
                            </div>
                        </div>

                        <slot name="breadcrumb" />
                    </div>

                    <slot />
                </main>

                <!-- Footer -->
                <footer
                    class="border-t border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8"
                >
                    <div
                        class="flex flex-wrap items-center justify-center gap-4 text-xs text-slate-600"
                    >
                        <Link
                            :href="route('privacy-policy')"
                            class="hover:text-portal-navy transition-colors"
                        >
                            Privacy Policy
                        </Link>
                        <span class="text-slate-400">•</span>
                        <span>
                            © {{ new Date().getFullYear() }} University Academic
                            Portal
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>

