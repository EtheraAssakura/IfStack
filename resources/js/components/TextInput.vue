<template>
  <input
    class="block w-full rounded-xl border-0 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200/50 transition-colors placeholder:text-gray-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-primary disabled:pointer-events-none disabled:opacity-50"
    :value="modelValue"
    @input="handleInput"
    v-bind="$attrs"
  />
</template>

<script setup lang="ts">
defineProps<{
  modelValue: string;
}>();

const emit = defineEmits<{
  'update:modelValue': [value: string];
}>();

defineOptions({
  name: 'TextInput',
});

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target) {
    emit('update:modelValue', target.value);
  }
};
</script>
