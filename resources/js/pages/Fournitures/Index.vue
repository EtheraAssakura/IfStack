<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
  fournitures: Array<{
    id: number;
    name: string;
    reference: string;
    packaging: string;
    category: {
      id: number;
      name: string;
    };
    fournisseurs: Array<{
      name: string;
      catalog_url: string;
      pivot: {
        supplier_reference: string;
        unit_price: number;
        catalog_url?: string;
      };
    }>;
  }>;
}


const props = defineProps<Props>();


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Fournitures',
    href: route('fournitures.index'),
  },
];

const searchQuery = ref('');

const filteredFournitures = computed(() => {
  if (!searchQuery.value) return props.fournitures;
  
  return props.fournitures.filter(fourniture => 
    fourniture.reference.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

</script>

<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <Head>
      <title>Fournitures - ISFAC</title>
    </Head>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold text-gray-900">Liste des fournitures</h2>
          <Link :href="route('fournitures.create')">
            <Button>
              <Plus class="mr-2 h-4 w-4" />
              Nouvelle fourniture
            </Button>
          </Link>
        </div>

        <div class="mb-6">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Search class="h-5 w-5 text-gray-400" />
            </div>
            <input
              v-model="searchQuery"
              type="text"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="Rechercher par référence..."
            />
          </div>
        </div>

        <div v-if="!filteredFournitures || filteredFournitures.length === 0" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <p class="text-gray-500 text-center">Aucune fourniture trouvée</p>
        </div>

        <div v-else class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conditionnement</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseurs</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="fourniture in filteredFournitures" :key="fourniture.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ fourniture.reference }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ fourniture.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ fourniture.category.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ fourniture.packaging }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div v-for="fournisseur in fourniture.fournisseurs" :key="fournisseur.name" class="mb-1">
                      <a v-if="fournisseur.pivot.catalog_url || fournisseur.catalog_url" 
                         :href="fournisseur.pivot.catalog_url || fournisseur.catalog_url" 
                         target="_blank" 
                         class="text-indigo-600 hover:text-indigo-900">
                        {{ fournisseur.name }}
                      </a>
                      <span v-else class="text-gray-500">{{ fournisseur.name }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex space-x-2">
                      <Link :href="route('fournitures.edit', fourniture.id)" class="text-indigo-600 hover:text-indigo-900">
                        Modifier
                      </Link>
                      <Link :href="route('fournitures.show', fourniture.id)" class="text-indigo-600 hover:text-indigo-900">
                        Voir
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template> 