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
    stock?: {
        name: string;
        estimated_quantity: number;
        local_alert_threshold: number;
    };
}

const props = defineProps<{
    notification: Notification;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Notifications',
        href: route('notifications.index'),
    },
    {
        title: 'Détails',
        href: route('notifications.show', props.notification.id),
    },
];
</script>

<template>
    <Head :title="notification.title" />

    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Détails de la notification
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">{{ notification.title }}</h3>
                            <p class="mt-2 text-gray-600">{{ notification.message }}</p>
                            <div class="mt-4 text-sm text-gray-500">
                                {{ new Date(notification.created_at).toLocaleDateString() }} à {{ new Date(notification.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                            </div>
                        </div>

                        <!-- Informations spécifiques aux alertes de stock -->
                        <div v-if="notification.type === 'alert' && notification.stock" class="border-t pt-6 mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informations sur le stock</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <span class="font-medium text-gray-700">Fourniture :</span>
                                    <span class="ml-2 text-gray-600">{{ notification.stock.name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Quantité estimée :</span>
                                    <span class="ml-2 text-gray-600">{{ notification.stock.estimated_quantity }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Seuil d'alerte :</span>
                                    <span class="ml-2 text-gray-600">{{ notification.stock.local_alert_threshold }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informations du demandeur</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <span class="font-medium text-gray-700">Nom :</span>
                                    <span class="ml-2 text-gray-600">{{ notification.users[0]?.name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Email :</span>
                                    <span class="ml-2 text-gray-600">{{ notification.users[0]?.email }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Etablissement :</span>
                                    <span class="ml-2 text-gray-600">{{ notification.users[0]?.site?.name || 'Non assigné' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <Link
                                :href="route('notifications.index')"
                                class="text-sm text-gray-600 hover:text-gray-900"
                            >
                                Retour à la liste
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 