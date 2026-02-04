<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

// Filter out "Previous" and "Next" text if you want to replace them with icons,
// or keep them as is. Laravel defaults are usually "&laquo; Previous" and "Next &raquo;".
// For this design, we'll keep them but might style them differently.
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap items-center justify-center gap-1">
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="mb-1 mr-1 rounded-md border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-400"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                class="mb-1 mr-1 rounded-md border px-3 py-1.5 text-xs font-medium focus:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                :class="{
                    'border-indigo-500 bg-indigo-50 text-indigo-700': link.active,
                    'border-slate-200 bg-white text-slate-700 hover:bg-slate-50': !link.active,
                }"
                v-html="link.label"
            />
        </template>
    </div>
</template>
