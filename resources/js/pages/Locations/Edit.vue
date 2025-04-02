<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <Head>
      <title>Modifier {{ location.name }} - ISFAC</title>
    </Head>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="handleSubmit" class="space-y-6">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <Label for="name">Nom</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 block w-full"
                  />
                  <div v-if="form.errors.name" class="text-sm text-red-600">
                    {{ form.errors.name }}
                  </div>
                </div>

                <div>
                  <Label for="site_id">Site</Label>
                  <select
                    id="site_id"
                    v-model="form.site_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                    required
                  >
                    <option v-for="site in sites" :key="site.id" :value="site.id">
                      {{ site.name }}
                    </option>
                  </select>
                  <div v-if="form.errors.site_id" class="text-sm text-red-600">
                    {{ form.errors.site_id }}
                  </div>
                </div>

                <div class="space-y-2 md:col-span-2">
                  <Label for="description">Description</Label>
                  <Textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    required
                    class="mt-1 block w-full"
                  />
                  <div v-if="form.errors.description" class="text-sm text-red-600">
                    {{ form.errors.description }}
                  </div>
                </div>

                <div class="space-y-2 md:col-span-2">
                  <Label for="photo">Photo</Label>
                  <div ref="dropZone" class="flex items-center justify-center h-32 border-2 border-dashed rounded-lg" :class="{ 'border-primary-500': isDragging }">
                    <div v-if="preview" class="relative">
                      <img :src="preview" alt="Preview" class="h-24 w-24 object-cover rounded-lg">
                      <button type="button" @click="removeImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                    <div v-else class="text-center">
                      <input type="file" accept="image/*" class="hidden" @change="handleFileInput">
                      <button type="button" class="text-gray-500 hover:text-gray-700" @click="$el.querySelector('input[type=file]').click()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="mt-2 block text-sm">Cliquez pour sélectionner une image</span>
                        <span class="text-xs text-gray-400">ou glissez-déposez ici</span>
                      </button>
                    </div>
                  </div>
                  <div v-if="form.errors.photo" class="text-sm text-red-600">
                    {{ form.errors.photo }}
                  </div>
                </div>
              </div>

              <div class="flex justify-end space-x-3">
                <Button
                  type="button"
                  variant="secondary"
                  @click="router.visit(route('locations.show', location.id))"
                >
                  Annuler
                </Button>
                <Button
                  type="submit"
                  variant="default"
                  :disabled="form.processing"
                >
                  Enregistrer
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useDropZone } from '@vueuse/core';
import { ref } from 'vue';

interface Site {
  id: number;
  name: string;
}

interface Location {
  id: number;
  name: string;
  description: string;
  site_id: number;
  photo_path: string | null;
  site: Site;
}

interface Props {
  location: Location;
  sites: Site[];
}

const props = defineProps<Props>();
const dropZone = ref<HTMLDivElement>();
const isDragging = ref(false);
const preview = ref<string | null>(props.location.photo_path);

const form = useForm({
  name: props.location.name,
  description: props.location.description,
  site_id: props.location.site_id,
  photo: null as File | null,
  _method: 'PUT'
});

useDropZone(dropZone, {
  onDrop: (files: File[] | null) => {
    if (!files) return;
    const file = files[0];
    if (file && file.type.startsWith('image/')) {
      if (file.size > 2 * 1024 * 1024) {
        alert('L\'image ne doit pas dépasser 2MB');
        return;
      }
      if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        alert('Le format de l\'image doit être JPEG, PNG ou GIF');
        return;
      }
      form.photo = file;
      const reader = new FileReader();
      reader.onload = (e) => {
        preview.value = e.target?.result as string;
      };
      reader.readAsDataURL(file);
    }
  },
  onEnter: () => {
    isDragging.value = true;
  },
  onLeave: () => {
    isDragging.value = false;
  },
});

const handleFileInput = (event: Event) => {
  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];
  if (file && file.type.startsWith('image/')) {
    if (file.size > 2 * 1024 * 1024) {
      alert('L\'image ne doit pas dépasser 2MB');
      return;
    }
    if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
      alert('Le format de l\'image doit être JPEG, PNG ou GIF');
      return;
    }
    form.photo = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const removeImage = () => {
  form.photo = null;
  preview.value = null;
};

const breadcrumbs: BreadcrumbItemType[] = [
  {
    title: 'Sites',
    href: route('sites.index'),
  },
  {
    title: props.location.site.name,
    href: route('sites.show', props.location.site.id),
  },
  {
    title: props.location.name,
    href: route('locations.show', props.location.id),
  },
  {
    title: 'Modifier',
    href: route('locations.edit', props.location.id),
  },
];

const handleSubmit = () => {
  form.transform(data => ({
    ...data
  })).post(route('locations.update', props.location.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      preview.value = null;
      router.visit(route('locations.show', props.location.id));
    },
    onError: (errors: Record<string, string>) => {
      console.error('Erreurs de validation:', errors);
    }
  });
};
</script>
