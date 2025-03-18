<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">Modifier l'Établissement</h1>

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
          >
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
            Email
          </label>
          <input
            v-model="form.email"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="email"
            type="email"
          >
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
            Téléphone
          </label>
          <input
            v-model="form.phone"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="phone"
            type="tel"
          >
        </div>

        <div class="flex items-center justify-between">
          <button
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            type="submit"
          >
            Mettre à jour
          </button>
          <Link
            :href="route('etablissements.index')"
            class="text-gray-600 hover:text-gray-800"
          >
            Annuler
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const props = defineProps({
  etablissement: {
    type: Object,
    required: true
  }
})

const form = ref({
  name: '',
  email: '',
  phone: ''
})

onMounted(() => {
  form.value = {
    name: props.etablissement.name,
    email: props.etablissement.email,
    phone: props.etablissement.phone
  }
})

const submit = async () => {
  try {
    await axios.put(route('api.etablissements.update', props.etablissement.id), form.value)
    window.location.href = route('etablissements.index')
  } catch (error) {
    console.error('Erreur lors de la mise à jour:', error)
  }
}
</script> 