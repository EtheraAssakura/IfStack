<template>
    <AppLayout :breadcrumbs="breadcrumbs" title="Modifier le rôle">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Modifier le rôle
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200/50">
                    <form @submit.prevent="form.put(route('roles.update', role.id))" class="p-6">
                        <div class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Nom" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Description" />
                                <TextInput
                                    id="description"
                                    v-model="form.description"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel value="Permissions" />
                                <div class="mt-2 space-y-2">
                                    <label
                                        v-for="permission in permissions"
                                        :key="permission.id"
                                        class="inline-flex items-center gap-2 rounded-xl bg-gray-50 px-4 py-2.5"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="permission.id"
                                            v-model="form.permissions"
                                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                        >
                                        <span class="text-sm text-gray-700">
                                            {{ permission.description }}
                                        </span>
                                    </label>
                                </div>
                                <InputError :message="form.errors.permissions" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-4">
                            <Link
                                :href="route('roles.index')"
                                class="inline-flex items-center gap-2 text-gray-500 transition-colors hover:text-gray-700"
                            >
                                Annuler
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                Enregistrer les modifications
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types/BreadcrumbItemType';
import { Link, useForm } from '@inertiajs/vue3';

interface Permission {
    id: number;
    name: string;
    description: string;
}

interface Role {
    id: number;
    name: string;
    description: string | null;
    permissions: Permission[];
}

const props = defineProps<{
    role: Role;
    permissions: Permission[];
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Rôles',
        href: route('roles.index'),
    },
    {
        title: 'Modifier',
        href: route('roles.edit', props.role.id),
    },
];

const form = useForm({
    name: props.role.name,
    description: props.role.description || '',
    permissions: props.role.permissions.map(p => p.id),
});
</script>
