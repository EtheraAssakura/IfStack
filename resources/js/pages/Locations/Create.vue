<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <Head>
      <title>Nouvel emplacement - ISFAC</title>
    </Head>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h1 class="text-2xl font-semibold mb-6">Nouvel emplacement</h1>

            <form @submit.prevent="handleSubmit" class="space-y-6">
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
                  <Label for="site_id">Site</Label>
                  <select
                    id="site_id"
                    v-model="form.site_id"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    required
                  >
                    <option value="">Sélectionnez un site</option>
                    <option
                      v-for="site in sites"
                      :key="site.id"
                      :value="site.id"
                    >
                      {{ site.name }}
                    </option>
                  </select>
                  <div v-if="form.errors.site_id" class="text-sm text-red-600">
                    {{ form.errors.site_id }}
                  </div>
                </div>

                <div class="space-y-2 md:col-span-2">
                  <Label for="description">Description</Label>
                  <Textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                  />
                  <div v-if="form.errors.description" class="text-sm text-red-600">
                    {{ form.errors.description }}
                  </div>
                </div>

                <div class="space-y-2 md:col-span-2">
                  <Label for="photo">Photo</Label>
                  <FileUpload
                    v-model="form.photo"
                    accept="image/*"
                    :max-size="2 * 1024 * 1024"
                  />
                  <div v-if="form.errors.photo" class="text-sm text-red-600">
                    {{ form.errors.photo }}
                  </div>
                </div>
              </div>

              <div class="flex justify-end gap-4">
                <Button
                  type="button"
                  variant="outline"
                  @click="router.visit(route('locations.index'))"
                >
                  Annuler
                </Button>
                <Button
                  type="submit"
                  :disabled="form.processing"
                >
                  Créer l'emplacement
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import FileUpload from '@/components/ui/file-upload.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';

interface Props {
    sites: Array<{
        id: number;
        name: string;
    }>;
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    description: '',
    site_id: '',
    photo: null as File | null,
});

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Emplacements',
        href: '/locations',
    },
    {
        title: 'Nouvel emplacement',
        href: '/locations/create',
    },
];

const handleSubmit = () => {
    form.post(route('locations.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>
