<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Établissements</h1>
      <Link
        :href="route('etablissements.create')"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      >
        Nouvel Établissement
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
              Email
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Téléphone
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="etablissement in etablissements" :key="etablissement.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ etablissement.name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ etablissement.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ etablissement.phone }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Link
                :href="route('etablissements.edit', etablissement.id)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Modifier
              </Link>
              <button
                @click="deleteEtablissement(etablissement.id)"
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

const etablissements = ref([])

const fetchEtablissements = async () => {
  try {
    const response = await axios.get(route('api.etablissements.index'))
    etablissements.value = response.data
  } catch (error) {
    console.error('Erreur lors de la récupération des établissements:', error)
  }
}

const deleteEtablissement = async (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet établissement ?')) {
    try {
      await axios.delete(route('api.etablissements.destroy', id))
      await fetchEtablissements()
    } catch (error) {
      console.error('Erreur lors de la suppression:', error)
    }
  }
}

onMounted(() => {
  fetchEtablissements()
})
</script> 