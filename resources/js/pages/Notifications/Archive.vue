<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

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
    notifications: Notification[];
    type: 'alert' | 'request';
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Notifications',
        href: route('notifications.index'),
    },
    {
        title: `Historique des ${props.type === 'alert' ? 'alertes' : 'demandes'}`,
        href: route('notifications.archive', { type: props.type }),
    },
];
</script>

<template>
    <Head :title="`Archive des ${type === 'alert' ? 'alertes' : 'demandes'}`" />

    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historique des {{ type === 'alert' ? 'alertes' : 'demandes' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="space-y-4">
                            <template v-if="notifications.length > 0">
                                <Link
                                    v-for="notification in notifications"
                                    :key="notification.id"
                                    :href="route('notifications.show', [notification.id, { type }])"
                                    class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors h-[120px]"
                                    :class="type === 'alert' ? 'bg-white' : 'bg-white'"
                                >
                                    <div class="flex flex-col h-full justify-between">
                                        <div>
                                            <h4 class="font-medium">{{ notification.title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1 line-clamp-1">
                                                {{ type === 'alert' ? notification.comment : notification.content }}
                                            </p>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <span>Créé le : </span>
                                            {{ new Date(notification.created_at).toLocaleDateString() }} à 
                                            {{ new Date(notification.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }} 
                                            {{ type === 'alert' ? 'signalé' : 'par' }} {{ notification.users[0].name }}
                                        </div>
                                    </div>
                                </Link>
                            </template>
                            <div v-else class="text-gray-500 text-center py-4">
                                Aucune archive disponible
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 