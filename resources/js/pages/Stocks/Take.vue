<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { BreadcrumbItemType } from '@/types';
import type { Stock } from '@/types/app';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { SearchIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    stocks: Stock[];
    locationId?: string;
    siteId?: string;
    site?: Stock;
}>();

console.log('Props reçues:', { stocks: props.stocks, locationId: props.locationId, siteId: props.siteId });

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Stocks',
        href: route('stocks.index'),
    },
    {
        title: 'Prise de stock',
        href: route('stock.take'),
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
                comment: '',
            })
        ])
    )
);

const submit = (stockId: number) => {
    forms.value[stockId].post(route('stock.take.submit', stockId));
};

const dialogOpen = ref(false);
const selectedStockId = ref<number | null>(null);

const ruptureForm = useForm({
    estimated_quantity: 0,
    comment: '',
});

const signalRupture = () => {
    const currentStockId = selectedStockId.value;
    if (currentStockId === null) return;
    
    const stock = filteredStocks.value.find(s => s.id === currentStockId);
    if (!stock) return;

    ruptureForm.comment = `Le stock de ${stock.supply.name} situé à l'emplacement ${stock.location.name} est en rupture de stock`;
    
    forms.value[currentStockId].quantity = 0;
    forms.value[currentStockId].comment = ruptureForm.comment;
    forms.value[currentStockId].post(route('stock.take.submit', currentStockId), {
        onSuccess: () => {
            dialogOpen.value = false;
            selectedStockId.value = null;
            router.get(route('stock.take', {
                locationId: props.locationId,
                siteId: props.siteId
            }), {}, {
                preserveState: false,
                preserveScroll: false
            });
        }
    });
};
</script>

<template>
    <UserLayout :breadcrumbs="breadcrumbs">
        <Head title="Prise de stock" />

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
                                
                                <div>
                                    <Label for="comment">Commentaire</Label>
                                    <Textarea
                                        :id="'comment_' + stock.id"
                                        v-model="forms[stock.id].comment"
                                        placeholder="Raison de l'ajustement (optionnel)"
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