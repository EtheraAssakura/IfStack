<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Eye, MoreVertical, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Location {
    id: number;
    name: string;
    description: string;
    stocks: Stock[];
}

interface Stock {
    id: number;
    estimated_quantity: number;
    local_alert_threshold: number;
    supply: {
        id: number;
        name: string;
        reference: string;
    };
}

interface Site {
    id: number;
    name: string;
    address: string;
    city: string;
    postal_code: string;
    phone: string | null;
    email: string | null;
    plan_path: string | null;
    locations: Location[];
    stocks: Stock[];
}

interface Props {
    site: Site;
}

const props = defineProps<Props>();

const editDialog = ref<InstanceType<typeof Dialog> | null>(null);
const editingLocation = ref<Location | null>(null);
const isEditDialogOpen = ref(false);

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Établissements',
        href: '/sites',
    },
    {
        title: props.site.name,
        href: `/sites/${props.site.id}`,
    },
];

const locationForm = useForm({
    name: '',
    description: '',
    site_id: props.site.id,
});

const editLocationForm = useForm({
    name: '',
    description: '',
});

const handleCreateLocation = () => {
    locationForm.post(route('sites.locations.store', props.site.id), {
        onSuccess: () => {
            locationForm.reset();
        },
    });
};

const handleEditLocation = (locationId: number) => {
    editLocationForm.put(route('sites.locations.update', [props.site.id, locationId]), {
        onSuccess: () => {
            isEditDialogOpen.value = false;
            editingLocation.value = null;
        },
    });
};

const handleDeleteLocation = (locationId: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet emplacement ?')) {
        router.delete(route('sites.locations.destroy', { site: props.site.id, location: locationId }));
    }
};

const initEditForm = (location: Location) => {
    editingLocation.value = location;
    editLocationForm.name = location.name;
    editLocationForm.description = location.description;
    isEditDialogOpen.value = true;
};


</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>{{ site.name }} - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ site.name }}</h1>
                        </div>
                        <div class="flex items-center gap-4">
                            <Button variant="outline" as-child>
                                <Link :href="route('sites.index')">
                                    Retour
                                </Link>
                            </Button>
                            <Button as-child>
                                <Link :href="route('sites.edit', site.id)">
                                    Modifier
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
                                    <dd class="mt-1 text-sm text-gray-900">{{ site.address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ville</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ site.city }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Code Postal</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ site.postal_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ site.phone ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ site.email ?? '-' }}</dd>
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
                                    <DialogContent class="sm:max-w-[425px]" description="Formulaire de création d'un nouvel emplacement">
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
                            <div v-if="site.locations.length === 0" class="text-center py-8 text-sm text-gray-500">
                                Aucun emplacement disponible
                            </div>
                            <div v-else class="space-y-4">
                                <Dialog v-model:open="isEditDialogOpen">
                                    <DialogContent class="sm:max-w-[425px]">
                                        <DialogHeader>
                                            <DialogTitle>Modifier l'emplacement</DialogTitle>
                                        </DialogHeader>
                                        <form v-if="editingLocation" @submit.prevent="handleEditLocation(editingLocation.id)" class="space-y-4">
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

                                <div v-for="location in site.locations" :key="location.id" class="p-4 bg-white rounded-lg shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-base font-medium text-gray-900">{{ location.name }}</h3>
                                        <div class="flex items-center gap-2">
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button variant="ghost" size="icon" class="h-8 w-8">
                                                        <MoreVertical class="h-4 w-4" />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <DropdownMenuItem asChild>
                                                        <button 
                                                            class="flex w-full items-center gap-2"
                                                            @click="initEditForm(location)"
                                                        >
                                                            <Pencil class="h-4 w-4" />
                                                            Modifier
                                                        </button>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem @click="handleDeleteLocation(location.id)" class="text-red-500 focus:text-red-500">
                                                        <Trash2 class="h-4 w-4" />
                                                        Supprimer
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </div>
                                    </div>
                                    <p v-if="location.description" class="mt-1 text-sm text-gray-500">{{ location.description }}</p>
                                    <div class="mt-4">
                                        <Button variant="outline" size="sm" as-child>
                                            <Link :href="route('sites.locations.show', [props.site.id, location.id])" class="flex items-center gap-2">
                                                <Eye class="h-4 w-4" />
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
                <div v-if="site.plan_path" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Plan de l'établissement</h2>
                    <div class="relative aspect-video">
                        <img
                            :src="`/storage/${site.plan_path}`"
                            :alt="`Plan de ${site.name}`"
                            class="max-w-full rounded-lg shadow-md object-contain w-full h-full"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>

