<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';

interface Props {
    for?: string;
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();

const classes = computed(() => {
    return cn(
        'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70',
        props.class
    );
});
</script>

<template>
    <label :for="props.for" :class="classes">
        <slot />
    </label>
</template>
