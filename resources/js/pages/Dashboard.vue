<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    alertStocks: Array<{
        id: number;
        supply: {
            id: number;
            name: string;
            reference: string;
        };
        location: {
            id: number;
            name: string;
            site: {
                name: string;
            };
        };
        estimated_quantity: number;
        local_alert_threshold: number | null;
    }>;
    unhandledAlerts: Array<{
        id: number;
        stockItem: {
            supply: {
                name: string;
                reference: string;
            };
            location: {
                name: string;
                site: {
                    name: string;
                };
            };
        };
        user: {
            name: string;
        };
        created_at: string;
    }>;
    weekDeliveries: Array<{
        id: number;
        order: {
            reference: string;
        };
        expected_date: string;
        status: string;
        items: Array<{
            location: {
                name: string;
                site: {
                    name: string;
                };
            };
        }>;
    }>;
    topSupplies: Array<{
        name: string;
        reference: string;
        total_ordered: number;
    }>;
    stats: {
        total_supplies: number;
        pending_orders: number;
        planned_deliveries: number;
        unhandled_alerts: number;
    };
    ordersByMonth: Array<{
        month: string;
        total: number;
    }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Tableau de bord',
        href: '/dashboard',
    },
];

const formattedDate = (date: string) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const stocksEnAlerte = computed(() => {
    const filtered = props.alertStocks.filter(stock => {
        const seuilAlerte = stock.local_alert_threshold;
        return stock.estimated_quantity <= seuilAlerte;
    });

    return filtered;
});
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Tableau de bord - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Fournitures</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ stats.total_supplies }}</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Commandes en cours</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ stats.pending_orders }}</p>
                    </div>
                    <!-- <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Livraisons planifiées</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ stats.planned_deliveries }}</p>
                    </div> -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Alertes non traitées</h3>
                        <p class="text-3xl font-bold text-red-600">{{ stats.unhandled_alerts }}</p>
                    </div>
                </div>

                <!-- Stocks en alerte -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Stocks en alerte</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fourniture</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emplacement</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seuil d'alerte</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="stock in stocksEnAlerte" :key="stock.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ stock.supply.reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ stock.supply.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ stock.location.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ stock.location.site.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">{{ stock.estimated_quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ stock.local_alert_threshold ?? stock.supply.global_alert_threshold }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <Button 
                                            variant="outline" 
                                            size="sm"
                                            as-child
                                        >
                                            <Link :href="route('commandes.create', { 
                                                fourniture_id: stock.supply.id,
                                                emplacement_id: stock.location.id,
                                                site: stock.location.site.name
                                            })">
                                                Commander
                                            </Link>
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Livraisons de la semaine -->
                <!-- <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Livraisons de la semaine</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commande</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date prévue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="delivery in weekDeliveries" :key="delivery.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ delivery.order.reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formattedDate(delivery.expected_date) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': delivery.status === 'planned',
                                                'bg-green-100 text-green-800': delivery.status === 'completed',
                                                'bg-gray-100 text-gray-800': delivery.status === 'cancelled'
                                            }">
                                            {{ delivery.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ delivery.items[0]?.location.site.name }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> -->

                <!-- Top 5 des fournitures -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 des fournitures les plus commandées</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fourniture</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité totale</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="supply in topSupplies" :key="supply.reference">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ supply.reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ supply.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ supply.total_ordered }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>
