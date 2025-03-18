<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Détails de l'Emplacement</h1>
        <div class="space-x-4">
          <Link
            :href="route('emplacements.edit', emplacement.id)"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            Modifier
          </Link>
          <Link
            :href="route('emplacements.index')"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
          >
            Retour
          </Link>
        </div>
      </div>

      <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
          <h2 class="text-lg font-semibold text-gray-700">Nom</h2>
          <p class="text-gray-900">{{ emplacement.name }}</p>
        </div>

        <div class="mb-4">
          <h2 class="text-lg font-semibold text-gray-700">Description</h2>
          <p class="text-gray-900">{{ emplacement.description || 'Aucune description' }}</p>
        </div>

        <div class="mb-4">
          <h2 class="text-lg font-semibold text-gray-700">Site</h2>
          <p class="text-gray-900">{{ emplacement.site?.name || 'Non assigné' }}</p>
        </div>

        <div class="mb-4">
          <h2 class="text-lg font-semibold text-gray-700">Date de création</h2>
          <p class="text-gray-900">{{ formatDate(emplacement.created_at) }}</p>
        </div>

        <div class="mb-4">
          <h2 class="text-lg font-semibold text-gray-700">Dernière mise à jour</h2>
          <p class="text-gray-900">{{ formatDate(emplacement.updated_at) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  emplacement: {
    type: Object,
    required: true
  }
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script> 