<template>
  <button
    class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary/80 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:pointer-events-none disabled:opacity-50"
  >
    <slot />
  </button>
</template>

<script setup lang="ts">
defineOptions({
  name: 'PrimaryButton',
});
</script>
