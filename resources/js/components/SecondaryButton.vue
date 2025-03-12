<template>
    <button
        class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>

<script setup lang="ts">
defineProps<{
    disabled?: boolean;
}>();
</script>
