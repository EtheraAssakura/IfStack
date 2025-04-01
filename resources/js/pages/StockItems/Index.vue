<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronDown, Search } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

interface StockItem {
    id: number;
    estimated_quantity: number;
    local_alert_threshold: number;
    supply: {
        id: number;
        name: string;
        reference: string;
    };
    location: {
        id: number;
        name: string;
        site: {
            id: number;
            name: string;
        };
    };
}

interface Props {
    stocks: Record<string, StockItem[]>;
    site: {
        id: number;
        name: string;
        slug: string;
    };
}

const props = defineProps<Props>();
const searchQuery = ref('');
const sortBy = ref('');
const sortDirection = ref<'asc' | 'desc'>('asc');
const selectedLocation = ref('');
const selectedSupply = ref('');
const selectedReference = ref('');
const selectedStatus = ref('');
const itemsPerPage = ref(10);
const currentPage = ref(1);
const filtersManuallyCleared = ref(false);

const locations = computed(() => {
    const uniqueLocations = new Set<string>();
    Object.values(props.stocks).forEach(stocks => {
        stocks.forEach(stock => {
            uniqueLocations.add(stock.location.name);
        });
    });
    return Array.from(uniqueLocations).sort();
});

const supplies = computed(() => {
    const uniqueSupplies = new Set<string>();
    Object.values(props.stocks).forEach(stocks => {
        stocks.forEach(stock => {
            if (!selectedLocation.value || stock.location.name === selectedLocation.value) {
                uniqueSupplies.add(stock.supply.name);
            }
        });
    });
    return Array.from(uniqueSupplies).sort();
});

const references = computed(() => {
    const uniqueReferences = new Set<string>();
    Object.values(props.stocks).forEach(stocks => {
        stocks.forEach(stock => {
            if ((!selectedLocation.value || stock.location.name === selectedLocation.value) &&
                (!selectedSupply.value || stock.supply.name === selectedSupply.value)) {
                uniqueReferences.add(stock.supply.reference);
            }
        });
    });
    return Array.from(uniqueReferences).sort();
});

const filteredStocks = computed(() => {
    const filtered: Record<string, StockItem[]> = {};

    Object.entries(props.stocks).forEach(([etablissement, stocks]) => {
        let filteredStocks = stocks.filter(stock => {
            // Filtre par recherche globale
            const matchesSearch = !searchQuery.value || 
                stock.supply.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                stock.supply.reference.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                stock.location.name.toLowerCase().includes(searchQuery.value.toLowerCase());

            // Filtre par emplacement
            const matchesLocation = !selectedLocation.value || 
                stock.location.name === selectedLocation.value;

            // Filtre par fourniture
            const matchesSupply = !selectedSupply.value || 
                stock.supply.name === selectedSupply.value;

            // Filtre par référence
            const matchesReference = !selectedReference.value || 
                stock.supply.reference === selectedReference.value;

            // Filtre par statut
            const matchesStatus = !selectedStatus.value || 
                (selectedStatus.value === 'En alerte' && stock.estimated_quantity <= stock.local_alert_threshold) ||
                (selectedStatus.value === 'Normal' && stock.estimated_quantity > stock.local_alert_threshold);

            return matchesSearch && matchesLocation && matchesSupply && matchesReference && matchesStatus;
        });

        if (sortBy.value) {
            filteredStocks.sort((a, b) => {
                let valueA, valueB;
                switch (sortBy.value) {
                    case 'emplacement':
                        valueA = a.location.name;
                        valueB = b.location.name;
                        break;
                    case 'fourniture':
                        valueA = a.supply.name;
                        valueB = b.supply.name;
                        break;
                    case 'reference':
                        valueA = a.supply.reference;
                        valueB = b.supply.reference;
                        break;
                    case 'status':
                        valueA = a.estimated_quantity <= a.local_alert_threshold ? 'En alerte' : 'Normal';
                        valueB = b.estimated_quantity <= b.local_alert_threshold ? 'En alerte' : 'Normal';
                        break;
                    default:
                        return 0;
                }
                const comparison = valueA.localeCompare(valueB);
                return sortDirection.value === 'asc' ? comparison : -comparison;
            });
        }

        if (filteredStocks.length > 0) {
            filtered[etablissement] = filteredStocks;
        }
    });

    return filtered;
});

const paginatedStocks = computed(() => {
    const filtered: Record<string, StockItem[]> = {};
    
    Object.entries(filteredStocks.value).forEach(([etablissement, stocks]) => {
        const start = (currentPage.value - 1) * itemsPerPage.value;
        const end = start + itemsPerPage.value;
        filtered[etablissement] = stocks.slice(start, end);
    });
    
    return filtered;
});

const totalPages = computed(() => {
    const totalItems = Object.values(filteredStocks.value).reduce((acc, stocks) => acc + stocks.length, 0);
    return Math.ceil(totalItems / itemsPerPage.value);
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
    selectedLocation.value = '';
    selectedSupply.value = '';
    selectedReference.value = '';
    selectedStatus.value = '';
    sortBy.value = '';
    sortDirection.value = 'asc';
    currentPage.value = 1;
    filtersManuallyCleared.value = true;
};

onMounted(() => {
    console.log('Données reçues:', {
        site: props.site,
        stocksCount: Object.keys(props.stocks).length,
        stocks: props.stocks,
        stocksKeys: Object.keys(props.stocks),
        firstStock: Object.values(props.stocks)[0]?.[0]
    });
});

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Stocks',
        href: '/stocks/sites',
    },
    {
        title: props.site.name.charAt(0).toUpperCase() + props.site.name.slice(1),
        href: `/stocks?site=${props.site.slug}`,
    },
];

