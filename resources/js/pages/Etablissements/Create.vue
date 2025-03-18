<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    address: '',
    city: '',
    postal_code: '',
    phone: '',
    email: '',
    plan: null as File | null,
});

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Établissements',
        href: '/etablissements',
    },
    {
        title: 'Nouvel établissement',
        href: '/etablissements/create',
    },
];

const handleSubmit = () => {
    form.post(route('etablissements.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Nouvel établissement - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-semibold mb-6">Nouvel établissement</h1>

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
                                        @change="e => form.plan = e.target.files[0]"
                                    />
                                    <div v-if="form.errors.plan" class="text-sm text-red-600">
                                        {{ form.errors.plan }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="$inertia.visit(route('etablissements.index'))"
                                >
                                    Annuler
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    Créer l'établissement
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 