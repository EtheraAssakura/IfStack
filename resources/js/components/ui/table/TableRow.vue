<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const classes = computed(() => {
    return cn('transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted', props.class);
});
</script>

<template>
    <tr :class="classes">
        <slot />
    </tr>
</template>