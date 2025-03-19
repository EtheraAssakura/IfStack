<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useDropZone } from '@vueuse/core';
import { ref } from 'vue';

interface Etablissement {
    id: number;
    name: string;
    address: string;
    city: string;
    postal_code: string;
    phone: string | null;
    email: string | null;
    plan_path: string | null;
}

interface Props {
    etablissement: Etablissement;
}

const props = defineProps<Props>();
const dropZone = ref<HTMLDivElement>();
const isDragging = ref(false);
const preview = ref<string | null>(props.etablissement.plan_path);

const form = useForm({
    name: props.etablissement.name,
    address: props.etablissement.address,
    city: props.etablissement.city,
    postal_code: props.etablissement.postal_code,
    phone: props.etablissement.phone ?? '',
    email: props.etablissement.email ?? '',
    plan: null as File | null,
});

const { isOverDropZone } = useDropZone(dropZone, {
    onDrop: (files: File[] | null) => {
        if (!files) return;
        const file = files[0];
        if (file && file.type.startsWith('image/')) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Le plan ne doit pas dépasser 5MB');
                return;
            }
            if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                alert('Le format du plan doit être JPEG, PNG ou GIF');
                return;
            }
            form.plan = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.value = e.target?.result as string;
            };
            reader.readAsDataURL(file);
        }
    },
    onEnter: () => {
        isDragging.value = true;
    },
    onLeave: () => {
        isDragging.value = false;
    },
});

const handleFileInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (file && file.type.startsWith('image/')) {
        if (file.size > 5 * 1024 * 1024) {
            alert('Le plan ne doit pas dépasser 5MB');
            return;
        }
        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            alert('Le format du plan doit être JPEG, PNG ou GIF');
            return;
        }
        form.plan = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.plan = null;
    preview.value = null;
};

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Établissements',
        href: '/etablissements',
    },
    {
        title: props.etablissement.name,
        href: `/etablissements/${props.etablissement.id}`,
    },
    {
        title: 'Modifier',
        href: `/etablissements/${props.etablissement.id}/edit`,
    },
];

const handleSubmit = () => {
    const data = {
        _method: 'PUT',
        name: form.name,
        address: form.address,
        city: form.city,
        postal_code: form.postal_code,
        phone: form.phone || '',
        email: form.email || '',
        plan: form.plan
    };

    form.transform(data => ({
        ...data
    })).post(route('etablissements.update', props.etablissement.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            preview.value = null;
        },
        onError: (errors: Record<string, string>) => {
            console.error('Erreurs de validation:', errors);
        }
    });
};
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Modifier {{ etablissement.name }} - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-semibold mb-6">Modifier {{ etablissement.name }}</h1>

                        <form @submit.prevent="handleSubmit" class="space-y-6" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="name">Nom</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                    />
                                    <div v-if="form.errors.name" class="text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="address">Adresse</Label>
                                    <Input
                                        id="address"
                                        v-model="form.address"
                                        type="text"
                                        required
                                    />
                                    <div v-if="form.errors.address" class="text-sm text-red-600">
                                        {{ form.errors.address }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="city">Ville</Label>
                                    <Input
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                        required
                                    />
                                    <div v-if="form.errors.city" class="text-sm text-red-600">
                                        {{ form.errors.city }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="postal_code">Code Postal</Label>
                                    <Input
                                        id="postal_code"
                                        v-model="form.postal_code"
                                        type="text"
                                        required
                                    />
                                    <div v-if="form.errors.postal_code" class="text-sm text-red-600">
                                        {{ form.errors.postal_code }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="phone">Téléphone</Label>
                                    <Input
                                        id="phone"
                                        v-model="form.phone"
                                        type="tel"
                                    />
                                    <div v-if="form.errors.phone" class="text-sm text-red-600">
                                        {{ form.errors.phone }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                    />
                                    <div v-if="form.errors.email" class="text-sm text-red-600">
                                        {{ form.errors.email }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="plan">Plan</Label>
                                    <div v-if="preview" class="mb-2">
                                        <img :src="preview" alt="Aperçu du plan" class="h-20 w-20 object-cover rounded" />
                                        <Button type="button" variant="destructive" size="sm" class="mt-2" @click="removeImage">
                                            Supprimer le plan
                                        </Button>
                                    </div>
                                    <div
                                        ref="dropZone"
                                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                                        :class="{ 'border-indigo-500': isOverDropZone }"
                                    >
                                        <div class="space-y-1 text-center">
                                            <svg
                                                class="mx-auto h-12 w-12 text-gray-400"
                                                stroke="currentColor"
                                                fill="none"
                                                viewBox="0 0 48 48"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label
                                                    for="plan"
                                                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                                                >
                                                    <span>Télécharger un fichier ou glisser-déposer</span>
                                                    <input
                                                        id="plan"
                                                        name="plan"
                                                        type="file"
                                                        class="sr-only"
                                                        accept="image/*"
                                                        @change="handleFileInput"
                                                    />
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG jusqu'à 5MB</p>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.plan" class="text-sm text-red-600">
                                        {{ form.errors.plan }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Button
                                    variant="outline"
                                    @click="router.visit(route('etablissements.index'))"
                                >
                                    Annuler
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    Mettre à jour
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>
