<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Location {
    id: number;
    name: string;
}

interface Site {
    id: number;
    name: string;
    locations: Location[];
}

const props = withDefaults(defineProps<{
    locationId?: number;
    site?: Site;
}>(), {
    locationId: undefined,
    site: undefined
});

const page = usePage();
const isAdmin = page.props.auth.user?.roles?.some(role => role.name === 'Administrateur');
const showLocationModal = ref(false);



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
                        {{ locationId ? "J'ai pris une fourniture" : "J'ai pris une fourniture (choisir l'emplacement)" }}
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
    </div>
</template>

<style>
body {
    font-family: 'Inter var', sans-serif;
}
</style>
