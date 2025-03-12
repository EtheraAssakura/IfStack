<template>
    <select
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
        :value="modelValue"
        @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
        v-bind="$attrs"
    >
        <slot />
    </select>
</template>

<script setup lang="ts">
defineProps<{
    modelValue: string | number;
}>();

defineEmits<{
    'update:modelValue': [value: string];
}>();
</script>
