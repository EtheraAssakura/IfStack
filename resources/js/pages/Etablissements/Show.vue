<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import FileUpload from '@/components/ui/file-upload.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';

interface Emplacement {
    id: number;
    name: string;
    description: string;
    stocks: Stock[];
}

interface Stock {
    id: number;
    estimated_quantity: number;
    local_alert_threshold: number;
    fourniture: {
        id: number;
        name: string;
        reference: string;
    };
}

interface Etablissement {
    id: number;
    name: string;
    address: string;
    city: string;
    postal_code: string;
    phone: string | null;
    email: string | null;
    plan_path: string | null;
    emplacements: Emplacement[];
    stocks: Stock[];
}

interface Props {
    etablissement: Etablissement;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Établissements',
        href: '/etablissements',
    },
    {
        title: props.etablissement.name,
        href: `/etablissements/${props.etablissement.id}`,
    },
];

const locationForm = useForm({
    name: '',
    description: '',
    etablissement_id: props.etablissement.id,
    photo: null as File | null,
});

const editLocationForm = useForm({
    name: '',
    description: '',
    photo: null as File | null,
});

const handleCreateLocation = () => {
    locationForm.post(route('etablissements.locations.store', props.etablissement.id), {
        onSuccess: () => {
            locationForm.reset();
        },
    });
};

const handleEditLocation = (locationId: number) => {
    editLocationForm.post(route('etablissements.locations.update', [props.etablissement.id, locationId]));
};

const handleDeleteLocation = (locationId: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet emplacement ?')) {
        router.delete(route('etablissements.locations.destroy', [props.etablissement.id, locationId]));
    }
};

const initEditForm = (emplacement: Emplacement) => {
    editLocationForm.name = emplacement.name;
    editLocationForm.description = emplacement.description;
};
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>{{ etablissement.name }} - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ etablissement.name }}</h1>
                        </div>
                        <div class="flex items-center gap-4">
                            <Button variant="outline" as-child>
                                <Link :href="route('etablissements.index')">
                                    Retour
                                </Link>
                            </Button>
                            <Button as-child>
                                <Link :href="route('etablissements.edit', etablissement.id)">
                                    Modifier
                                </Link>
                            </Button>
                            <Button variant="destructive" as-child>
                                <Link :href="route('etablissements.destroy', etablissement.id)" method="delete" as="button">
                                    Supprimer
                                </Link>
                            </Button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informations générales -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h2>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ville</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.city }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Code Postal</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.postal_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.phone ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ etablissement.email ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Emplacements -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-medium text-gray-900">Emplacements</h2>
                                <Dialog>
                                    <DialogTrigger asChild>
                                        <Button size="sm">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Nouvel emplacement
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>Nouvel emplacement</DialogTitle>
                                        </DialogHeader>
                                        <form @submit.prevent="handleCreateLocation" class="space-y-4">
                                            <div class="space-y-2">
                                                <Label for="name">Nom</Label>
                                                <Input
                                                    id="name"
                                                    v-model="locationForm.name"
                                                    type="text"
                                                    required
                                                />
                                                <div v-if="locationForm.errors.name" class="text-sm text-red-600">
                                                    {{ locationForm.errors.name }}
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <Label for="description">Description</Label>
                                                <Textarea
                                                    id="description"
                                                    v-model="locationForm.description"
                                                    rows="3"
                                                />
                                                <div v-if="locationForm.errors.description" class="text-sm text-red-600">
                                                    {{ locationForm.errors.description }}
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <Label for="photo">Photo</Label>
                                                <FileUpload
                                                    v-model="locationForm.photo"
                                                    accept="image/*"
                                                    :max-size="2 * 1024 * 1024"
                                                />
                                                <div v-if="locationForm.errors.photo" class="text-sm text-red-600">
                                                    {{ locationForm.errors.photo }}
                                                </div>
                                            </div>

                                            <div class="flex justify-end gap-4">
                                                <DialogTrigger asChild>
                                                    <Button type="button" variant="outline">
                                                        Annuler
                                                    </Button>
                                                </DialogTrigger>
                                                <Button
                                                    type="submit"
                                                    :disabled="locationForm.processing"
                                                >
                                                    Créer
                                                </Button>
                                            </div>
                                        </form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                            <div v-if="etablissement.emplacements.length === 0" class="text-center py-8 text-sm text-gray-500">
                                Aucun emplacement disponible
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="emplacement in etablissement.emplacements" :key="emplacement.id" class="p-4 bg-white rounded-lg shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-base font-medium text-gray-900">{{ emplacement.name }}</h3>
                                        <div class="flex items-center gap-2">
                                            <Dialog>
                                                <DialogTrigger asChild>
                                                    <Button variant="ghost" size="sm" @click="initEditForm(emplacement)">
                                                        <Pencil class="h-4 w-4" />
                                                    </Button>
                                                </DialogTrigger>
                                                <DialogContent>
                                                    <DialogHeader>
                                                        <DialogTitle>Modifier l'emplacement</DialogTitle>
                                                    </DialogHeader>
                                                    <form @submit.prevent="handleEditLocation(emplacement.id)" class="space-y-4">
                                                        <div class="space-y-2">
                                                            <Label for="edit-name">Nom</Label>
                                                            <Input
                                                                id="edit-name"
                                                                v-model="editLocationForm.name"
                                                                type="text"
                                                                required
                                                            />
                                                            <div v-if="editLocationForm.errors.name" class="text-sm text-red-600">
                                                                {{ editLocationForm.errors.name }}
                                                            </div>
                                                        </div>

                                                        <div class="space-y-2">
                                                            <Label for="edit-description">Description</Label>
                                                            <Textarea
                                                                id="edit-description"
                                                                v-model="editLocationForm.description"
                                                                rows="3"
                                                            />
                                                            <div v-if="editLocationForm.errors.description" class="text-sm text-red-600">
                                                                {{ editLocationForm.errors.description }}
                                                            </div>
                                                        </div>

                                                        <div class="space-y-2">
                                                            <Label for="edit-photo">Photo</Label>
                                                            <FileUpload
                                                                v-model="editLocationForm.photo"
                                                                accept="image/*"
                                                                :max-size="2 * 1024 * 1024"
                                                                :current-file="emplacement.photo_path"
                                                            />
                                                            <div v-if="editLocationForm.errors.photo" class="text-sm text-red-600">
                                                                {{ editLocationForm.errors.photo }}
                                                            </div>
                                                        </div>

                                                        <div class="flex justify-end gap-4">
                                                            <DialogTrigger asChild>
                                                                <Button type="button" variant="outline">
                                                                    Annuler
                                                                </Button>
                                                            </DialogTrigger>
                                                            <Button
                                                                type="submit"
                                                                :disabled="editLocationForm.processing"
                                                            >
                                                                Mettre à jour
                                                            </Button>
                                                        </div>
                                                    </form>
                                                </DialogContent>
                                            </Dialog>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="handleDeleteLocation(emplacement.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-red-500" />
                                            </Button>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ emplacement.description }}
                                    </p>
                                    <div class="mt-4">
                                        <Button variant="outline" size="sm" as-child>
                                            <Link :href="route('etablissements.locations.show', [props.etablissement.id, emplacement.id])">
                                                Voir l'emplacement
                                            </Link>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan -->
                <div v-if="etablissement.plan_path" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Plan de l'établissement</h2>
                    <div class="relative aspect-video">
                        <img
                            :src="`/storage/${etablissement.plan_path}`"
                            :alt="`Plan de ${etablissement.name}`"
                            class="max-w-full rounded-lg shadow-md object-contain w-full h-full"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>
