<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

interface Props {
  fourniture: {
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
    fournisseurs: Array<{
      id: number;
      name: string;
      pivot: {
        supplier_reference: string;
        unit_price: number;
        catalog_url?: string | null;
      };
    }>;
  };
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

console.log('Props reçues:', {
  fourniture: props.fourniture,
  categories: props.categories,
  category_id: props.fourniture.category_id,
  category: props.fourniture.category
});

const form = useForm({
  name: props.fourniture.name,
  reference: props.fourniture.reference,
  packaging: props.fourniture.packaging,
  catalog_url: props.fourniture.catalog_url || '',
  category_id: props.fourniture.category_id,
  image: null as File | null,
  fournisseurs: props.fourniture.fournisseurs.map(f => {
    return {
      id: f.id,
      reference: f.pivot.supplier_reference,
      prix: f.pivot.unit_price,
      catalog_url: f.pivot.catalog_url || '',
    };
  }),
});


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Fournitures',
    href: route('fournitures.index'),
  },
  {
    title: 'Modifier la fourniture',
    href: route('fournitures.edit', props.fourniture.id),
  },
];

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
  form.put(route('fournitures.update', props.fourniture.id));
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
          <form @submit.prevent="submit" class="p-6">
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
                      :selected="category.id === form.category_id"
                    >
                      {{ category.name }}
                    </option>
                  </Select>
                </div>

                

                <div>
                  <Label for="image">Image</Label>
                  <div v-if="fourniture.image_url" class="mb-2">
                    <img :src="fourniture.image_url" alt="Image actuelle" class="h-20 w-20 object-cover rounded" />
                  </div>
                  <Input
                    id="image"
                    type="file"
                    class="mt-1 block w-full"
                    @input="form.image = $event.target.files[0]"
                  />
                </div>
              </div>

              <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Fournisseurs</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Gérez les fournisseurs associés à cette fourniture.
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
                  @click="route('fournitures.index')"
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