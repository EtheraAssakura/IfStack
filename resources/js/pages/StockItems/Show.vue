<script setup lang="ts">
import UserStockView from '@/components/Stock/UserStockView.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import type { Stock } from '@/types/app';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface StockItem extends Stock {
    alerts?: Array<{
        id: number;
        type: string;
        message: string;
        created_at: string;
    }>;
}

const props = defineProps<{
    stockItem: StockItem;
}>();

const page = usePage();
const isUser = page.props.auth?.user?.roles?.some(role => role.name === 'Utilisateur') ?? false;
const dialogOpen = ref(false);

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Stocks',
        href: route('stock-items.index'),
    },
    {
        title: 'Détails du stock',
        href: route('stock-items.show', props.stockItem.id),
    },
];

const form = useForm({
    estimated_quantity: props.stockItem.estimated_quantity,
    local_alert_threshold: props.stockItem.local_alert_threshold,
});

const ruptureForm = useForm({
    estimated_quantity: 0,
    commentaire: `Le stock de ${props.stockItem.supply.name} situé à l'emplacement ${props.stockItem.location.name} (${props.stockItem.location.site.name}) est en rupture de stock`
});

const submit = () => {
    form.put(route('stock-items.update', props.stockItem.id));
};

const signalRupture = () => {
    ruptureForm.post(route('stock-items.signal-rupture', props.stockItem.id), {
        onSuccess: () => {
            dialogOpen.value = false;
            router.visit(route('stock-items.show', props.stockItem.id));
        }
    });
};
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head title="Détails du stock" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <UserStockView v-if="isUser" :stock="stockItem" />
                <div v-else class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Détails du stock</h1>
                            <p class="text-sm text-gray-500">
                                {{ stockItem.supply.name }} - {{ stockItem.location.name }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <Button variant="outline" as-child>
                                <Link :href="route('stock-items.index')">
                                    Retour
                                </Link>
                            </Button>
                            <Dialog v-model:open="dialogOpen">
                                <DialogTrigger as-child>
                                    <Button variant="destructive">
                                        Signaler une rupture
                                    </Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Signaler une rupture de stock</DialogTitle>
                                        <DialogDescription>
                                            Cette action mettra la quantité estimée à 0 et créera une alerte de rupture de stock.
                                        </DialogDescription>
                                    </DialogHeader>
                                    <form @submit.prevent="signalRupture" class="space-y-4">
                                        <Button type="submit" variant="destructive" :disabled="ruptureForm.processing">
                                            Confirmer la rupture
                                        </Button>
                                    </form>
                                </DialogContent>
                            </Dialog>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h2>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Établissement</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ stockItem.location.site.name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Emplacement</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ stockItem.location.name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fourniture</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ stockItem.supply.name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Référence</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ stockItem.supply.reference }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">État du stock</h2>
                            <form @submit.prevent="submit" class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="estimated_quantity">Quantité estimée</Label>
                                    <Input
                                        id="estimated_quantity"
                                        v-model="form.estimated_quantity"
                                        type="number"
                                        min="0"
                                        required
                                        class="bg-white"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="local_alert_threshold">Seuil d'alerte local</Label>
                                    <Input
                                        id="local_alert_threshold"
                                        v-model="form.local_alert_threshold"
                                        type="number"
                                        min="0"
                                        class="bg-white"
                                    />
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                    <dd class="mt-1">
                                        <Badge :variant="stockItem.estimated_quantity <= stockItem.local_alert_threshold ? 'destructive' : 'default'">
                                            {{ stockItem.estimated_quantity <= stockItem.local_alert_threshold ? 'En alerte' : 'Normal' }}
                                        </Badge>
                                    </dd>
                                </div>
                                <Button type="submit" :disabled="form.processing" class="w-full">
                                    Mettre à jour
                                </Button>
                            </form>
                        </div>
                    </div>
                </div>

                <div v-if="stockItem.alerts?.length" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Historique des alertes</h2>
                    <div class="space-y-4">
                        <div v-for="alert in stockItem.alerts" :key="alert.id" class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                            <Badge :variant="alert.type === 'rupture' ? 'destructive' : 'default'">
                                {{ alert.type === 'rupture' ? 'Rupture' : 'Seuil atteint' }}
                            </Badge>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">{{ alert.message }}</p>
                                <p class="text-xs text-gray-500">
                                    Le {{ new Date(alert.created_at).toLocaleDateString() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 