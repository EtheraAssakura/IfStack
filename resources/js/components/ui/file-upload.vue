<script setup lang="ts">
import { Upload, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Button } from './button';

defineOptions({
  name: 'FileUpload'
});

interface Props {
  modelValue: File | null;
  accept?: string;
  maxSize?: number;
  class?: string;
  currentFile?: string | null;
}

const props = withDefaults(defineProps<Props>(), {
  accept: 'image/*',
  maxSize: 5 * 1024 * 1024, // 5MB par défaut
});

const emit = defineEmits(['update:modelValue', 'error']);

const dragOver = ref(false);
const error = ref('');

const handleDrop = (e: DragEvent) => {
  e.preventDefault();
  dragOver.value = false;

  const files = e.dataTransfer?.files;
  if (files?.length) {
    validateAndEmitFile(files[0]);
  }
};

const handleFileInput = (e: Event) => {
  const input = e.target as HTMLInputElement;
  const files = input.files;
  if (files?.length) {
    validateAndEmitFile(files[0]);
  }
};

const validateAndEmitFile = (file: File) => {
  error.value = '';

  if (!file.type.startsWith('image/')) {
    error.value = 'Le fichier doit être une image';
    emit('error', error.value);
    return;
  }

  if (file.size > props.maxSize) {
    error.value = `Le fichier ne doit pas dépasser ${props.maxSize / 1024 / 1024}MB`;
    emit('error', error.value);
    return;
  }

  emit('update:modelValue', file);
};

const clearFile = () => {
  emit('update:modelValue', null);
};

watch(() => props.modelValue, (newValue) => {
  if (!newValue) {
    error.value = '';
  }
});
</script>

<template>
  <div class="space-y-2">
    <div
      :class="[
        'relative border-2 border-dashed rounded-lg p-6 transition-colors',
        'hover:border-gray-400 focus-within:border-gray-400',
        dragOver ? 'border-gray-400 bg-gray-50' : 'border-gray-200',
        props.class
      ]"
      @dragover.prevent="dragOver = true"
      @dragleave.prevent="dragOver = false"
      @drop="handleDrop"
    >
      <input
        type="file"
        :accept="accept"
        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
        @change="handleFileInput"
      />

      <div class="text-center">
        <Upload class="mx-auto h-12 w-12 text-gray-400" />

        <div class="mt-4 flex text-sm leading-6 text-gray-600">
          <label class="relative cursor-pointer rounded-md font-semibold text-primary hover:text-primary/80">
            <span>Cliquez pour télécharger</span>
            <span class="mx-1">ou</span>
            <span>glissez et déposez</span>
          </label>
        </div>

        <p class="text-xs leading-5 text-gray-600 mt-1">
          PNG, JPG, GIF jusqu'à {{ props.maxSize / 1024 / 1024 }}MB
        </p>
      </div>

      <div v-if="modelValue || currentFile" class="mt-4">
        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
          <span class="text-sm text-gray-600 truncate">
            {{ modelValue?.name || currentFile }}
          </span>
          <Button variant="ghost" size="icon" @click="clearFile">
            <X class="h-4 w-4" />
          </Button>
        </div>
      </div>
    </div>

    <p v-if="error" class="text-sm text-red-600">
      {{ error }}
    </p>
  </div>
</template>
