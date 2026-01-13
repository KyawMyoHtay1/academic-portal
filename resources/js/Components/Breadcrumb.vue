<script setup>
import { Link } from "@inertiajs/vue3";
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
});

const breadcrumbItems = computed(() => {
    // Always start with Dashboard
    const items = [
        {
            label: "Dashboard",
            href: route("dashboard"),
        },
    ];

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
