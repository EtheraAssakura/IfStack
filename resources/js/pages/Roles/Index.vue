<template>
    <AppLayout :breadcrumbs="breadcrumbs" title="Gestion des rôles">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Gestion des rôles
                </h2>
                <Link
                    :href="route('roles.create')"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary/80"
                >
                    <PlusIcon class="h-5 w-5" />
                    Nouveau rôle
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200/50">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Nom
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Permissions
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Utilisateurs
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="role in roles" :key="role.id" class="group transition-colors hover:bg-gray-50/50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10">
                                                    <span class="text-sm font-medium leading-none text-primary">
                                                        {{ role.name.charAt(0).toUpperCase() }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ role.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            {{ role.description || 'Aucune description' }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="permission in role.permissions"
                                                :key="permission.id"
                                                class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary"
                                            >
                                                {{ permission.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                            {{ role.users_count }} utilisateur{{ role.users_count > 1 ? 's' : '' }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-4">
                                            <Link
                                                :href="route('roles.edit', role.id)"
                                                class="inline-flex items-center gap-2 text-gray-500 transition-colors hover:text-primary"
                                            >
                                                <PencilIcon class="h-4 w-4" />
                                                Modifier
                                            </Link>
                                            <button
                                                v-if="role.name !== 'Administrateur' && role.users_count === 0"
                                                @click="confirmDelete(role)"
                                                class="inline-flex items-center gap-2 text-gray-500 transition-colors hover:text-red-600"
                                            >
                                                <TrashIcon class="h-4 w-4" />
                                                Supprimer
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmation de suppression -->
        <Modal :show="deleteModal" @close="closeDeleteModal">
            <template #title>
                Confirmer la suppression
            </template>

            <div class="mt-2">
                <p class="text-sm text-gray-500">
                    Êtes-vous sûr de vouloir supprimer ce rôle ? Cette action est irréversible.
                </p>
            </div>

            <template #footer>
                <SecondaryButton @click="closeDeleteModal">
                    Annuler
                </SecondaryButton>
                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="deleteRole"
                >
                    Supprimer
                </DangerButton>
            </template>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import DangerButton from '@/components/DangerButton.vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types/BreadcrumbItemType';
import { Link, useForm } from '@inertiajs/vue3';
import { PencilIcon, PlusIcon, TrashIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface Permission {
    id: number;
    name: string;
    description: string;
}

interface Role {
    id: number;
    name: string;
    description: string | null;
    users_count: number;
    permissions: Permission[];
}

const props = defineProps<{
    roles: Role[];
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Rôles',
        href: route('roles.index'),
    },
];

const deleteModal = ref(false);
const roleToDelete = ref<number | null>(null);

const form = useForm({});

const confirmDelete = (role: { id: number }) => {
    roleToDelete.value = role.id;
    deleteModal.value = true;
};

const closeDeleteModal = () => {
    deleteModal.value = false;
    roleToDelete.value = null;
};

const deleteRole = () => {
    if (roleToDelete.value) {
        form.delete(route('roles.destroy', roleToDelete.value), {
            onSuccess: () => closeDeleteModal(),
        });
    }
};
</script>
