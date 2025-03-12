<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Permissions</h2>
                <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    + Nouvelle Permission
                </button>
            </div>

            <!-- Table des permissions -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôles</th>
                            <th scope="col" class="relative px-6 py-4">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="permission in permissions" :key="permission.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ permission.name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ permission.description }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <span v-for="role in permission.roles" :key="role" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="{
                                        'bg-red-100 text-red-800': role === 'admin',
                                        'bg-blue-100 text-blue-800': role === 'user'
                                    }">
                                        {{ role }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="relative">
                                    <button @click="(e) => toggleMenu(permission, e)" class="text-gray-400 hover:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <div v-if="permission.showMenu" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                        <div class="py-1">
                                            <button @click="editPermission(permission)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                                Éditer
                                            </button>
                                            <button @click="deletePermission(permission)" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 hover:text-red-700">
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal d'édition -->
        <TransitionRoot appear :show="showModal" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-10">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        >
                            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                                <div>
                                    <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                                        {{ isCreating ? 'Créer une permission' : 'Modifier la permission' }}
                                    </DialogTitle>
                                    <div class="mt-6 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nom</label>
                                            <input
                                                type="text"
                                                v-model="form.name"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                :class="{ 'border-red-500': errors.name }"
                                            />
                                            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name?.[0] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Description</label>
                                            <textarea
                                                v-model="form.description"
                                                rows="3"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                :class="{ 'border-red-500': errors.description }"
                                            ></textarea>
                                            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description?.[0] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Rôles</label>
                                            <div class="mt-2 space-y-2">
                                                <div class="flex items-center">
                                                    <input
                                                        type="checkbox"
                                                        v-model="form.roles"
                                                        value="admin"
                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    />
                                                    <label class="ml-2 text-sm text-gray-700">Admin</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input
                                                        type="checkbox"
                                                        v-model="form.roles"
                                                        value="user"
                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    />
                                                    <label class="ml-2 text-sm text-gray-700">User</label>
                                                </div>
                                            </div>
                                            <p v-if="errors.roles" class="mt-1 text-sm text-red-600">{{ errors.roles?.[0] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6 sm:mt-8 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="button"
                                        @click="savePermission"
                                        class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto"
                                    >
                                        {{ isCreating ? 'Créer' : 'Enregistrer' }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                    >
                                        Annuler
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Permission {
    id: number;
    name: string;
    description: string;
    roles: string[];
    showMenu?: boolean;
}

interface FormErrors {
    name?: string[];
    description?: string[];
    roles?: string[];
}

const { props } = usePage();
const permissions = ref<Permission[]>(Array.isArray(props.permissions) ? props.permissions : []);
const showModal = ref(false);
const selectedPermission = ref<Permission | null>(null);
const isCreating = ref(false);
const errors = ref<FormErrors>({});
const form = ref({
    name: '',
    description: '',
    roles: [] as string[]
});

const breadcrumbs = [
    { text: 'Accueil', href: '/', title: 'Accueil' },
    { text: 'Permissions', href: '/permissions', title: 'Permissions' }
];

const toggleMenu = (permission: Permission, event: Event): void => {
    event.stopPropagation();
    permissions.value.forEach(p => {
        if (p.id !== permission.id) {
            p.showMenu = false;
        }
    });
    permission.showMenu = !permission.showMenu;
};

const closeModal = (): void => {
    showModal.value = false;
    selectedPermission.value = null;
    form.value = {
        name: '',
        description: '',
        roles: []
    };
    errors.value = {};
};

const openCreateModal = () => {
    isCreating.value = true;
    showModal.value = true;
};

const editPermission = (permission: Permission) => {
    selectedPermission.value = permission;
    form.value = {
        name: permission.name,
        description: permission.description,
        roles: [...permission.roles]
    };
    isCreating.value = false;
    showModal.value = true;
    permission.showMenu = false;
};

const deletePermission = (permission: Permission) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette permission ?')) {
        router.delete(`/permissions/${permission.id}`, {
            onSuccess: () => {
                window.location.reload();
            }
        });
    }
};

const savePermission = () => {
    if (isCreating.value) {
        router.post('/permissions', form.value, {
            onSuccess: () => {
                window.location.reload();
            },
            onError: (e) => {
                errors.value = e;
            }
        });
    } else if (selectedPermission.value) {
        router.put(`/permissions/${selectedPermission.value.id}`, form.value, {
            onSuccess: () => {
                window.location.reload();
            },
            onError: (e) => {
                errors.value = e;
            }
        });
    }
};
</script>
