<script setup>
defineOptions({
    inheritAttrs: false,
});

const emit = defineEmits(['update:checked']);

defineProps({
    checked: {
        type: Boolean,
        default: false,
    },
    value: {
        type: [String, Number, Boolean],
        default: null,
    },
});

const onChange = (event) => {
    emit('update:checked', event.target.checked);
};
</script>

<template>
    <input
        v-bind="$attrs"
        type="checkbox"
        :value="value"
        :checked="checked"
        @change="onChange"
        class="h-4 w-4 cursor-pointer appearance-auto rounded border-gray-300 accent-indigo-600 shadow-sm focus:ring-indigo-500"
    />
</template>
