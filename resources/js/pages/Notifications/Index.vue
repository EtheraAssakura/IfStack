<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

interface Notification {
    id: number;
    type: string;
    title: string;
    message: string;
    is_read: boolean;
    created_at: string;
    users: {
        id: number;
        name: string;
        email: string;
        site: {
            name: string;
        };
    }[];
}

const props = defineProps<{
    latestAlerts: Notification[];
    latestRequests: Notification[];
}>();

// Ajout des logs de débogage
console.log('Props reçues:', props);
console.log('Alertes:', props.latestAlerts);
console.log('Nombre d\'alertes:', props.latestAlerts?.length || 0);
console.log('Demandes:', props.latestRequests);
console.log('Nombre de demandes:', props.latestRequests?.length || 0);

try {
    if (props.latestAlerts) {
        props.latestAlerts.forEach((alert, index) => {
            console.log(`Alerte ${index + 1}:`, {
                id: alert.id,
                type: alert.type,
                title: alert.title,
                message: alert.message,
                is_read: alert.is_read,
                created_at: alert.created_at
            });
        });
    }
} catch (error) {
    console.error('Erreur lors du traitement des alertes:', error);
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Notifications',
        href: route('notifications.index'),
    },
];

</script>

<template>
    <Head title="Notifications" />

    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notifications
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 auto-rows-auto">
                    <!-- Dernières Alertes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Dernières Alertes</h3>
                            <div class="space-y-4">
                                <template v-if="latestAlerts && latestAlerts.length > 0">
                                    <Link
                                        v-for="alert in latestAlerts"
                                        :key="alert.id"
                                        :href="route('notifications.show', [alert.id, { type: 'alert' }])"
                                        class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors h-[120px]"
                                        :class="{'bg-red-50': !alert.is_read, 'bg-white': alert.is_read}"
                                    >
                                        <div class="flex flex-col h-full justify-between">
                                            <div>
                                                <h4 class="font-medium">{{ alert.title }}</h4>
                                                <p class="text-sm text-gray-600 mt-1 line-clamp-1">{{ alert.message }}</p>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <span>Créé le : </span>
                                                {{ new Date(alert.created_at).toLocaleDateString() }} à {{ new Date(alert.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                                            </div>
                                        </div>
                                    </Link>
                                </template>
                                <div v-else class="text-gray-500 text-center py-4">
                                    Aucune alerte à afficher
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dernières Demandes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Dernières Demandes</h3>
                            <div class="space-y-4">
                                <template v-if="latestRequests && latestRequests.length > 0">
                                    <Link
                                        v-for="request in latestRequests"
                                        :key="request.id"
                                        :href="route('notifications.show', [request.id, { type: 'request' }])"
                                        class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors h-[120px]"
                                        :class="{'bg-blue-50': !request.is_read, 'bg-white': request.is_read}"
                                    >
                                        <div class="flex flex-col h-full justify-between">
                                            <div>
                                                <h4 class="font-medium">{{ request.title }}</h4>
                                                <p class="text-sm text-gray-600 mt-1 line-clamp-1">{{ request.message }}</p>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <span>Créé le : </span>
                                                {{ new Date(request.created_at).toLocaleDateString() }} à {{ new Date(request.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                                            </div>
                                        </div>
                                    </Link>
                                </template>
                                <div v-else class="text-gray-500 text-center py-4">
                                    Aucune demande à afficher
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 