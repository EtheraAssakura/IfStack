<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex gap-6">
                <!-- Partie gauche : Cartes statistiques -->
                <div class="flex gap-6">
                    <!-- Carte Total utilisateurs -->
                    <div class="rounded-xl bg-white p-6 shadow-sm w-64">
                        <div class="flex items-center">
                            <div class="rounded-lg bg-primary/10 p-3">
                                <UsersIcon class="h-6 w-6 text-primary" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total utilisateurs</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ props.users.length }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Carte Administrateurs -->
                    <div class="rounded-xl bg-white p-6 shadow-sm w-64">
                        <div class="flex items-center">
                            <div class="rounded-lg bg-primary/10 p-3">
                                <ShieldCheckIcon class="h-6 w-6 text-primary" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Administrateurs</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ props.users.filter(u => u.roles.some(r => r.name === 'Administrateur')).length }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Partie droite : Bouton et filtres -->
                <div class="flex flex-col gap-4">
                    <!-- Bouton Nouvel utilisateur -->
                    <div class="flex justify-end">
                        <Link
                            :href="route('users.create')"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary/80"
                        >
                            <PlusIcon class="h-5 w-5" />
                            Nouvel utilisateur
                        </Link>
                    </div>

                    <!-- Barre de recherche et filtres -->
                    <div class="flex gap-4">
                        <div class="relative flex-1">
                            <div class="relative">
                                <input
                                    v-model="search"
                                    type="text"
                                    class="block w-full appearance-none rounded-xl border-0 bg-gray-50 px-4 py-3 pr-8 text-base ring-1 ring-inset ring-gray-200 transition-all duration-300 hover:ring-gray-300 focus:bg-white focus:ring-2 focus:ring-primary"
                                    placeholder="Rechercher un utilisateur..."
                                    @input="debouncedFilter"
                                />
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                    <SearchIcon class="h-5 w-5 text-gray-400" />
                                </div>
                            </div>
                        </div>
                        <div class="w-48">
                            <div class="relative">
                                <select
                                    v-model="selectedRole"
                                    class="block w-full appearance-none rounded-xl border-0 bg-gray-50 px-4 py-3 pr-8 text-base ring-1 ring-inset ring-gray-200 transition-all duration-300 hover:ring-gray-300 focus:bg-white focus:ring-2 focus:ring-primary"
                                    @change="filterUsers"
                                >
                                    <option value="">Tous les rôles</option>
                                    <option v-for="role in uniqueRoles" :key="role.id" :value="role.name">
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
            </div>

            <!-- Table des utilisateurs -->
            <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200/50">
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
                                    :href="route('users.edit', user.id)"
                                    class="text-primary hover:text-primary/80"
                                >
                                    Modifier
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ChevronDownIcon, PlusIcon, SearchIcon, ShieldCheckIcon, UsersIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface BreadcrumbItemType {
    title: string;
    href: string;
}

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Utilisateurs',
        href: route('users.index'),
    },
];

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

// Extraire les rôles uniques des utilisateurs
const uniqueRoles = computed(() => {
    const roleSet = new Set<string>();
    props.users.forEach(user => {
        user.roles.forEach(role => {
            roleSet.add(role.name);
        });
    });
    return Array.from(roleSet).map(name => ({ id: name, name }));
});

const search = ref('');
const selectedRole = ref('');

const filteredUsers = computed(() => {
    return props.users.filter(user => {
        const matchesSearch = search.value === '' ||
            user.name.toLowerCase().includes(search.value.toLowerCase()) ||
            user.email.toLowerCase().includes(search.value.toLowerCase());

        const matchesRole = selectedRole.value === '' ||
            user.roles.some(role => role.name === selectedRole.value);

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
