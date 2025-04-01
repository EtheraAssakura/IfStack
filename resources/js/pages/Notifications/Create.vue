<script setup lang="ts">
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import UserLayout from '@/layouts/user/UserLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    title: '',
    content: '',
});

const showSuccessModal = ref(false);

const submit = () => {
    form.post(route('notifications.store'), {
        onSuccess: () => {
            form.reset();
            showSuccessModal.value = true;
            setTimeout(() => {
                showSuccessModal.value = false;
            }, 1000);
        },
    });
};

const closeModal = () => {
    showSuccessModal.value = false;
};
</script>

<template>
    <UserLayout>
        <Head>
            <title>Nouvelle Demande - ISFAC</title>
        </Head>

        <!-- Modale de succès -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div v-if="showSuccessModal" class="fixed inset-0 flex items-center justify-center z-50" @click="closeModal">
                <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm mx-auto" @click.stop>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="text-gray-900">
                            La demande a été envoyée avec succès
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

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
                                <label for="content" class="block text-sm font-medium text-gray-700">
                                    Description détaillée
                                </label>
                                <textarea
                                    id="content"
                                    v-model="form.content"
                                    rows="6"
                                    class="mt-1 block w-full rounded-xl border-0 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200/50 transition-colors placeholder:text-gray-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-primary disabled:pointer-events-none disabled:opacity-50"
                                    required
                                    placeholder="Décrivez votre demande en détail, ou vous avez besoin de fournitures, si c'est urgent..."
                                ></textarea>
                                <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.content }}
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