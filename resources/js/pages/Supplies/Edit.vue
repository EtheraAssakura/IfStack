<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle
} from "@/components/ui/dialog";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useDropZone } from '@vueuse/core';
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
  supply: {
    id: number;
    name: string;
    reference: string;
    packaging: string;
    catalog_url: string | null;
    image_url: string | null;
    category_id: number;
    category: {
      id: number;
      name: string;
    } | null;
    suppliers: Array<{
      id: number;
      name: string;
      catalog_url: string | null;
      pivot: {
        supplier_reference: string;
        unit_price: number;
        catalog_url: string | null;
      };
    }>;
  };
  categories: Array<{
    id: number;
    name: string;
  }>;
  suppliers: Array<{
    id: number;
    name: string;
  }>;
}

const props = defineProps<Props>();
const dropZone = ref<HTMLDivElement>();
const isDragging = ref(false);
const preview = ref<string | null>(props.supply.image_url);

console.log('Props reçues:', {
  supply: props.supply,
  categories: props.categories,
  category_id: props.supply.category_id,
  category: props.supply.category
});

const form = useForm({
  name: props.supply.name,
  reference: props.supply.reference,
  packaging: props.supply.packaging,
  catalog_url: props.supply.catalog_url || '',
  category_id: props.supply.category_id,
  image: null as File | null,
  suppliers: props.supply.suppliers.map(f => ({
    id: f.id,
    supplier_reference: f.pivot.supplier_reference,
    unit_price: f.pivot.unit_price,
    catalog_url: f.pivot.catalog_url || '',
  })),
  new_category_name: '',
});

const showNewCategoryForm = ref(false);
const showSuccessMessage = ref(false);
const showCategoryModal = ref(false);
const successMessage = ref('');

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
  if (confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
    router.delete(route('supplies.remove-image', props.supply.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.image = null;
        preview.value = null;
        form.catalog_url = '';
      },
      onError: (errors) => {
        console.error('Erreurs de suppression:', errors);
      }
    });
  }
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Fournitures',
    href: route('supplies.index'),
  },
  {
    title: 'Modifier la fourniture',
    href: route('supplies.edit', props.supply.id),
  },
];

const addSupplier = () => {
  form.suppliers.push({
    id: 0,
    supplier_reference: '',
    unit_price: 0,
    catalog_url: '',
  });
};

const removeSupplier = (index: number) => {
  form.suppliers.splice(index, 1);
};

const deleteCategory = (categoryId: number) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) return;

  const deleteForm = useForm({});
  deleteForm.delete(route('categories.destroy', categoryId), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      successMessage.value = 'La catégorie a été supprimée avec succès !';
      showSuccessMessage.value = true;
      setTimeout(() => {
        showSuccessMessage.value = false;
      }, 3000);
    },
    onError: (errors) => {
      console.error('Erreurs de suppression:', errors);
    }
  });
};

interface CategoryResponse {
  category: {
    id: number;
    name: string;
  };
  message: string;
}

const createCategory = () => {
  if (!form.new_category_name) return;
  
  const categoryForm = useForm({
    name: form.new_category_name
  });

  categoryForm.post(route('categories.store'), {
    preserveState: true,
    preserveScroll: true,
    headers: {
      'Accept': 'application/json'
    },
    onSuccess: (response: any) => {
      // Ajouter la nouvelle catégorie à la liste locale
      const newCategory = {
        id: response.category.id,
        name: response.category.name
      };
      props.categories.push(newCategory);
      
      // Réinitialiser le formulaire et afficher le message de succès
      form.new_category_name = '';
      successMessage.value = response.message;
      showSuccessMessage.value = true;
      setTimeout(() => {
        showSuccessMessage.value = false;
      }, 3000);
    },
    onError: (errors) => {
      console.error('Erreurs de validation:', errors);
    }
  });
};

