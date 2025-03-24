<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Location {
    id: number;
    name: string;
}

interface Site {
    id: number;
    name: string;
    locations: Location[];
}

interface PageProps {
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            roles: Array<{
                id: number;
                name: string;
            }>;
        };
    };
    flash: {
        success?: string;
    };
    [key: string]: any;
}

const props = withDefaults(defineProps<{
    locationId?: number;
    site?: Site;
}>(), {
    locationId: undefined,
    site: undefined
});

const page = usePage<PageProps>();
const isAdmin = page.props.auth.user?.roles?.some(role => role.name === 'Administrateur');
const showLocationModal = ref(false);
const showSuccessModal = ref(false);
const successMessage = ref('');

// Surveiller les changements dans les props de la page pour détecter les messages de succès
watch(() => {
    return page.props.flash?.success;
}, (newValue) => {
    if (newValue) {
        successMessage.value = newValue;
        showSuccessModal.value = true;
        
        // Auto-fermeture après 1 seconde
        setTimeout(() => {
            showSuccessModal.value = false;
        }, 1000);
    }
}, { immediate: true });

const handleLogout = () => {
    router.post(route('logout'));
};

const handleTakeStock = () => {
    if (props.locationId) {
        router.visit(route('stock.take', { locationId: props.locationId }));
    } else {
        showLocationModal.value = true;
    }
};

const selectLocation = (locationId: number) => {
    router.visit(route('stock.take', { locationId }));
    showLocationModal.value = false;
};
</script>

<template>
    <Head title="Bienvenue sur IfStack" />

    <div class="min-h-screen bg-gradient-to-br from-[#023048] to-[#034268]">
        <main class="relative min-h-screen flex flex-col items-center justify-center px-4">
            <div class="w-full max-w-md mx-auto text-center space-y-12">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#023048]/10 to-transparent rounded-3xl transform rotate-3"></div>
                    <div class="relative bg-[#d2e9f7] rounded-3xl p-8 shadow-xl">
                        <img src="/Logo.png" alt="IfStack Logo" class="w-full h-auto" />
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <button
                        @click="handleTakeStock"
                        class="w-full py-3 bg-transparent border-2 border-white text-white rounded-full font-medium transition-all hover:bg-white/10 text-base"
                    >
                        {{ props.locationId ? "J'ai pris une fourniture" : "J'ai pris une fourniture (choisir l'emplacement)" }}
                    </button>
                    
                    <Link
                        v-if="!isAdmin"
                        :href="route('notifications.create')"
                        class="w-full py-3 bg-transparent border-2 border-white text-white rounded-full font-medium transition-all hover:bg-white/10"
                    >
                        Je veux faire une demande
                    </Link>

                    <Link
                        v-if="isAdmin"
                        :href="route('dashboard')"
                        class="w-full py-3 bg-transparent border-2 border-white text-white rounded-full font-medium transition-all hover:bg-white/10"
                    >
                        Accéder au tableau de bord
                    </Link>

                    <button
                        v-if="page.props.auth.user"
                        @click="handleLogout"
                        class="text-gray-400 hover:text-white text-sm transition-colors mt-2"
                    >
                        Se déconnecter
                    </button>
                </div>
            </div>
        </main>

        <Dialog :open="showLocationModal" @update:open="showLocationModal = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Sélectionnez l'emplacement</DialogTitle>
                </DialogHeader>
                <div class="grid grid-cols-1 gap-2 mt-4">
                    <div v-if="!site?.locations?.length" class="text-center text-gray-500">
                        Aucun emplacement disponible
                    </div>
                    <Button
                        v-for="location in site?.locations"
                        :key="location.id"
                        variant="outline"
                        class="w-full"
                        @click="selectLocation(location.id)"
                    >
                        {{ location.name }}
                    </Button>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Modale de succès -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform -translate-y-full"
            enter-to-class="transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform translate-y-0"
            leave-to-class="transform -translate-y-full"
        >
            <div v-if="showSuccessModal" class="fixed top-0 left-0 right-0 z-50">
                <div class="bg-white shadow-lg mx-auto max-w-sm mt-4 rounded-lg p-4" @click.stop>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="text-gray-900">
                            {{ successMessage }}
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style>
body {
    font-family: 'Inter var', sans-serif;
}

.fixed {
    position: fixed;
}

.inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
</style>
