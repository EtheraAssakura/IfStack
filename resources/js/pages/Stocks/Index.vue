<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
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
                            <Link :href="route('stocks.export', { site })">
                                Exporter
                            </Link>
                        </Button>
                    </div>
                </div>

                <div v-if="Object.keys(stocks).length === 0" class="text-center py-12">
                    <p class="text-gray-500">Aucun stock trouvé pour ce site.</p>
                    <p class="text-sm text-gray-400 mt-2">Site sélectionné : {{ site }}</p>
                </div>

                <div v-else class="space-y-6">
                    <div v-for="(stocks, etablissement) in props.stocks" :key="etablissement" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ etablissement }}</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                Liste des stocks disponibles dans cet établissement
                            </p>
                            <div class="overflow-x-auto">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Emplacement</TableHead>
                                            <TableHead>Fourniture</TableHead>
                                            <TableHead>Référence</TableHead>
                                            <TableHead class="text-right">Quantité</TableHead>
                                            <TableHead class="text-right">Seuil local</TableHead>
                                            <TableHead>Statut</TableHead>
                                            <TableHead class="text-right">Actions</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="stock in stocks" :key="stock.id">
                                            <TableCell>{{ getStockValue(stock, 'emplacement.name') }}</TableCell>
                                            <TableCell>{{ getStockValue(stock, 'fourniture.name') }}</TableCell>
                                            <TableCell>{{ getStockValue(stock, 'fourniture.reference') }}</TableCell>
                                            <TableCell class="text-right">{{ stock.estimated_quantity ?? '-' }}</TableCell>
                                            <TableCell class="text-right">{{ stock.local_alert_threshold ?? '-' }}</TableCell>
                                            <TableCell>
                                                <Badge :variant="getBadgeVariant(stock)">
                                                    {{ getBadgeText(stock) }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell class="text-right">
                                                <Button variant="ghost" size="sm" as-child>
                                                    <Link :href="route('stocks.show', stock.id)">
                                                        Détails
                                                    </Link>
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 