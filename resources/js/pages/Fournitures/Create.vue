<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { useDropZone } from '@vueuse/core';
import { ref } from 'vue';

interface Props {
  categories: Array<{
    id: number;
    name: string;
  }>;
  fournisseurs: Array<{
    id: number;
    name: string;
  }>;
}

const props = defineProps<Props>();
const dropZone = ref<HTMLDivElement>();
const isDragging = ref(false);
const preview = ref<string | null>(null);

const form = useForm({
  name: '',
  reference: '',
  packaging: '',
  catalog_url: '',
  category_id: '',
  image: null as File | null,
  fournisseurs: [] as Array<{
    id: number;
    reference: string;
    prix: number;
    catalog_url: string;
  }>,
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Fournitures',
    href: route('fournitures.index'),
  },
  {
    title: 'Nouvelle fourniture',
    href: route('fournitures.create'),
  },
];

const { isOverDropZone } = useDropZone(dropZone, {
  onDrop: (files: File[] | null) => {
    if (!files) return;
    const file = files[0];
    if (file && file.type.startsWith('image/')) {
      if (file.size > 2048 * 1024) {
        alert('L\'image ne doit pas dépasser 2MB');
        return;
      }
      if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        alert('Le format de l\'image doit être JPEG, PNG ou GIF');
        return;
      }
      form.image = file;
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
    if (file.size > 2048 * 1024) {
      alert('L\'image ne doit pas dépasser 2MB');
      return;
    }
    if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
      alert('Le format de l\'image doit être JPEG, PNG ou GIF');
      return;
    }
    form.image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const removeImage = () => {
  form.image = null;
  preview.value = null;
};

const addFournisseur = () => {
  form.fournisseurs.push({
    id: 0,
    reference: '',
    prix: 0,
    catalog_url: '',
  });
};

const removeFournisseur = (index: number) => {
  form.fournisseurs.splice(index, 1);
};

const submit = () => {
  const data = {
    name: form.name,
    reference: form.reference,
    packaging: form.packaging,
    category_id: form.category_id,
    catalog_url: form.catalog_url || '',
    image: form.image,
    fournisseurs: form.fournisseurs.map(f => ({
      id: f.id,
      reference: f.reference,
      prix: f.prix,
      catalog_url: f.catalog_url || ''
    }))
  };

  form.transform(data => ({
    ...data,
    fournisseurs: JSON.stringify(data.fournisseurs)
  })).post(route('fournitures.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      preview.value = null;
    },
    onError: (errors) => {
      console.error('Erreurs de validation:', errors);
    }
  });
};
</script>

<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <Head>
      <title>Nouvelle fourniture - ISFAC</title>
    </Head>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6" enctype="multipart/form-data">
            <div class="space-y-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900">Informations générales</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Informations de base de la fourniture.
                </p>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <Label for="name">Nom</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                  />
                </div>

                <div>
                  <Label for="reference">Référence ISFAC</Label>
                  <Input
                    id="reference"
                    v-model="form.reference"
                    type="text"
                    class="mt-1 block w-full"
                    required
                  />
                </div>

                <div>
                  <Label for="packaging">Conditionnement</Label>
                  <Input
                    id="packaging"
                    v-model="form.packaging"
                    type="text"
                    class="mt-1 block w-full"
                    required
                  />
                </div>

                <div>
                  <Label for="category">Catégorie</Label>
                  <Select
                    id="category"
                    v-model="form.category_id"
                    class="mt-1 block w-full"
                    required
                  >
                    <option value="">Sélectionner une catégorie</option>
                    <option
                      v-for="category in categories"
                      :key="category.id"
                      :value="category.id"
                    >
                      {{ category.name }}
                    </option>
                  </Select>
                </div>

                <div>
                  <Label for="image">Image</Label>
                  <div v-if="preview" class="mb-2">
                    <img :src="preview" alt="Aperçu" class="h-20 w-20 object-cover rounded" />
                    <Button type="button" variant="destructive" size="sm" class="mt-2" @click="removeImage">Supprimer l'image</Button>
                  </div>
                  <div
                    ref="dropZone"
                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                    :class="{ 'border-indigo-500': isOverDropZone }"
                  >
                    <div class="space-y-1 text-center">
                      <svg
                        class="mx-auto h-12 w-12 text-gray-400"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 48 48"
                        aria-hidden="true"
                      >
                        <path
                          d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label
                          for="image"
                          class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                        >
                          <span>Télécharger un fichier</span>
                          <input
                            id="image"
                            name="image"
                            type="file"
                            class="sr-only"
                            accept="image/*"
                            @change="handleFileInput"
                          />
                        </label>
                        <p class="pl-1">ou glisser-déposer</p>
                      </div>
                      <p class="text-xs text-gray-500">PNG, JPG jusqu'à 2MB</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Fournisseurs</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Associez un ou plusieurs fournisseurs à cette fourniture.
                </p>

                <div class="mt-4 space-y-4">
                  <div
                    v-for="(fournisseur, index) in form.fournisseurs"
                    :key="index"
                    class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-end border-b border-gray-200 pb-4"
                  >
                    <div>
                      <Label :for="'fournisseur-' + index">Fournisseur</Label>
                      <Select
                        :id="'fournisseur-' + index"
                        v-model="fournisseur.id"
                        class="mt-1 block w-full"
                        required
                      >
                        <option value="">Sélectionner un fournisseur</option>
                        <option
                          v-for="f in fournisseurs"
                          :key="f.id"
                          :value="f.id"
                        >
                          {{ f.name }}
                        </option>
                      </Select>
                    </div>

                    <div>
                      <Label :for="'reference-' + index">Référence fournisseur</Label>
                      <Input
                        :id="'reference-' + index"
                        v-model="fournisseur.reference"
                        type="text"
                        class="mt-1 block w-full"
                        required
                      />
                    </div>

                    <div>
                      <Label :for="'catalog_url-' + index">URL du catalogue</Label>
                      <Input
                        :id="'catalog_url-' + index"
                        v-model="fournisseur.catalog_url"
                        type="url"
                        class="mt-1 block w-full"
                      />
                    </div>

                    <div>
                      <Label :for="'prix-' + index">Prix unitaire</Label>
                      <div class="flex items-center space-x-2">
                        <Input
                          :id="'prix-' + index"
                          v-model="fournisseur.prix"
                          type="number"
                          step="0.01"
                          min="0"
                          class="mt-1 block w-full"
                          required
                        />
                        <Button
                          type="button"
                          variant="destructive"
                          @click="removeFournisseur(index)"
                        >
                          Supprimer
                        </Button>
                      </div>
                    </div>
                  </div>

                  <Button
                    type="button"
                    variant="outline"
                    @click="addFournisseur"
                  >
                    Ajouter un fournisseur
                  </Button>
                </div>
              </div>

              <div class="flex justify-end space-x-3">
                <Button
                  type="button"
                  variant="outline"
                  :href="route('fournitures.index')"
                >
                  Annuler
                </Button>
                <Button type="submit" :disabled="form.processing">
                  Créer la fourniture
                </Button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<style scoped>
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}
</style>