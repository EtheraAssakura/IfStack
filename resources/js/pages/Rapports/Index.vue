<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Download } from 'lucide-vue-next';

interface Report {
    id: number;
    name: string;
    path: string;
    created_at: string;
}

interface Props {
    rapports: Report[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Rapports',
        href: '/reports',
    },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Rapports" />

    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Rapports
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="rapports.length === 0" class="text-center text-gray-500 py-8">
                            Aucun rapport disponible pour le moment.
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rapport
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="rapport in rapports" :key="rapport.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ rapport.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(rapport.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                as-child
                                            >
                                                <Link
                                                    :href="route('reports.download', rapport.id)"
                                                    class="inline-flex items-center gap-2"
                                                >
                                                    <Download class="h-4 w-4" />
                                                    Télécharger
                                                </Link>
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>
