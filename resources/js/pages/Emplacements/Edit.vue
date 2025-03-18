<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">Modifier l'Emplacement</h1>

      <form @submit.prevent="submit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
            Nom
          </label>
          <input
            v-model="form.name"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="name"
            type="text"
            required
          />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
            Description
          </label>
          <textarea
            v-model="form.description"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="description"
            rows="3"
          ></textarea>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="site_id">
            Site
          </label>
          <select
            v-model="form.site_id"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="site_id"
            required
          >
            <option value="">Sélectionnez un site</option>
            <option v-for="site in sites" :key="site.id" :value="site.id">
              {{ site.name }}
            </option>
          </select>
        </div>

        <div class="flex items-center justify-between">
          <Link
            :href="route('emplacements.index')"
            class="text-gray-600 hover:text-gray-800"
          >
            Annuler
          </Link>
          <button
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            type="submit"
          >
            Mettre à jour
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const props = defineProps({
  emplacement: {
    type: Object,
    required: true
  }
})

const sites = ref([])
const form = useForm({
  name: props.emplacement.name,
  description: props.emplacement.description,
  site_id: props.emplacement.site_id
})

const fetchSites = async () => {
  try {
    const response = await axios.get(route('api.sites.index'))
    sites.value = response.data
  } catch (error) {
    console.error('Erreur lors de la récupération des sites:', error)
  }
}

const submit = () => {
  form.put(route('emplacements.update', props.emplacement.id))
}

onMounted(() => {
  fetchSites()
})
</script> 