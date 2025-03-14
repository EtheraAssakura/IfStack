<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    modelValue: string | number;
    class?: string;
}

const props = defineProps<Props>();

const emits = defineEmits<{
    'update:modelValue': [value: string];
}>();

const classes = computed(() => {
    return cn(
        'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background',
        'file:border-0 file:bg-transparent file:text-sm file:font-medium',
        'placeholder:text-muted-foreground',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
        'disabled:cursor-not-allowed disabled:opacity-50',
        props.class
    );
});

const handleChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    emits('update:modelValue', target.value);
};
</script>

<template>
    <select :value="modelValue" :class="classes" @change="handleChange">
        <slot />
    </select>
</template> 