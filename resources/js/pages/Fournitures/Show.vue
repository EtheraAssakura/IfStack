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
    catalog_url: string | null;
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
      };
    }>;
    stocks: Array<{
      id: number;
      estimated_quantity: number;
      local_alert_threshold: number;
      location: {
        id: number;
        name: string;
        site: {
          id: number;
          name: string;
        };
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
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div v-if="fourniture" class="mb-6 flex justify-between items-center">
          <h2 class="text-2xl font-semibold text-gray-900">Détails de la fourniture</h2>
          <div class="flex space-x-3">
            <Link :href="route('fournitures.edit', fourniture.id)">
              <Button>Modifier</Button>
            </Link>
          </div>
        </div>

        <div class="bg-white shadow-xl sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Informations générales -->
              <div class="col-span-2">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                  <div v-if="fourniture.catalog_url">
                    <dt class="text-sm font-medium text-gray-500">URL du catalogue</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <a :href="fourniture.catalog_url" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                        Voir dans le catalogue
                      </a>
                    </dd>
                  </div>
                </div>
              </div>

              <!-- Image -->
              <div v-if="fourniture.image_url" class="col-span-2 sm:col-span-1">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Image</h3>
                <img :src="fourniture.image_url" :alt="fourniture.name" class="max-w-sm rounded-lg shadow-md" />
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
                          <a v-if="fournisseur.catalog_url" :href="fournisseur.catalog_url" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                            Voir
                          </a>
                          <span v-else>-</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- État des stocks -->
              <div class="col-span-2">
                <h3 class="text-lg font-medium text-gray-900 mb-4">État des stocks</h3>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emplacement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité estimée</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seuil d'alerte local</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="stock in fourniture.stocks" :key="stock.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ stock.location?.site?.name || '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ stock.location?.name || '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ stock.estimated_quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ stock.local_alert_threshold || '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="{
                              'bg-red-100 text-red-800': stock.estimated_quantity <= stock.local_alert_threshold,
                              'bg-green-100 text-green-800': stock.estimated_quantity > stock.local_alert_threshold,
                            }"
                          >
                            {{ stock.estimated_quantity <= stock.local_alert_threshold ? 'En alerte' : 'Normal' }}
                          </span>
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
    </div>
  </AppSidebarLayout>
</template> 