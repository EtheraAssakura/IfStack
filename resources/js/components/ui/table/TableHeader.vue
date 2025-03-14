<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const classes = computed(() => {
    return cn('[&_tr]:border-b', props.class);
});
</script>

<template>
    <thead :class="classes">
        <slot />
    </thead>
</template>