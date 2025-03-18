<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    class: '',
});

const classes = computed(() => {
    return cn(
        'p-4 align-middle [&:has([role=checkbox])]:pr-0',
        props.class
    );
});
</script>

<template>
    <td :class="classes">
        <slot />
    </td>
</template>