<template>
    <AppLayout title="Gestion des utilisateurs">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Gestion des utilisateurs
                </h2>
                <Link
                    :href="route('utilisateurs.create')"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary/80"
                >
                    <PlusIcon class="h-5 w-5" />
                    Nouvel utilisateur
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Carte statistique : Total des utilisateurs -->
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200/50">
                        <div class="flex items-center">
                            <div class="rounded-lg bg-primary/10 p-3">
                                <UsersIcon class="h-6 w-6 text-primary" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total utilisateurs</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ filteredUsers.length }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Carte statistique : Administrateurs -->
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200/50">
                        <div class="flex items-center">
                            <div class="rounded-lg bg-blue-50 p-3">
                                <ShieldCheckIcon class="h-6 w-6 text-blue-500" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Administrateurs</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ filteredUsers.filter(u => u.roles.some(r => r.name === 'Administrateur')).length }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Barre de recherche moderne -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <SearchBar
                                    v-model="search"
                                    placeholder="Rechercher un utilisateur..."
                                    @input="debouncedFilter"
                                />
                            </div>
                            <div class="relative">
                                <select
                                    v-model="selectedRole"
                                    class="block w-full appearance-none rounded-xl border-0 bg-gray-50 px-4 py-3 pr-8 text-base ring-1 ring-inset ring-gray-200 transition-all duration-300 hover:ring-gray-300 focus:bg-white focus:ring-2 focus:ring-primary"
                                    @change="filterUsers"
                                >
                                    <option value="">Tous les rôles</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                    <ChevronDownIcon class="h-5 w-5 text-gray-400" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200/50">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Nom
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Rôle
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="user in filteredUsers" :key="user.id" class="group transition-colors hover:bg-gray-50/50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10">
                                                    <span class="text-sm font-medium leading-none text-primary">
                                                        {{ user.name.charAt(0).toUpperCase() }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ user.email }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            v-for="role in user.roles"
                                            :key="role.id"
                                            :class="{
                                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium': true,
                                                'bg-primary/10 text-primary': role.name === 'Administrateur',
                                                'bg-blue-100 text-blue-800': role.name !== 'Administrateur'
                                            }"
                                        >
                                            {{ role.name }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <Link
                                            :href="route('utilisateurs.edit', user.id)"
                                            class="inline-flex items-center gap-2 text-gray-500 transition-colors hover:text-primary"
                                        >
                                            <PencilIcon class="h-4 w-4" />
                                            Modifier
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import SearchBar from '@/Components/SearchBar.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ChevronDownIcon, PencilIcon, PlusIcon, ShieldCheckIcon, UsersIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    users: {
        id: number;
        name: string;
        email: string;
        roles: {
            id: number;
            name: string;
        }[];
    }[];
    roles: {
        id: number;
        name: string;
    }[];
}>();

const search = ref('');
const selectedRole = ref('');

const filteredUsers = computed(() => {
    return props.users.filter(user => {
        const matchesSearch = search.value === '' ||
            user.name.toLowerCase().includes(search.value.toLowerCase()) ||
            user.email.toLowerCase().includes(search.value.toLowerCase());

        const matchesRole = selectedRole.value === '' ||
            user.roles.some(role => role.id === Number(selectedRole.value));

        return matchesSearch && matchesRole;
    });
});

const filterUsers = () => {
    // Cette fonction est appelée lors du changement de rôle
    // Le filtrage est géré automatiquement par le computed
};

const debouncedFilter = debounce(() => {
    // Cette fonction est appelée lors de la recherche
    // Le filtrage est géré automatiquement par le computed
}, 300);
</script>
