<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { computed, defineProps, ref } from 'vue';

interface Notification {
    id: number;
    type: string;
    title: string;
    content: string;
    comment: string;
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

// Ajout de la computed property pour trier les demandes
const sortedRequests = computed(() => {
    if (!props.latestRequests) return [];
    
    return [...props.latestRequests].sort((a, b) => {
        // D'abord trier par statut de lecture (non lus en premier)
        if (a.is_read !== b.is_read) {
            return a.is_read ? 1 : -1;
        }
        // Ensuite trier par date (plus ancien en premier)
        return new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
    });
});

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
                content: alert.content,
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

// Ajout des refs pour gérer l'affichage des notifications traitées
const showProcessedAlerts = ref(false);
const showProcessedRequests = ref(false);

// Computed properties pour les notifications traitées
const processedAlerts = computed(() => {
    if (!props.latestAlerts) return [];
    return props.latestAlerts
        .filter(alert => alert.is_read)
        .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
        .slice(0, 5);
});

const processedRequests = computed(() => {
    if (!props.latestRequests) return [];
    return props.latestRequests
        .filter(request => request.is_read)
        .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
        .slice(0, 5);
});

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
                    <!-- Alertes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Alertes</h3>
                                <Link
                                    :href="route('notifications.archive', { type: 'alert' })"
                                    class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1"
                                >
                                    <span>Voir l'historique</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                            
                            <!-- Alertes non traitées -->
                            <div class="space-y-4">
                                <template v-if="latestAlerts && latestAlerts.filter(alert => !alert.is_read).length > 0">
                                    <Link
                                        v-for="alert in latestAlerts.filter(alert => !alert.is_read)"
                                        :key="alert.id"
                                        :href="route('notifications.show', [alert.id, { type: 'alert' }])"
                                        class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors h-[120px] bg-red-50"
                                    >
                                        <div class="flex flex-col h-full justify-between">
                                            <div>
                                                <h4 class="font-medium">{{ alert.title }}</h4>
                                                <p class="text-sm text-gray-600 mt-1 line-clamp-1">{{ alert.comment }}</p>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <span>Créé le : </span>
                                                {{ new Date(alert.created_at).toLocaleDateString() }} à {{ new Date(alert.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }} signalé par {{ alert.users[0].name }}
                                            </div>
                                        </div>
                                    </Link>
                                </template>
                                <div v-else class="text-gray-500 text-center py-4">
                                    Aucune alerte non traitée
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Demandes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Demandes</h3>
                                <Link
                                    :href="route('notifications.archive', { type: 'request' })"
                                    class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1"
                                >
                                    <span>Voir l'historique</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                            
                            <!-- Demandes non traitées -->
                            <div class="space-y-4">
                                <template v-if="latestRequests && latestRequests.filter(request => !request.is_read).length > 0">
                                    <Link
                                        v-for="request in latestRequests.filter(request => !request.is_read)"
                                        :key="request.id"
                                        :href="route('notifications.show', [request.id, { type: 'request' }])"
                                        class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors h-[120px] bg-blue-50"
                                    >
                                        <div class="flex flex-col h-full justify-between">
                                            <div>
                                                <h4 class="font-medium">{{ request.title }}</h4>
                                                <p class="text-sm text-gray-600 mt-1 line-clamp-1">{{ request.content }}</p>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <span>Créé le : </span>
                                                {{ new Date(request.created_at).toLocaleDateString() }} à {{ new Date(request.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }} par {{ request.users[0].name }}
                                            </div>
                                        </div>
                                    </Link>
                                </template>
                                <div v-else class="text-gray-500 text-center py-4">
                                    Aucune demande non traitée
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 