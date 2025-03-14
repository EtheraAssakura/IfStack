<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const classes = computed(() => {
    return cn('flex items-center gap-x-2', props.class);
});
</script>

<template>
    <div :class="classes">
        <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
            <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
        </div>
        <div class="ml-1 grid flex-1 text-left text-sm">
            <span class="mb-0.5 truncate font-semibold leading-none">IfStack</span>
        </div>
    </div>
</template>
