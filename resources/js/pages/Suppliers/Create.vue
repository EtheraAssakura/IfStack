<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    contact_name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    postal_code: '',
    catalog_url: '',
});

const submit = () => {
    form.post(route('suppliers.store'));
};

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Fournisseurs',
        href: '/suppliers',
    },
    {
        title: 'Nouveau fournisseur',
        href: '/suppliers/create',
    },
];
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Nouveau fournisseur - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Nouveau fournisseur</h1>
                        <p class="text-muted-foreground">
                            Ajouter un nouveau fournisseur
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="mt-4">
                                <InputLabel for="name" value="Nom" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="contact_name" value="Nom du contact" />
                                <TextInput
                                    id="contact_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.contact_name"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.contact_name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="phone" value="Téléphone" />
                                <TextInput
                                    id="phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.phone"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.phone" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="address" value="Adresse" />
                                <TextInput
                                    id="address"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.address"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="city" value="Ville" />
                                <TextInput
                                    id="city"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.city"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.city" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="postal_code" value="Code postal" />
                                <TextInput
                                    id="postal_code"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.postal_code"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.postal_code" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="catalog_url" value="URL du catalogue" />
                                <TextInput
                                    id="catalog_url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    v-model="form.catalog_url"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.catalog_url" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Créer
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 