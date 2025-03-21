<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

interface Stock {
    id: number;
    estimated_quantity: number;
    local_alert_threshold: number;
    fourniture: {
        id: number;
        name: string;
        reference: string;
    };
    emplacement: {
        id: number;
        name: string;
        etablissement: {
            id: number;
            name: string;
        };
    };
}

interface Props {
    stocks: Record<string, Stock[]>;
    site: string;
}

const props = defineProps<Props>();

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
        title: props.site.charAt(0).toUpperCase() + props.site.slice(1),
        href: `/stocks?site=${props.site}`,
    },
];

const getBadgeVariant = (stock: Stock) => {
    return stock.estimated_quantity <= stock.local_alert_threshold ? 'destructive' : 'default';
};

const getBadgeText = (stock: Stock) => {
    return stock.estimated_quantity <= stock.local_alert_threshold ? 'En alerte' : 'Normal';
};

const getStockValue = (stock: Stock, path: string) => {
    return path.split('.').reduce((obj, key) => obj?.[key], stock) ?? '-';
};
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Stocks - {{ site.charAt(0).toUpperCase() + site.slice(1) }} - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Stocks - {{ site.charAt(0).toUpperCase() + site.slice(1) }}</h1>
                        <p class="text-muted-foreground">
                            Gestion des stocks par établissement
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <Button variant="outline" as-child>
                            <Link :href="route('stocks.sites')">
                                Changer de site
                            </Link>
                        </Button>
                        <Button as-child>
                            <Link :href="route('stocks.create', { site: props.site })">
                                Gérer les stocks
                            </Link>
                        </Button>
                        <form :action="route('stocks.export', { site: props.site })" method="GET" class="inline">
                            <Button type="submit">
                                Exporter en Excel
                            </Button>
                        </form>
                    </div>
                </div>

                <div v-if="Object.keys(stocks).length === 0" class="text-center py-12">
                    <p class="text-gray-500">Aucun stock trouvé pour ce site.</p>
                    <p class="text-sm text-gray-400 mt-2">Site sélectionné : {{ site }}</p>
                </div>

                <div v-else class="space-y-6">
                    <div v-for="(stocks, etablissement) in props.stocks" :key="etablissement" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50/50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Emplacement
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Fourniture
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Référence
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Quantité
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Seuil local
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Statut
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr v-for="stock in stocks" :key="stock.id" class="group transition-colors hover:bg-gray-50/50">
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ getStockValue(stock, 'emplacement.name') }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ getStockValue(stock, 'fourniture.name') }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ getStockValue(stock, 'fourniture.reference') }}</div>
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
                                                        :href="route('stocks.show', stock.id)"
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 