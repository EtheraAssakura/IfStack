<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const classes = computed(() => {
    return cn('divide-y divide-border', props.class);
});
</script>

<template>
    <tbody :class="classes">
        <slot />
    </tbody>
</template>