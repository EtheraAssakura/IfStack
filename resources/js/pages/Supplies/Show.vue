<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
  fourniture: {
    id: number;
    name: string;
    reference: string;
    packaging: string;
    image_url: string | null;
    category: {
      id: number;
      name: string;
    } | null;
    fournisseurs: Array<{
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
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Fournitures',
    href: route('fournitures.index'),
  },
  {
    title: props.fourniture.name,
    href: route('fournitures.show', props.fourniture.id),
  },
];
</script>

<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <Head>
      <title>{{ fourniture.name }} - ISFAC</title>
    </Head>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-medium text-gray-900">Informations générales</h2>
            <div class="flex items-center gap-4">
              <Button variant="outline" as-child>
              <Link :href="route('fournitures.index')">
                  Retour
              </Link>
            </Button>
            <Button>
            <Link :href="route('fournitures.edit', fourniture.id)" class="flex items-center">
                Modifier
              </Link>
              </Button>
            <Button variant="destructive">
              <Link :href="route('fournitures.destroy', fourniture.id)" method="delete" as="button" class="flex items-center">
                  Supprimer
                </Link>
                </Button>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Informations générales -->
            <div class="col-span-2">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Image -->
                <div v-if="fourniture.image_url" class="sm:col-span-1">
                  <img :src="fourniture.image_url" :alt="fourniture.name" class="w-full h-48 object-contain rounded-lg shadow-md" />
                </div>
                <!-- Informations -->
                <div class="sm:col-span-1 grid grid-cols-1 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Nom</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ fourniture.name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Référence ISFAC</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ fourniture.reference }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Conditionnement</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ fourniture.packaging }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ fourniture?.category?.name || 'Non catégorisé' }}</dd>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fournisseurs -->
            <div class="col-span-2">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Fournisseurs</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catalogue</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="fournisseur in fourniture.fournisseurs" :key="fournisseur.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ fournisseur.name }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ fournisseur.pivot.supplier_reference }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ fournisseur.pivot.unit_price }} €</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <a v-if="fournisseur.pivot.catalog_url || fournisseur.catalog_url" 
                           :href="(fournisseur.pivot.catalog_url || fournisseur.catalog_url) ?? '#'" 
                           target="_blank" 
                           class="text-sm text-indigo-600 hover:text-indigo-900">
                          Voir
                        </a>
                        <span v-else class="text-sm text-gray-500">-</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template> 