const submit = () => {
  const data = {
    _method: 'PUT',
    name: form.name,
    reference: form.reference,
    packaging: form.packaging,
    category_id: form.category_id,
    catalog_url: form.catalog_url || '',
    image: form.image,
    suppliers: form.suppliers.map(f => ({
      id: f.id,
      supplier_reference: f.supplier_reference,
      unit_price: f.unit_price,
      catalog_url: f.catalog_url || ''
    }))
  };

  form.transform(data => ({
    ...data,
    suppliers: JSON.stringify(data.suppliers)
  })).post(route('supplies.update', props.supply.id), {
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
      <title>Modifier la fourniture - ISFAC</title>
    </Head>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6" enctype="multipart/form-data">
            <div class="space-y-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900">Informations générales</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Modifiez les informations de la fourniture.
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
                  <div class="flex gap-2">
                    <Button
                      type="button"
                      variant="outline"
                      class="mt-1 flex-1 justify-between"
                      @click="showCategoryModal = true"
                    >
                      <span>{{ categories.find(c => c.id === form.category_id)?.name || 'Sélectionner une catégorie' }}</span>
                      <ChevronDown class="h-4 w-4 ml-2" />
                    </Button>
                    <Dialog v-model:open="showCategoryModal">
                      <DialogContent class="sm:max-w-[425px]">
                        <DialogHeader>
                          <DialogTitle>Gestion des catégories</DialogTitle>
                        </DialogHeader>
                        <div class="space-y-4">
                          <div v-if="showSuccessMessage" class="p-3 bg-green-100 text-green-700 rounded-md">
                            {{ successMessage }}
                          </div>
                          <div class="flex gap-2">
                            <Input
                              v-model="form.new_category_name"
                              type="text"
                              placeholder="Nom de la nouvelle catégorie"
                              class="flex-1"
                            />
                            <Button
                              type="button"
                              @click="createCategory"
                              :disabled="!form.new_category_name"
                            >
                              Ajouter
                            </Button>
                          </div>
                          <div class="space-y-2 max-h-[300px] overflow-y-auto">
                            <div 
                              v-for="category in categories" 
                              :key="category.id" 
                              class="flex items-center justify-between p-2 bg-gray-50 rounded hover:bg-gray-100 cursor-pointer"
                              :class="{ 'bg-indigo-50': category.id === form.category_id }"
                              @click="form.category_id = category.id; showCategoryModal = false"
                            >
                              <span>{{ category.name }}</span>
                              <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                class="text-red-600 hover:text-red-700"
                                @click.stop="deleteCategory(category.id)"
                              >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                              </Button>
                            </div>
                          </div>
                        </div>
                      </DialogContent>
                    </Dialog>
                  </div>
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
                          <span>Télécharger un fichier ou glisser-déposer</span>
                          <input
                            id="image"
                            name="image"
                            type="file"
                            class="sr-only"
                            accept="image/*"
                            @change="handleFileInput"
                          />
                        </label>
                      </div>
                      <p class="text-xs text-gray-500">PNG, JPG jusqu'à 10MB</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Fournisseurs</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Gérez les fournisseurs associés à cette fourniture.
                </p>

                <div class="mt-4 space-y-4">
                  <div
                    v-for="(supplier, index) in form.suppliers"
                    :key="index"
                    class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-end border-b border-gray-200 pb-4"
                  >
                    <div>
                      <Label :for="'supplier-' + index">Fournisseur</Label>
                      <Select
                        :id="'supplier-' + index"
                        v-model="supplier.id"
                        class="mt-1 block w-full"
                        required
                      >
                        <option value="">Sélectionner un fournisseur</option>
                        <option
                          v-for="f in suppliers"
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
                        v-model="supplier.supplier_reference"
                        type="text"
                        class="mt-1 block w-full"
                        required
                      />
                    </div>

                    <div>
                      <Label :for="'catalog_url-' + index">URL du catalogue</Label>
                      <Input
                        :id="'catalog_url-' + index"
                        v-model="supplier.catalog_url"
                        type="url"
                        class="mt-1 block w-full"
                      />
                    </div>

                    <div>
                      <Label :for="'prix-' + index">Prix unitaire</Label>
                      <div class="flex items-center space-x-2">
                        <Input
                          :id="'unit_price-' + index"
                          v-model="supplier.unit_price"
                          type="number"
                          step="0.01"
                          min="0"
                          class="mt-1 block w-full"
                          required
                        />
                        <Button
                          type="button"
                          variant="destructive"
                          @click="removeSupplier(index)"
                        >
                          Supprimer
                        </Button>
                      </div>
                    </div>
                  </div>

                  <Button
                    type="button"
                    variant="outline"
                    @click="addSupplier"
                  >
                    Ajouter un fournisseur
                  </Button>
                </div>
              </div>

              <div class="flex justify-end space-x-3">
                <Button
                  @click="route('supplies.index')"
                  variant="outline"
                >
                  Annuler
                </Button>
                <Button type="submit" :disabled="form.processing">
                  Mettre à jour
                </Button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template> 