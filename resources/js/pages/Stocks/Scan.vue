<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Stock {
    id: number;
    quantite_estimee: number;
    seuil_alerte_local: number;
    fourniture: {
        id: number;
        nom: string;
        reference_isfac: string;
    };
}

interface Emplacement {
    id: number;
    nom: string;
    etablissement: {
        id: number;
        nom: string;
    };
    stocks: Stock[];
}

interface Props {
    emplacement: Emplacement;
}

defineProps<Props>();
</script>

<template>
    <AppLayout>
        <Head title="Scan d'emplacement" />

        <div class="container py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Scan d'emplacement</h1>
                    <p class="text-muted-foreground">
                        {{ emplacement.nom }} - {{ emplacement.etablissement.nom }}
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <Button variant="outline" as-child>
                        <Link :href="route('stocks.index')">
                            Retour
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="mt-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Stocks disponibles</CardTitle>
                        <CardDescription>
                            Liste des stocks dans cet emplacement
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="emplacement.stocks.length === 0" class="text-center text-muted-foreground">
                            Aucun stock disponible dans cet emplacement
                        </div>
                        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <Card v-for="stock in emplacement.stocks" :key="stock.id">
                                <CardHeader>
                                    <CardTitle class="text-lg">{{ stock.fourniture.nom }}</CardTitle>
                                    <CardDescription>
                                        Référence : {{ stock.fourniture.reference_isfac }}
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium">Quantité</span>
                                            <span class="text-sm">{{ stock.quantite_estimee }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium">Seuil d'alerte</span>
                                            <span class="text-sm">{{ stock.seuil_alerte_local }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium">Statut</span>
                                            <Badge :variant="stock.quantite_estimee <= stock.seuil_alerte_local ? 'destructive' : 'default'">
                                                {{ stock.quantite_estimee <= stock.seuil_alerte_local ? 'En alerte' : 'Normal' }}
                                            </Badge>
                                        </div>
                                    </div>
                                    <Button variant="outline" class="mt-4 w-full" as-child>
                                        <Link :href="route('stocks.show', stock.id)">
                                            Voir les détails
                                        </Link>
                                    </Button>
                                </CardContent>
                            </Card>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template> 