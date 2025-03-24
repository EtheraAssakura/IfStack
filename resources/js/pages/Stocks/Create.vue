<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue';

interface Emplacement {
    id: number;
    name: string;
    etablissement: {
        id: number;
        name: string;
    };
    stocks?: Array<{
        fourniture_id: number;
        estimated_quantity: number;
        local_alert_threshold: number;
    }>;
}

interface Fourniture {
    id: number;
    name: string;
    reference: string;
}

interface Props {
    emplacements: Emplacement[];
    fournitures: Fourniture[];
    site: string;
    site_name: string;
}

interface FormErrors {
    emplacement_id?: string;
    stocks?: {
        [key: number]: {
            fourniture_id?: string;
            estimated_quantity?: string;
            local_alert_threshold?: string;
        };
    };
}

interface StockForm {
    fourniture_id: string;
    estimated_quantity: number;
    local_alert_threshold: number;
}

const props = defineProps<Props>();

const form = useForm<{
    emplacement_id: string;
    stocks: Array<{
        fourniture_id: string;
        estimated_quantity: number;
        local_alert_threshold: number;
    }>;
}>({
    emplacement_id: '',
    stocks: [
        {
            fourniture_id: '',
            estimated_quantity: 0,
            local_alert_threshold: 0
        }
    ]
});

// Computed property pour filtrer les fournitures disponibles
const getAvailableFournitures = computed(() => {
    // Récupérer les IDs des fournitures déjà sélectionnées
    const selectedIds = form.stocks.map(stock => stock.fourniture_id).filter(id => id !== '');
    
    // Retourner les fournitures qui ne sont pas déjà sélectionnées, triées par nom
    return props.fournitures
        .filter(fourniture => !selectedIds.includes(fourniture.id.toString()))
        .sort((a, b) => a.name.localeCompare(b.name));
});

// Computed property pour filtrer les fournitures disponibles pour une ligne spécifique
const getAvailableFournituresForLine = (currentFournitureId: string) => {
    // Récupérer les IDs des fournitures déjà sélectionnées, sauf celle de la ligne courante
    const selectedIds = form.stocks
        .map(stock => stock.fourniture_id)
        .filter(id => id !== '' && id !== currentFournitureId);
    
    // Retourner les fournitures qui ne sont pas déjà sélectionnées ou qui sont la fourniture courante, triées par nom
    return props.fournitures
        .filter(fourniture => 
            !selectedIds.includes(fourniture.id.toString()) || 
            fourniture.id.toString() === currentFournitureId
        )
        .sort((a, b) => a.name.localeCompare(b.name));
};

const getFormError = (path: string): string | undefined => {
    return form.errors[path as keyof FormErrors] as string | undefined;
};

const addStock = () => {
    form.stocks.push({
        fourniture_id: '',
        estimated_quantity: 0,
        local_alert_threshold: 0
    });
};

const removeStock = (index: number) => {
    form.stocks.splice(index, 1);
};

const loadExistingStocks = () => {
    if (!form.emplacement_id) {
        form.stocks = [{
            fourniture_id: '',
            estimated_quantity: 0,
            local_alert_threshold: 0
        }];
        return;
    }

    const selectedEmplacement = props.emplacements.find(e => e.id.toString() === form.emplacement_id);
    if (!selectedEmplacement) return;

    // Réinitialiser les stocks
    form.stocks = [];

    // Si l'emplacement a des stocks existants, les ajouter
    if (selectedEmplacement.stocks && selectedEmplacement.stocks.length > 0) {
        // Filtrer les doublons potentiels par fourniture_id
        const uniqueStocks = selectedEmplacement.stocks.reduce<StockForm[]>((acc, stock) => {
            if (!acc.some(s => s.fourniture_id === stock.fourniture_id.toString())) {
                acc.push({
                    fourniture_id: stock.fourniture_id.toString(),
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
            fourniture_id: '',
            estimated_quantity: 0,
            local_alert_threshold: 0
        });
    }
};

const submit = () => {
    // Filtrer les lignes vides et les doublons
    const nonEmptyStocks = form.stocks.filter(stock => stock.fourniture_id !== '');
    
    // Supprimer les doublons en gardant la dernière occurrence
    const uniqueStocks = nonEmptyStocks.reduce<StockForm[]>((acc, stock) => {
        const index = acc.findIndex(s => s.fourniture_id === stock.fourniture_id);
        if (index !== -1) {
            acc[index] = stock; // Remplacer par la dernière version
        } else {
            acc.push(stock);
        }
        return acc;
    }, []);

    form.stocks = uniqueStocks;
    form.post(route('stocks.store'));
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
                                    <Label for="emplacement_id">Emplacement</Label>
                                    <Select
                                        id="emplacement_id"
                                        v-model="form.emplacement_id"
                                        @change="loadExistingStocks"
                                        class="mt-1 block w-full"
                                        required
                                    >
                                        <option value="">Sélectionnez un emplacement</option>
                                        <option
                                            v-for="emplacement in emplacements"
                                            :key="emplacement.id"
                                            :value="emplacement.id.toString()"
                                        >
                                            {{ emplacement.name }} - {{ emplacement.etablissement.name }}
                                        </option>
                                    </Select>
                                    <div v-if="getFormError('emplacement_id')" class="text-sm text-red-500 mt-1">
                                        {{ getFormError('emplacement_id') }}
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
                                            <Label :for="'fourniture_id_' + index">Fourniture</Label>
                                            <Select
                                                :id="'fourniture_id_' + index"
                                                v-model="stock.fourniture_id"
                                                class="mt-1 block w-full"
                                                required
                                            >
                                                <option value="">Sélectionnez une fourniture</option>
                                                <option
                                                    v-for="fourniture in getAvailableFournituresForLine(stock.fourniture_id)"
                                                    :key="fourniture.id"
                                                    :value="fourniture.id.toString()"
                                                >
                                                    {{ fourniture.name }} ({{ fourniture.reference }})
                                                </option>
                                            </Select>
                                            <div v-if="getFormError(`stocks.${index}.fourniture_id`)" class="text-sm text-red-500 mt-1">
                                                {{ getFormError(`stocks.${index}.fourniture_id`) }}
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
                                @click="router.visit(route('stocks.by-site', { site: props.site }))"
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