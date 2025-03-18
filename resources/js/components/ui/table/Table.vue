<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const classes = computed(() => {
    return cn('w-full caption-bottom text-sm', props.class);
});
</script>

<template>
    <div class="relative w-full overflow-auto">
        <table :class="classes">
            <slot />
        </table>
    </div>
</template>