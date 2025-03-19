<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { MoreVertical, Pencil, Plus, Trash2 } from 'lucide-vue-next';

interface Etablissement {
    id: number;
    name: string;
    address: string;
    city: string;
    postal_code: string;
    phone: string | null;
    email: string | null;
    emplacements_count: number;
    stocks_count: number;
}

interface Props {
    etablissements: Etablissement[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Établissements',
        href: '/etablissements',
    },
];

const deleteEtablissement = (id: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet établissement ?')) {
        router.delete(route('etablissements.destroy', id));
    }
};
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Établissements - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Établissements</h1>
                        <p class="text-muted-foreground">
                            Gestion des établissements
                        </p>
                    </div>
                    <Button as-child>
                        <Link :href="route('etablissements.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Nouvel établissement
                        </Link>
                    </Button>
                </div>

                <div v-if="etablissements.length === 0" class="text-center py-12">
                    <p class="text-gray-500">Aucun établissement trouvé.</p>
                </div>

                <div v-else class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Nom
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Téléphone
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Emplacements
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="etablissement in etablissements" :key="etablissement.id" class="group transition-colors hover:bg-gray-50/50">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ etablissement.name }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ etablissement.phone ?? '-' }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ etablissement.email ?? '-' }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ etablissement.emplacements_count }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                            <div class="flex justify-end gap-2">
                                                <Link
                                                    :href="route('etablissements.show', etablissement.id)"
                                                    class="inline-flex items-center gap-2 text-gray-500 transition-colors hover:text-primary"
                                                >
                                                    Détails
                                                </Link>
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger as-child>
                                                        <Button variant="ghost" size="icon" class="h-8 w-8">
                                                            <MoreVertical class="h-4 w-4" />
                                                        </Button>
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent align="end">
                                                        <DropdownMenuItem as-child>
                                                            <Link :href="route('etablissements.edit', etablissement.id)" class="flex items-center gap-2">
                                                                <Pencil class="h-4 w-4" />
                                                                Modifier
                                                            </Link>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem @click="deleteEtablissement(etablissement.id)" class="text-red-500 focus:text-red-500">
                                                            <Trash2 class="h-4 w-4" />
                                                            Supprimer
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
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
    </AppSidebarLayout>
</template> 