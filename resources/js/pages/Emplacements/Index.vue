<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Emplacements</h1>
      <Link
        :href="route('emplacements.create')"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      >
        Nouvel Emplacement
      </Link>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nom
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Description
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Site
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="emplacement in emplacements" :key="emplacement.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ emplacement.name }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-900">{{ emplacement.description }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ emplacement.site?.name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Link
                :href="route('emplacements.edit', emplacement.id)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Modifier
              </Link>
              <button
                @click="deleteEmplacement(emplacement.id)"
                class="text-red-600 hover:text-red-900"
              >
                Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const emplacements = ref([])

const fetchEmplacements = async () => {
  try {
    const response = await axios.get(route('api.emplacements.index'))
    emplacements.value = response.data
  } catch (error) {
    console.error('Erreur lors de la récupération des emplacements:', error)
  }
}

const deleteEmplacement = async (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet emplacement ?')) {
    try {
      await axios.delete(route('api.emplacements.destroy', id))
      await fetchEmplacements()
    } catch (error) {
      console.error('Erreur lors de la suppression:', error)
    }
  }
}

onMounted(() => {
  fetchEmplacements()
})
</script> 