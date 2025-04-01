<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue';

interface Location {
    id: number;
    name: string;
    site: {
        id: number;
        name: string;
        slug: string;
    };
    stocks?: Array<{
        supply_id: number;
        estimated_quantity: number;
        local_alert_threshold: number;
    }>;
}

interface Supply {
    id: number;
    name: string;
    reference: string;
}

interface Props {
    locations: Location[];
    supplies: Supply[];
    site: string;
    site_name: string;
}

interface FormErrors {
    location_id?: string;
    stocks?: {
        [key: number]: {
            supply_id?: string;
            estimated_quantity?: string;
            local_alert_threshold?: string;
        };
    };
}

interface StockForm {
    supply_id: string;
    estimated_quantity: number;
    local_alert_threshold: number;
}

const props = defineProps<Props>();

const form = useForm<{
    location_id: string;
    stocks: Array<{
        supply_id: string;
        estimated_quantity: number;
        local_alert_threshold: number;
    }>;
}>({
    location_id: '',
    stocks: [
        {
            supply_id: '',
            estimated_quantity: 0,
            local_alert_threshold: 0
        }
    ]
});

// Computed property pour filtrer les fournitures disponibles
const getAvailableFournitures = computed(() => {
    // Récupérer les IDs des fournitures déjà sélectionnées
    const selectedIds = form.stocks.map(stock => stock.supply_id).filter(id => id !== '');
    
    // Retourner les fournitures qui ne sont pas déjà sélectionnées, triées par nom
    return props.supplies
        .filter(supply => !selectedIds.includes(supply.id.toString()))
        .sort((a, b) => a.name.localeCompare(b.name));
});

// Computed property pour filtrer les fournitures disponibles pour une ligne spécifique
const getAvailableFournituresForLine = (currentSupplyId: string) => {
    // Récupérer les IDs des fournitures déjà sélectionnées, sauf celle de la ligne courante
    const selectedIds = form.stocks
        .map(stock => stock.supply_id)
        .filter(id => id !== '' && id !== currentSupplyId);
    
    // Retourner les fournitures qui ne sont pas déjà sélectionnées ou qui sont la fourniture courante, triées par nom
    return props.supplies
        .filter(supply => 
            !selectedIds.includes(supply.id.toString()) || 
            supply.id.toString() === currentSupplyId
        )
        .sort((a, b) => a.name.localeCompare(b.name));
};

const getFormError = (path: string): string | undefined => {
    return form.errors[path as keyof FormErrors] as string | undefined;
};

const addStock = () => {
    form.stocks.push({
        supply_id: '',
        estimated_quantity: 0,
        local_alert_threshold: 0
    });
};

const removeStock = (index: number) => {
    form.stocks.splice(index, 1);
};

const loadExistingStocks = () => {
    if (!form.location_id) {
        form.stocks = [{
            supply_id: '',
            estimated_quantity: 0,
            local_alert_threshold: 0
        }];
        return;
    }

    const selectedLocation = props.locations.find(l => l.id.toString() === form.location_id);
    if (!selectedLocation) return;

    // Réinitialiser les stocks
    form.stocks = [];

    // Si l'emplacement a des stocks existants, les ajouter
    if (selectedLocation.stocks && selectedLocation.stocks.length > 0) {
        // Filtrer les doublons potentiels par fourniture_id
        const uniqueStocks = selectedLocation.stocks.reduce<StockForm[]>((acc, stock) => {
            if (!acc.some(s => s.supply_id === stock.supply_id.toString())) {
                acc.push({
                    supply_id: stock.supply_id.toString(),
                    estimated_quantity: stock.estimated_quantity,
                    local_alert_threshold: stock.local_alert_threshold
                });
            }
            return acc;
        }, []);

        form.stocks = uniqueStocks;
    }

    // Ajouter une ligne vide uniquement s'il n'y a pas de stocks
    if (form.stocks.length === 0) {
        form.stocks.push({
            supply_id: '',
            estimated_quantity: 0,
            local_alert_threshold: 0
        });
    }
};

