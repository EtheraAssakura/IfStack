<template>
  <AppLayout title="Gestion des emplacements">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Gestion des emplacements
        </h2>
        <Link
          :href="route('locations.create')"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
        >
          Nouvel emplacement
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Messages de succès/erreur -->
            <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
              {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash.error" class="mb-4 p-4 bg-red-100 text-red-700 rounded">
              {{ $page.props.flash.error }}
            </div>

            <!-- Table des emplacements -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Photo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nom
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Site
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Description
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Stocks
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="location in locations" :key="location.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div v-if="location.photo_url" class="h-10 w-10">
                        <img :src="location.photo_url" :alt="location.name" class="h-10 w-10 rounded-full object-cover" />
                      </div>
                      <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <MapPinIcon class="h-6 w-6 text-gray-400" />
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <Link :href="route('sites.locations.show', [location.site_id, location.id])" class="text-indigo-600 hover:text-indigo-900">
                        {{ location.name }}
                      </Link>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ location.site }}</td>
                    <td class="px-6 py-4">{{ location.description || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ location.stock_items_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                      <Link
                        :href="route('locations.edit', location.id)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        Modifier
                      </Link>
                      <button
                        @click="deleteLocation(location)"
                        class="text-red-600 hover:text-red-900"
                        :disabled="location.stock_items_count > 0"
                      >
                        Supprimer
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { MapPinIcon } from '@heroicons/vue/24/outline/index.js';
import type { Page } from '@inertiajs/core';
import { Link, useForm } from '@inertiajs/vue3';

interface Props {
  locations: Array<{
    id: number
    name: string
    description: string | null
    site: string
    photo_url: string | null
    stock_items_count: number
    site_id: number
  }>
}

interface PageProps {
  flash: {
    success?: string
    error?: string
  }
  auth: {
    user: any
  }
  [key: string]: unknown
}

defineProps<Props>();

declare const $page: Page<PageProps>;

const deleteLocation = (location: Props['locations'][0]) => {
  if (location.stock_items_count > 0) {
    return
  }

  if (confirm(`Êtes-vous sûr de vouloir supprimer l'emplacement "${location.name}" ?`)) {
    useForm({}).delete(route('locations.destroy', location.id))
  }
}
</script>
