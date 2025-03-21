<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { BreadcrumbItemType } from '@/types';
import type { Stock } from '@/types/app';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    stocks: Stock[];
    locationId: string;
}>();

console.log('Props reçues:', { stocks: props.stocks, locationId: props.locationId });

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

const filteredStocks = computed(() => {
    return props.stocks.filter(stock => stock.location.id === Number(props.locationId));
});

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
</script>

<template>
    <UserLayout :breadcrumbs="breadcrumbs">
        <Head title="Prise de stock" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Prise de stock</h1>
                            <p class="text-sm text-gray-500">
                                Sélectionnez une fourniture à prendre
                            </p>
                        </div>
                        <Link :href="route('welcome', { locationId: props.locationId })" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                            Retour
                        </Link>
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

                                <Button type="submit" class="w-full">
                                    Mettre à jour
                                </Button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template> 