const getBadgeVariant = (stock: StockItem) => {
    return stock.estimated_quantity <= stock.local_alert_threshold ? 'destructive' : 'default';
};

const getBadgeText = (stock: StockItem) => {
    return stock.estimated_quantity <= stock.local_alert_threshold ? 'En alerte' : 'Normal';
};

const getStockValue = (stock: StockItem, path: string) => {
    const keys = path.split('.');
    let value: any = stock;
    
    for (const key of keys) {
        if (value && typeof value === 'object') {
            value = value[key as keyof typeof value];
        } else {
            return '-';
        }
    }
    
    return value ?? '-';
};
console.log(props.site);
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Stocks - {{ site.name.charAt(0).toUpperCase() + site.name.slice(1) }} - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Stocks - {{ site.name.charAt(0).toUpperCase() + site.name.slice(1) }}</h1>
                        <p class="text-muted-foreground">
                            Gestion des stocks par établissement
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <Button variant="outline" as-child>
                            <Link :href="route('stock-items.sites')">
                                Changer de site
                            </Link>
                        </Button>
                        <Button as-child>
                            <Link :href="route('stock-items.create', { site: site.slug })">
                                Gérer les stocks
                            </Link>
                        </Button>
                    </div>
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
                                placeholder="Rechercher par nom de fourniture, emplacement ou référence..."
                            />
                        </div>
                        <div v-if="selectedLocation || selectedSupply || selectedReference || selectedStatus" class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm text-gray-500">Filtres actifs :</span>
                            <Badge v-if="selectedLocation" variant="secondary" class="flex items-center gap-1">
                                Emplacement: {{ selectedLocation }}
                                <button @click="selectedLocation = ''" class="ml-1 hover:text-gray-700">×</button>
                            </Badge>
                            <Badge v-if="selectedSupply" variant="secondary" class="flex items-center gap-1">
                                Fourniture: {{ selectedSupply }}
                                <button @click="selectedSupply = ''" class="ml-1 hover:text-gray-700">×</button>
                            </Badge>
                            <Badge v-if="selectedReference" variant="secondary" class="flex items-center gap-1">
                                Référence: {{ selectedReference }}
                                <button @click="selectedReference = ''" class="ml-1 hover:text-gray-700">×</button>
                            </Badge>
                            <Badge v-if="selectedStatus" variant="secondary" class="flex items-center gap-1">
                                Statut: {{ selectedStatus }}
                                <button @click="selectedStatus = ''" class="ml-1 hover:text-gray-700">×</button>
                            </Badge>
                            <Button variant="ghost" size="sm" @click="clearFilters" class="ml-2">
                                Réinitialiser tous les filtres
                            </Button>
                        </div>
                    </div>
                </div>

                <div v-if="Object.keys(filteredStocks).length === 0" class="text-center py-12">
                    <p class="text-gray-500">Aucun stock trouvé pour ce site.</p>
                    <p class="text-sm text-gray-400 mt-2">Site sélectionné : {{ site.name }}</p>
                </div>

                <div v-else class="space-y-6">
                    <div v-for="(stocks, etablissement) in paginatedStocks" :key="etablissement" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50/50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger class="flex items-center gap-1 hover:text-gray-700">
                                                        Emplacement
                                                        <ChevronDown class="h-4 w-4" />
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent>
                                                        <DropdownMenuItem @click="sortBy = 'emplacement'; sortDirection = 'asc'">
                                                            Trier par emplacement (A-Z)
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem @click="sortBy = 'emplacement'; sortDirection = 'desc'">
                                                            Trier par emplacement (Z-A)
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem v-for="location in locations" :key="location" @click="selectedLocation = location">
                                                            {{ location }}
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger class="flex items-center gap-1 hover:text-gray-700">
                                                        Fourniture
                                                        <ChevronDown class="h-4 w-4" />
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent>
                                                        <DropdownMenuItem @click="sortBy = 'fourniture'; sortDirection = 'asc'">
                                                            Trier par fourniture (A-Z)
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem @click="sortBy = 'fourniture'; sortDirection = 'desc'">
                                                            Trier par fourniture (Z-A)
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem v-for="supply in supplies" :key="supply" @click="selectedSupply = supply">
                                                            {{ supply }}
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
                                            </th>
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
                                                        <DropdownMenuItem v-for="reference in references" :key="reference" @click="selectedReference = reference">
                                                            {{ reference }}
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Quantité
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Seuil local
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger class="flex items-center gap-1 hover:text-gray-700">
                                                        Statut
                                                        <ChevronDown class="h-4 w-4" />
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent>
                                                        <DropdownMenuItem @click="selectedStatus = 'En alerte'">
                                                            En alerte
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem @click="selectedStatus = 'Normal'">
                                                            Normal
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr v-for="stock in stocks" :key="stock.id" class="group transition-colors hover:bg-gray-50/50">
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ getStockValue(stock, 'location.name') }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ getStockValue(stock, 'supply.name') }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ getStockValue(stock, 'supply.reference') }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                                <div class="text-sm text-gray-900">{{ stock.estimated_quantity ?? '-' }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                                <div class="text-sm text-gray-900">{{ stock.local_alert_threshold ?? '-' }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <Badge :variant="getBadgeVariant(stock)">
                                                    {{ getBadgeText(stock) }}
                                                </Badge>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                                <div class="flex justify-end gap-2">
                                                    <Link
                                                        :href="route('stock-items.show', stock.id)"
                                                        class="inline-flex items-center gap-2 text-gray-500 transition-colors hover:text-primary"
                                                    >
                                                        Détails
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
        </div>
    </AppSidebarLayout>
</template> 