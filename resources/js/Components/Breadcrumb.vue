<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    items: {
        type: Array,
        required: true,
        validator: (items) => {
            return items.every(
                (item) =>
                    typeof item === "object" &&
                    typeof item.label === "string" &&
                    (item.href === undefined || typeof item.href === "string")
            );
        },
    },
    autoGroup: {
        type: Boolean,
        default: true,
    },
});

const page = usePage();

// Helper function to determine which group a route belongs to
const getGroupForRoute = (currentRoute) => {
    const user = page.props.auth?.user;
    const role = user?.role;

    // Route pattern matching for groups
    const routeName = currentRoute || route().current();

    if (!routeName) return null;

    // Student role groups
    if (role === "student") {
        // Student group
        if (routeName.match(/^student\.profile\./)) {
            return { name: "Student", href: null };
        }

        // Academics group
        if (
            routeName.match(/^courses\./) ||
            routeName.match(/^my-courses\./) ||
            routeName.match(/^student\.grades\./) ||
            routeName.match(/^student\.attendance\./) ||
            routeName.match(/^student\.assignments\./) ||
            routeName.match(/^student\.timetable\./)
        ) {
            return { name: "Academics", href: null };
        }

        // Finance group
        if (routeName.match(/^student\.fees\./)) {
            return { name: "Finance", href: null };
        }

        // Communication group
        if (
            routeName.match(/^announcements\./) ||
            routeName.match(/^notifications\./) ||
            routeName.match(/^messages\./)
        ) {
            return { name: "Communication", href: null };
        }
    }

    // Teacher role groups
    if (role === "teacher") {
        // Teaching group
        if (
            routeName.match(/^teacher\.courses\./) ||
            routeName.match(/^teacher\.timetable\./) ||
            routeName.match(/^teacher\.attendance\./) ||
            routeName.match(/^teacher\.grades\./) ||
            routeName.match(/^teacher\.assignments\./) ||
            routeName.match(/^teacher\.announcements\./)
        ) {
            return { name: "Teaching", href: null };
        }

        // Communication group
        if (
            routeName.match(/^announcements\./) ||
            routeName.match(/^notifications\./) ||
            routeName.match(/^messages\./)
        ) {
            return { name: "Communication", href: null };
        }
    }

    // Staff role groups
    if (role === "staff" || role === "admin") {
        // Academics group
        if (
            routeName.match(/^admin\.courses\./) ||
            routeName.match(/^admin\.subjects\./) ||
            routeName.match(/^admin\.timetables\./) ||
            routeName.match(/^admin\.enrollments\./) ||
            routeName.match(/^admin\.grades\./) ||
            routeName.match(/^admin\.attendance\./)
        ) {
            return { name: "Academics", href: null };
        }

        // People group
        if (
            routeName.match(/^students\./) ||
            routeName.match(/^admin\.users\./)
        ) {
            return { name: "People", href: null };
        }

        // Finance group
        if (routeName.match(/^admin\.fees\./)) {
            return { name: "Finance", href: null };
        }

        // Communication group
        if (
            routeName.match(/^admin\.announcements\./) ||
            routeName.match(/^admin\.contact-messages\./) ||
            routeName.match(/^admin\.feedback-messages\./) ||
            routeName.match(/^notifications\./) ||
            routeName.match(/^messages\./)
        ) {
            return { name: "Communication", href: null };
        }
    }

    return null;
};

const breadcrumbItems = computed(() => {
    // Always start with Dashboard
    const items = [
        {
            label: "Dashboard",
            href: route("dashboard"),
        },
    ];

    // Automatically add group if enabled and route matches
    if (props.autoGroup) {
        const group = getGroupForRoute();
        if (group) {
            items.push({
                label: group.name,
                href: group.href,
            });
        }
    }

    // Add custom items
    items.push(...props.items);

    return items;
});
</script>

<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li
                v-for="(item, index) in breadcrumbItems"
                :key="index"
                class="flex items-center"
            >
                <div class="flex items-center">
                    <Link
                        v-if="item.href && index < breadcrumbItems.length - 1"
                        :href="item.href"
                        class="text-sm font-medium text-slate-500 hover:text-portal-navy transition-colors"
                    >
                        {{ item.label }}
                    </Link>
                    <span
                        v-else
                        class="text-sm font-medium text-slate-900"
                        :aria-current="
                            index === breadcrumbItems.length - 1
                                ? 'page'
                                : undefined
                        "
                    >
                        {{ item.label }}
                    </span>
                </div>
                <svg
                    v-if="index < breadcrumbItems.length - 1"
                    class="ml-2 h-4 w-4 flex-shrink-0 text-slate-400"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                        clip-rule="evenodd"
                    />
                </svg>
            </li>
        </ol>
    </nav>
</template>
