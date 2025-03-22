<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const isAdmin = page.props.auth.user?.roles?.some(role => role.name === 'Administrateur');
console.log('User roles:', page.props.auth.user?.roles);
console.log('Is admin:', isAdmin);

const props = defineProps<{
    locationId: number;
}>();

const handleLogout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head title="Welcome to IfStack" />

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
                    <Link
                        :href="route('stock.take', { locationId: props.locationId })"
                        class="w-full py-3 bg-transparent border-2 border-white text-white rounded-full font-medium transition-all hover:bg-white/10"
                    >
                        Voir les stocks de l'emplacement
                    </Link>
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
    </div>
</template>

<style>
body {
    font-family: 'Inter var', sans-serif;
}
</style>
