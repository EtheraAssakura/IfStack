<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Emplacement {
    id: number;
    name: string;
    stocks: Stock[];
}

interface Stock {
    id: number;
    estimated_quantity: number;
    local_alert_threshold: number;
    fourniture: {
        id: number;
        name: string;
        reference: string;
    };
}

interface Etablissement {
    id: number;
    name: string;
    address: string;
    city: string;
    postal_code: string;
    phone: string | null;
    email: string | null;
    plan_path: string | null;
    emplacements: Emplacement[];
    stocks: Stock[];
}

interface Props {
    etablissement: Etablissement;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Établissements',
        href: '/etablissements',
    },
    {
        title: props.etablissement.name,
        href: `/etablissements/${props.etablissement.id}`,
    },
];
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>{{ etablissement.name }} - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">{{ etablissement.name }}</h1>
                        <p class="text-muted-foreground">
                            Détails de l'établissement
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <Button variant="outline" as-child>
                            <Link :href="route('etablissements.edit', etablissement.id)">
                                Modifier
                            </Link>
                        </Button>
                        <Button variant="destructive" as-child>
                            <Link :href="route('etablissements.destroy', etablissement.id)" method="delete" as="button">
                                Supprimer
                            </Link>
                        </Button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Informations générales</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ville</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.city }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Code Postal</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.postal_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.phone ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.email ?? '-' }}</dd>
                                </div>
                            </dl>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Plan</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="etablissement.plan_path" class="relative aspect-video">
                                <img
                                    :src="`/storage/${etablissement.plan_path}`"
                                    :alt="`Plan de ${etablissement.name}`"
                                    class="object-contain w-full h-full"
                                />
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                Aucun plan disponible
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="mt-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Emplacements</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="etablissement.emplacements.length === 0" class="text-center py-8 text-gray-500">
                                Aucun emplacement disponible
                            </div>
                            <div v-else class="space-y-6">
                                <div v-for="emplacement in etablissement.emplacements" :key="emplacement.id">
                                    <h3 class="text-lg font-medium mb-4">{{ emplacement.name }}</h3>
                                    <div v-if="emplacement.stocks.length === 0" class="text-sm text-gray-500">
                                        Aucun stock dans cet emplacement
                                    </div>
                                    <Table v-else>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Fourniture</TableHead>
                                                <TableHead>Référence</TableHead>
                                                <TableHead class="text-right">Quantité</TableHead>
                                                <TableHead class="text-right">Seuil local</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            <TableRow v-for="stock in emplacement.stocks" :key="stock.id">
                                                <TableCell>{{ stock.fourniture?.name ?? '-' }}</TableCell>
                                                <TableCell>{{ stock.fourniture?.reference ?? '-' }}</TableCell>
                                                <TableCell class="text-right">{{ stock.estimated_quantity }}</TableCell>
                                                <TableCell class="text-right">{{ stock.local_alert_threshold }}</TableCell>
                                            </TableRow>
                                        </TableBody>
                                    </Table>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 