<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';

interface Notification {
    id: number;
    type: string;
    title: string;
    content: string;
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
        processed: boolean;
        location: {
            name: string;
            site: {
                name: string;
            };
        };
    };
}

const props = defineProps<{
    notification: Notification;
}>();

const isProcessed = ref(props.notification.type === 'alert' ? props.notification.stock?.processed : props.notification.is_read);
const initialProcessedState = ref(props.notification.type === 'alert' ? props.notification.stock?.processed : props.notification.is_read);
const showSuccessModal = ref(false);

const handleProcessChange = () => {
    const currentValue = props.notification.type === 'alert' ? props.notification.stock?.processed : props.notification.is_read;
    const newValue = !currentValue;
    
    router.put(route('notifications.process', [props.notification.id, { type: props.notification.type }]), {
        processed: newValue
    }, {
        preserveScroll: true,
        onSuccess: () => {
            if (props.notification.type === 'alert' && props.notification.stock) {
                props.notification.stock.processed = newValue;
            } else {
                props.notification.is_read = newValue;
            }
            isProcessed.value = newValue;
            
            showSuccessModal.value = true;
            setTimeout(() => {
                showSuccessModal.value = false;
            }, 1000);
        },
        onError: (errors) => {
            console.error('Error updating notification:', errors);
        }
    });
};

const closeModal = () => {
    showSuccessModal.value = false;
};

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
                            La notification a été mise à jour avec succès
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">{{ notification.title }}</h3>
                            <p class="mt-2 text-gray-600">{{ notification.content }}</p>
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
                                    <span class="font-medium text-gray-700">Emplacement :</span>
                                    <span class="ml-2 text-gray-600">{{ notification.stock.location.name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Établissement :</span>
                                    <span class="ml-2 text-gray-600">{{ notification.stock.location.site.name }}</span>
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
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informations de l'utilisateur</h4>
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

                        <div class="mt-6 flex justify-between items-center">
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="processed"
                                    v-model="isProcessed"
                                    @change="handleProcessChange"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="processed" class="ml-2 block text-sm text-gray-900">
                                    Traiter
                                </label>
                            </div>
                            <Link
                                :href="initialProcessedState ? route('notifications.archive') : route('notifications.index')"
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

<style scoped>
.fixed {
    position: fixed;
}
.inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
</style> 