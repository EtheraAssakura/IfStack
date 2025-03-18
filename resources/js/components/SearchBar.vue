<template>
    <div class="group relative">
        <div
            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 transition-opacity duration-300"
            :class="{ 'opacity-0': isFocused && modelValue }"
        >
            <SearchIcon class="h-5 w-5 text-gray-400" />
        </div>
        <input
            type="search"
            :value="modelValue"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            @focus="isFocused = true"
            @blur="isFocused = false"
            class="block w-full rounded-xl border-0 bg-gray-50 py-3 pl-12 pr-4 text-gray-900 ring-1 ring-inset ring-gray-200 transition-all duration-300 placeholder:text-gray-500 focus:bg-white focus:ring-2 focus:ring-primary group-hover:ring-gray-300"
            :class="{ 'pl-4': isFocused && modelValue }"
            v-bind="$attrs"
        />
        <button
            v-if="modelValue"
            @click="$emit('update:modelValue', '')"
            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600"
        >
            <XIcon class="h-5 w-5" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { SearchIcon, XIcon } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    modelValue: string;
}>();

defineEmits<{
    'update:modelValue': [value: string];
}>();

const isFocused = ref(false);
</script>