const submit = () => {
    // Filtrer les lignes vides et les doublons
    const nonEmptyStocks = form.stocks.filter(stock => stock.supply_id !== '');
    
    // Supprimer les doublons en gardant la dernière occurrence
    const uniqueStocks = nonEmptyStocks.reduce<StockForm[]>((acc, stock) => {
        const index = acc.findIndex(s => s.supply_id === stock.supply_id);
        if (index !== -1) {
            acc[index] = stock; // Remplacer par la dernière version
        } else {
            acc.push(stock);
        }
        return acc;
    }, []);

    form.stocks = uniqueStocks;
    form.post(route('stock-items.store'), {
        onSuccess: () => {
            const selectedLocation = props.locations.find(l => l.id.toString() === form.location_id);
            if (selectedLocation) {
                router.visit(route('stock-items.by-site', {
                    site: selectedLocation.site.slug
                }));
            }
        }
    });
};

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Stocks',
        href: '/stocks/sites',
    },
    {
        title: props.site_name ? props.site_name : 'Tous les sites',
        href: props.site ? `/stocks?site=${props.site}` : '/stocks',
    },
    {
        title: 'Ajouter des stocks',
        href: '/stocks/create',
    },
];
</script>

<template>
    <Head>
        <title>Ajouter des stocks - {{ site_name ? site_name : 'Tous les sites' }} - ISFAC</title>
    </Head>

    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Informations générales</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ site_name ? `Ajout de stocks pour le site ${site_name}` : 'Ajout de stocks pour tous les sites' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <Label for="location_id">Emplacement</Label>
                                    <select
                                        id="location_id"
                                        v-model="form.location_id"
                                        @change="loadExistingStocks"
                                        class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        required
                                    >
                                        <option value="">Sélectionnez un emplacement</option>
                                        <option
                                            v-for="location in locations"
                                            :key="location.id"
                                            :value="location.id.toString()"
                                        >
                                            {{ location.name }} - {{ location.site.name }}
                                        </option>
                                    </select>
                                    <div v-if="getFormError('location_id')" class="text-sm text-red-500 mt-1">
                                        {{ getFormError('location_id') }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900">Fournitures</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Ajoutez les fournitures à stocker dans cet emplacement.
                                </p>

                                <div class="mt-4 space-y-4">
                                    <div
                                        v-for="(stock, index) in form.stocks"
                                        :key="index"
                                        class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end border-b border-gray-200 pb-4"
                                    >
                                        <div class="sm:col-span-2">
                                            <Label :for="'supply_id_' + index">Fourniture</Label>
                                            <select
                                                :id="'supply_id_' + index"
                                                v-model="stock.supply_id"
                                                class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                required
                                            >
                                                <option value="">Sélectionnez une fourniture</option>
                                                <option
                                                    v-for="supply in getAvailableFournituresForLine(stock.supply_id)"
                                                    :key="supply.id"
                                                    :value="supply.id.toString()"
                                                >
                                                    {{ supply.name }} ({{ supply.reference }})
                                                </option>
                                            </select>
                                            <div v-if="getFormError(`stocks.${index}.supply_id`)" class="text-sm text-red-500 mt-1">
                                                {{ getFormError(`stocks.${index}.supply_id`) }}
                                            </div>
                                        </div>

                                        <div>
                                            <Label :for="'estimated_quantity_' + index">Quantité estimée</Label>
                                            <Input
                                                :id="'estimated_quantity_' + index"
                                                type="number"
                                                v-model="stock.estimated_quantity"
                                                min="0"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                            <div v-if="getFormError(`stocks.${index}.estimated_quantity`)" class="text-sm text-red-500 mt-1">
                                                {{ getFormError(`stocks.${index}.estimated_quantity`) }}
                                            </div>
                                        </div>

                                        <div>
                                            <Label :for="'local_alert_threshold_' + index">Seuil d'alerte local</Label>
                                            <div class="flex items-center space-x-2">
                                                <Input
                                                    :id="'local_alert_threshold_' + index"
                                                    type="number"
                                                    v-model="stock.local_alert_threshold"
                                                    min="0"
                                                    class="mt-1 block w-full"
                                                    required
                                                />
                                                <Button
                                                    v-if="form.stocks.length > 1"
                                                    type="button"
                                                    variant="destructive"
                                                    @click="removeStock(index)"
                                                    class="mt-1"
                                                >
                                                    Supprimer
                                                </Button>
                                            </div>
                                            <div v-if="getFormError(`stocks.${index}.local_alert_threshold`)" class="text-sm text-red-500 mt-1">
                                                {{ getFormError(`stocks.${index}.local_alert_threshold`) }}
                                            </div>
                                        </div>
                                    </div>

                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="addStock"
                                        class="w-full"
                                    >
                                        <Plus class="h-4 w-4 mr-2" />
                                        Ajouter une fourniture
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <Button
                                type="button"
                                variant="outline"
                                @click="router.visit(route('stock-items.by-site', { site: props.site }))"
                            >
                                Annuler
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                Enregistrer
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 