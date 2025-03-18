<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();

const classes = computed(() => {
    return cn('h-8 w-auto', props.class);
});
</script>

<template>
    <img
        src="/Logo.png"
        alt="Logo"
        :class="classes"
    />
</template>
