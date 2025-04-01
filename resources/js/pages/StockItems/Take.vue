<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { SearchIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface StockItem {
    id: number;
    supply: {
        name: string;
        reference: string;
    };
    location: {
        id: number;
        name: string;
    };
    estimated_quantity: number;
    local_alert_threshold: number;
}

const props = defineProps<{
    stocks: StockItem[];
    locationId?: string;
    siteId?: string;
    site?: StockItem;
}>();

console.log('Props reçues:', { stocks: props.stocks, locationId: props.locationId, siteId: props.siteId });

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Stocks',
        href: route('stock-items.index'),
    },
    {
        title: 'Prise de stock',
        href: route('stock-items.take'),
    },
];

const search = ref('');

const filteredStocks = computed(() => {
    let stocks = props.stocks;
    
    if (props.locationId) {
        stocks = stocks.filter(stock => stock.location.id === Number(props.locationId));
    }

    if (search.value) {
        const searchLower = search.value.toLowerCase();
        stocks = stocks.filter(stock => 
            stock.supply.name.toLowerCase().includes(searchLower)
        );
    }

    return stocks;
});

const debouncedFilter = debounce(() => {
    // Cette fonction est appelée lors de la recherche
    // Le filtrage est géré automatiquement par le computed
}, 300);

const forms = ref(
    Object.fromEntries(
        filteredStocks.value.map(stock => [
            stock.id,
            useForm({
                quantity: stock.estimated_quantity,
            })
        ])
    )
);

const dialogOpen = ref(false);
const selectedStockId = ref<number | null>(null);
const showSuccessModal = ref(false);
const successMessage = ref('');

const ruptureForm = useForm({
    estimated_quantity: 0,
    comment: '',
});

const submit = (stockId: number) => {
    const stock = filteredStocks.value.find(s => s.id === stockId);
    if (!stock) return;

    const previousQuantity = stock.estimated_quantity;
    const newQuantity = forms.value[stockId].quantity;

    console.log('Soumission du formulaire', {
        stockId,
        previousQuantity,
        newQuantity,
        formData: forms.value[stockId]
    });

    forms.value[stockId].post(route('stock-items.take.submit', stockId), {
        onSuccess: () => {
            successMessage.value = 'Le stock a été mis à jour avec succès';
            showSuccessModal.value = true;

            // Si la quantité est passée sous le seuil d'alerte, on attend que la modale s'affiche avant de recharger
            if (newQuantity <= stock.local_alert_threshold && previousQuantity > stock.local_alert_threshold) {
                setTimeout(() => {
                    router.get(route('stock-items.take', {
                        locationId: props.locationId,
                        siteId: props.siteId
                    }), {}, {
                        preserveState: false,
                        preserveScroll: false
                    });
                }, 1000);
            }
        },
        onError: (errors) => {
            console.error('Erreur lors de la mise à jour du stock:', errors);
        }
    });
};

const signalRupture = () => {
    if (!selectedStockId.value) return;

    const stock = filteredStocks.value.find(s => s.id === selectedStockId.value);
    if (!stock) return;

    ruptureForm.post(route('stock-items.signal-rupture', selectedStockId.value), {
        onSuccess: () => {
            successMessage.value = 'La rupture de stock a été signalée avec succès';
            showSuccessModal.value = true;
            dialogOpen.value = false;
            selectedStockId.value = null;

            // On attend que la modale s'affiche avant de recharger
            setTimeout(() => {
                router.get(route('stock-items.take', {
                    locationId: props.locationId,
                    siteId: props.siteId
                }), {}, {
                    preserveState: false,
                    preserveScroll: false
                });
            }, 1000);
        }
    });
};

</script>

<template>
    <UserLayout :breadcrumbs="breadcrumbs">
        <Head title="Prise de stock" />

        <!-- Modale de succès -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform -translate-y-full"
            enter-to-class="transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform translate-y-0"
            leave-to-class="transform -translate-y-full"
        >
            <div v-if="showSuccessModal" class="fixed top-0 left-0 right-0 z-50">
                <div class="bg-white shadow-lg mx-auto max-w-sm mt-4 rounded-lg p-4" @click.stop>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="text-gray-900">
                            {{ successMessage }}
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Prise de stock</h1>
                            <p class="text-sm text-gray-500">
                                Sélectionnez une fourniture à prendre
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                :href="route('welcome', { 
                                    locationId: props.locationId
                                })"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
                            >
                                Retour
                            </Link>
                            <Link
                                :href="route('welcome')"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
                            >
                                Mauvais emplacement
                            </Link>
                        </div>
                    </div>

                    <!-- Barre de recherche -->
                    <div class="mb-6">
                        <div class="relative">
                            <input
                                v-model="search"
                                type="text"
                                class="block w-full appearance-none rounded-xl border-0 bg-gray-50 px-4 py-3 pr-8 text-base ring-1 ring-inset ring-gray-200 transition-all duration-300 hover:ring-gray-300 focus:bg-white focus:ring-2 focus:ring-primary"
                                placeholder="Rechercher une fourniture par nom..."
                                @input="debouncedFilter"
                            />
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                <SearchIcon class="h-5 w-5 text-gray-400" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="stock in filteredStocks" :key="stock.id" class="border rounded-lg p-4">
                            <h3 class="font-semibold">{{ stock.supply.name }}</h3>
                            <p class="text-sm text-gray-500">{{ stock.supply.reference }}</p>
                            <p class="text-sm text-gray-500">Emplacement: {{ stock.location.name }}</p>
                            <p class="text-sm text-gray-500">Quantité actuelle: {{ stock.estimated_quantity }}</p>
                            
                            <form @submit.prevent="submit(stock.id)" class="mt-4 space-y-4">
                                <div>
                                    <Label for="estimated_quantity">Quantité estimée en stock</Label>
                                    <Input
                                        :id="'estimated_quantity_' + stock.id"
                                        v-model="forms[stock.id].quantity"
                                        type="number"
                                        min="0"
                                        required
                                    />
                                </div>

                                <div class="flex flex-col sm:flex-row gap-2">
                                    <Button type="submit" class="w-full sm:flex-1">
                                        Mettre à jour
                                    </Button>
                                    <Dialog v-model:open="dialogOpen">
                                        <DialogTrigger asChild>
                                            <Button variant="destructive" type="button" @click="selectedStockId = stock.id" class="w-full sm:w-auto">
                                                Rupture
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent>
                                            <DialogHeader>
                                                <DialogTitle>Signaler une rupture de stock</DialogTitle>
                                                <DialogDescription>
                                                    Cette action mettra la quantité estimée à 0 et créera une alerte de rupture de stock.
                                                </DialogDescription>
                                            </DialogHeader>
                                            <form @submit.prevent="signalRupture()" class="space-y-4">
                                                <Button type="submit" variant="destructive" :disabled="ruptureForm.processing">
                                                    Confirmer la rupture
                                                </Button>
                                            </form>
                                        </DialogContent>
                                    </Dialog>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template> 