<script setup lang="ts">
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import UserLayout from '@/layouts/user/UserLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
});

const submit = () => {
    form.post(route('notifications.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <UserLayout>
        <Head>
            <title>Nouvelle Demande - ISFAC</title>
        </Head>

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nouvelle Demande
                </h2>
                <Link
                    :href="route('welcome')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Retour à l'accueil
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Titre de la demande
                                </label>
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="Ex: Demande de fournitures pour le bureau"
                                />
                                <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description détaillée
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="6"
                                    class="mt-1 block w-full rounded-xl border-0 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200/50 transition-colors placeholder:text-gray-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-primary disabled:pointer-events-none disabled:opacity-50"
                                    required
                                    placeholder="Décrivez votre demande en détail, ou vous avez besoin de fournitures, si c'est urgent..."
                                ></textarea>
                                <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <PrimaryButton
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full sm:w-auto"
                                >
                                    Envoyer la demande
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template> 