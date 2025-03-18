<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

interface Etablissement {
    id: number;
    name: string;
    address: string;
    city: string;
    postal_code: string;
    phone: string | null;
    email: string | null;
    plan_path: string | null;
}

interface Props {
    etablissement: Etablissement;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.etablissement.name,
    address: props.etablissement.address,
    city: props.etablissement.city,
    postal_code: props.etablissement.postal_code,
    phone: props.etablissement.phone ?? '',
    email: props.etablissement.email ?? '',
    plan: null as File | null,
});

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Établissements',
        href: '/etablissements',
    },
    {
        title: props.etablissement.name,
        href: `/etablissements/${props.etablissement.id}`,
    },
    {
        title: 'Modifier',
        href: `/etablissements/${props.etablissement.id}/edit`,
    },
];

const handlePlanChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    form.plan = input.files?.[0] || null;
};

const handleSubmit = () => {
    form.put(route('etablissements.update', props.etablissement.id));
};
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Modifier {{ etablissement.name }} - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-semibold mb-6">Modifier {{ etablissement.name }}</h1>

                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="name">Nom</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                    />
                                    <div v-if="form.errors.name" class="text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="address">Adresse</Label>
                                    <Input
                                        id="address"
                                        v-model="form.address"
                                        type="text"
                                        required
                                    />
                                    <div v-if="form.errors.address" class="text-sm text-red-600">
                                        {{ form.errors.address }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="city">Ville</Label>
                                    <Input
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                        required
                                    />
                                    <div v-if="form.errors.city" class="text-sm text-red-600">
                                        {{ form.errors.city }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="postal_code">Code Postal</Label>
                                    <Input
                                        id="postal_code"
                                        v-model="form.postal_code"
                                        type="text"
                                        required
                                    />
                                    <div v-if="form.errors.postal_code" class="text-sm text-red-600">
                                        {{ form.errors.postal_code }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="phone">Téléphone</Label>
                                    <Input
                                        id="phone"
                                        v-model="form.phone"
                                        type="tel"
                                    />
                                    <div v-if="form.errors.phone" class="text-sm text-red-600">
                                        {{ form.errors.phone }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                    />
                                    <div v-if="form.errors.email" class="text-sm text-red-600">
                                        {{ form.errors.email }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="plan">Plan (optionnel)</Label>
                                    <Input
                                        id="plan"
                                        type="file"
                                        accept="image/*"
                                        @change="handlePlanChange"
                                    />
                                    <div v-if="form.errors.plan" class="text-sm text-red-600">
                                        {{ form.errors.plan }}
                                    </div>
                                    <div v-if="etablissement.plan_path" class="text-sm text-gray-500">
                                        Plan actuel : {{ etablissement.plan_path }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Button
                                    variant="outline"
                                    @click="route('etablissements.index')"
                                >
                                    Annuler
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    Mettre à jour
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 