<template>
    <button
        class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 disabled:pointer-events-none disabled:opacity-50"
    >
        <slot />
    </button>
</template>

<script setup lang="ts">
defineOptions({
    name: 'DangerButton',
});
</script>
