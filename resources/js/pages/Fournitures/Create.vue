<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

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

const form = useForm({
  name: '',
  reference: '',
  packaging: '',
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
  form.post(route('fournitures.store'));
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
          <form @submit.prevent="submit" class="p-6">
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