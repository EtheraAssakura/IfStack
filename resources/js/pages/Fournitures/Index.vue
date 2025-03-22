<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ChevronDown, MoreVertical, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
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
      id: number;
      name: string;
      catalog_url: string | null;
      pivot: {
        supplier_reference: string;
        unit_price: number;
        catalog_url: string | null;
      };
    }>;
  }>;
}

const props = defineProps<Props>();

// Ajout du console.log pour déboguer
console.log('Fournitures reçues:', props.fournitures.map(f => ({
  id: f.id,
  name: f.name,
  fournisseurs: f.fournisseurs
})));

const getFournisseurUrl = (fournisseur: Props['fournitures'][0]['fournisseurs'][0]) => {
  return (fournisseur.pivot?.catalog_url || fournisseur.catalog_url) ?? '#';
};

const hasFournisseurUrl = (fournisseur: Props['fournitures'][0]['fournisseurs'][0]) => {
  return Boolean(fournisseur.pivot?.catalog_url || fournisseur.catalog_url);
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Fournitures',
    href: route('fournitures.index'),
  },
];

const deleteFourniture = (id: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette fourniture ?')) {
        router.delete(route('fournitures.destroy', id));
    }
};

const searchQuery = ref('');
const selectedCategory = ref('');
const selectedFournisseur = ref('');
const sortBy = ref('');
const sortDirection = ref<'asc' | 'desc'>('asc');
const itemsPerPage = ref(10);
const currentPage = ref(1);

const categories = computed(() => {
  const uniqueCategories = new Set(props.fournitures.map(f => f.category.name));
  return Array.from(uniqueCategories).sort();
});

const fournisseurs = computed(() => {
  const uniqueFournisseurs = new Set<string>();
  props.fournitures.forEach(fourniture => {
    fourniture.fournisseurs.forEach(fournisseur => {
      uniqueFournisseurs.add(fournisseur.name);
    });
  });
  return Array.from(uniqueFournisseurs).sort();
});

const filteredFournitures = computed(() => {
  let filtered = props.fournitures;
  
  if (searchQuery.value) {
    filtered = filtered.filter(fourniture => 
      fourniture.reference.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      fourniture.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }
  
  if (selectedCategory.value) {
    filtered = filtered.filter(fourniture => 
      fourniture.category.name === selectedCategory.value
    );
  }

  if (selectedFournisseur.value) {
    filtered = filtered.filter(fourniture =>
      fourniture.fournisseurs.some(f => f.name === selectedFournisseur.value)
    );
  }

  if (sortBy.value) {
    filtered.sort((a, b) => {
      let valueA, valueB;
      switch (sortBy.value) {
        case 'reference':
          valueA = a.reference;
          valueB = b.reference;
          break;
        case 'name':
          valueA = a.name;
          valueB = b.name;
          break;
        case 'category':
          valueA = a.category.name;
          valueB = b.category.name;
          break;
        case 'packaging':
          valueA = a.packaging;
          valueB = b.packaging;
          break;
        default:
          return 0;
      }
      const comparison = valueA.localeCompare(valueB);
      return sortDirection.value === 'asc' ? comparison : -comparison;
    });
  }
  
  return filtered;
});

const paginatedFournitures = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredFournitures.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredFournitures.value.length / itemsPerPage.value);
});

const changePage = (page: number) => {
  currentPage.value = page;
};

const changeItemsPerPage = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  if (target) {
    itemsPerPage.value = Number(target.value);
    currentPage.value = 1;
  }
};

const clearFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = '';
  selectedFournisseur.value = '';
  sortBy.value = '';
  sortDirection.value = 'asc';
};

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
          <div class="flex flex-col gap-4">
            <div class="relative flex-1">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Search class="h-5 w-5 text-gray-400" />
              </div>
              <input
                v-model="searchQuery"
                type="text"
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Rechercher par référence ou nom..."
              />
            </div>
            <div v-if="searchQuery || selectedCategory || selectedFournisseur" class="flex items-center gap-2 flex-wrap">
              <span class="text-sm text-gray-500">Filtres actifs :</span>
              <Badge v-if="searchQuery" variant="secondary" class="flex items-center gap-1">
                Recherche: {{ searchQuery }}
                <button @click="searchQuery = ''" class="ml-1 hover:text-gray-700">×</button>
              </Badge>
              <Badge v-if="selectedCategory" variant="secondary" class="flex items-center gap-1">
                Catégorie: {{ selectedCategory }}
                <button @click="selectedCategory = ''" class="ml-1 hover:text-gray-700">×</button>
              </Badge>
              <Badge v-if="selectedFournisseur" variant="secondary" class="flex items-center gap-1">
                Fournisseur: {{ selectedFournisseur }}
                <button @click="selectedFournisseur = ''" class="ml-1 hover:text-gray-700">×</button>
              </Badge>
              <Button variant="ghost" size="sm" @click="clearFilters" class="ml-2">
                Réinitialiser tous les filtres
              </Button>
            </div>
          </div>
        </div>

        <div v-if="!filteredFournitures || filteredFournitures.length === 0" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <p class="text-gray-500 text-center">Aucune fourniture trouvée</p>
        </div>

        <div v-else class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200/50">
          <div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50/50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <DropdownMenu>
                      <DropdownMenuTrigger class="flex items-center gap-1 hover:text-gray-700">
                        Référence
                        <ChevronDown class="h-4 w-4" />
                      </DropdownMenuTrigger>
                      <DropdownMenuContent>
                        <DropdownMenuItem @click="sortBy = 'reference'; sortDirection = 'asc'">
                          Trier par référence (A-Z)
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="sortBy = 'reference'; sortDirection = 'desc'">
                          Trier par référence (Z-A)
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <DropdownMenu>
                      <DropdownMenuTrigger class="flex items-center gap-1 hover:text-gray-700">
                        Nom
                        <ChevronDown class="h-4 w-4" />
                      </DropdownMenuTrigger>
                      <DropdownMenuContent>
                        <DropdownMenuItem @click="sortBy = 'name'; sortDirection = 'asc'">
                          Trier par nom (A-Z)
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="sortBy = 'name'; sortDirection = 'desc'">
                          Trier par nom (Z-A)
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <DropdownMenu>
                      <DropdownMenuTrigger class="flex items-center gap-1 hover:text-gray-700">
                        Catégorie
                        <ChevronDown class="h-4 w-4" />
                      </DropdownMenuTrigger>
                      <DropdownMenuContent>
                        <DropdownMenuItem @click="sortBy = 'category'; sortDirection = 'asc'">
                          Trier par catégorie (A-Z)
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="sortBy = 'category'; sortDirection = 'desc'">
                          Trier par catégorie (Z-A)
                        </DropdownMenuItem>
                        <DropdownMenuItem v-for="category in categories" :key="category" @click="selectedCategory = category">
                          {{ category }}
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <DropdownMenu>
                      <DropdownMenuTrigger class="flex items-center gap-1 hover:text-gray-700">
                        Conditionnement
                        <ChevronDown class="h-4 w-4" />
                      </DropdownMenuTrigger>
                      <DropdownMenuContent>
                        <DropdownMenuItem @click="sortBy = 'packaging'; sortDirection = 'asc'">
                          Trier par conditionnement (A-Z)
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="sortBy = 'packaging'; sortDirection = 'desc'">
                          Trier par conditionnement (Z-A)
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <DropdownMenu>
                      <DropdownMenuTrigger class="flex items-center gap-1 hover:text-gray-700">
                        Fournisseurs
                        <ChevronDown class="h-4 w-4" />
                      </DropdownMenuTrigger>
                      <DropdownMenuContent>
                        <DropdownMenuItem v-for="fournisseur in fournisseurs" :key="fournisseur" @click="selectedFournisseur = fournisseur">
                          {{ fournisseur }}
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 text-right">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="fourniture in paginatedFournitures" :key="fourniture.id" class="group transition-colors hover:bg-gray-50/50">
                  <td class="whitespace-nowrap px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ fourniture.reference }}</div>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    <div class="text-sm text-gray-900">{{ fourniture.name }}</div>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    <div class="text-sm text-gray-900">{{ fourniture.category.name }}</div>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    <div class="text-sm text-gray-900">{{ fourniture.packaging }}</div>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    <template v-if="fourniture.fournisseurs?.length">
                      <div v-for="fournisseur in fourniture.fournisseurs" :key="fournisseur.id" class="mb-1">
                        <a v-if="hasFournisseurUrl(fournisseur)" 
                           :href="getFournisseurUrl(fournisseur)" 
                           target="_blank" 
                           class="text-sm text-indigo-600 hover:text-indigo-900">
                          {{ fournisseur.name }}
                        </a>
                        <span v-else class="text-sm text-gray-500">{{ fournisseur.name }}</span>
                      </div>
                    </template>
                    <div v-else class="text-sm text-gray-500">
                      Aucun fournisseur
                    </div>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                    <div class="flex justify-end gap-2">
                      <Link
                        :href="route('fournitures.show', fourniture.id)"
                        class="inline-flex items-center gap-2 text-gray-500 transition-colors hover:text-primary"
                      >
                        Détails
                      </Link>
                      <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                          <Button variant="ghost" size="icon" class="h-8 w-8">
                            <MoreVertical class="h-4 w-4" />
                          </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                          <DropdownMenuItem asChild>
                            <Link :href="route('fournitures.edit', fourniture.id)" class="flex items-center gap-2">
                              <Pencil class="h-4 w-4" />
                              Modifier
                            </Link>
                          </DropdownMenuItem>
                          <DropdownMenuItem @click="deleteFourniture(fourniture.id)" class="text-red-500 focus:text-red-500">
                            <Trash2 class="h-4 w-4" />
                            Supprimer
                          </DropdownMenuItem>
                        </DropdownMenuContent>
                      </DropdownMenu>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="mt-4 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="text-sm text-gray-700">Afficher</span>
                <select
                  v-model="itemsPerPage"
                  @change="changeItemsPerPage"
                  class="block w-20 px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                  <option :value="50">50</option>
                </select>
                <span class="text-sm text-gray-700">par page</span>
              </div>

              <div class="flex items-center gap-2">
                <button
                  @click="changePage(currentPage - 1)"
                  :disabled="currentPage === 1"
                  class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Précédent
                </button>
                <span class="text-sm text-gray-700">
                  Page {{ currentPage }} sur {{ totalPages }}
                </span>
                <button
                  @click="changePage(currentPage + 1)"
                  :disabled="currentPage === totalPages"
                  class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Suivant
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template> 