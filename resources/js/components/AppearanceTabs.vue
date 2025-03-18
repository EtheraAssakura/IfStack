<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { Monitor, Moon, Sun } from 'lucide-vue-next';

interface Props {
    class?: string;
}

const { class: containerClass = '' } = defineProps<Props>();

const { appearance, updateAppearance } = useAppearance();

const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Monitor, label: 'System' },
] as const;
</script>

<template>
    <div :class="['inline-flex gap-1 rounded-lg p-1', containerClass, appearance === 'dark' ? 'bg-dark' : 'bg-light']">
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            @click="updateAppearance(value)"
            :class="[
                'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                appearance === value
                    ? 'selected'
                    : 'unselected',
            ]"
        >
            <component :is="Icon" class="-ml-1 h-4 w-4" />
            <span class="ml-1.5 text-sm">{{ label }}</span>
        </button>
    </div>
</template>

<style scoped>
.bg-light {
    background-color: #d2e9f7;
}

.bg-dark {
    background-color: #023048;
}

.selected {
    background-color: #ffa034;
    color: #d7912e;
}

.selected span {
    color: white;
}

.selected svg {
    color: white;
}

.unselected {
    color: #319cb3;
}

.unselected:hover {
    background-color: #d7912e;
    color: #023048;
}
</style>
