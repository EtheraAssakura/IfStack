<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Supplier {
    id: number;
    name: string;
    contact_name: string;
    email: string;
    phone: string;
    address: string;
    city: string;
    country: string;
    postal_code: string;
    catalog_url: string | null;
}

defineProps<{
    suppliers: Supplier[];
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Fournisseurs',
        href: '/suppliers',
    },
];
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Fournisseurs - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Fournisseurs</h1>
                        <p class="text-muted-foreground">
                            Gestion des fournisseurs
                        </p>
                    </div>
                    <Link
                        :href="route('suppliers.create')"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                    >
                        Nouveau fournisseur
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catalogue</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="supplier in suppliers" :key="supplier.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ supplier.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ supplier.contact_name ? supplier.contact_name : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ supplier.email ? supplier.email : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ supplier.phone ? supplier.phone : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="supplier.catalog_url" target="_blank" class="text-blue-500 hover:text-blue-700">
                                                {{ supplier.catalog_url ? supplier.catalog_url : '-' }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link
                                                :href="route('suppliers.edit', supplier.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            >
                                                Modifier
                                            </Link>
                                            <Link
                                                :href="route('suppliers.destroy', supplier.id)"
                                                method="delete"
                                                as="button"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Supprimer
                                            </Link>
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