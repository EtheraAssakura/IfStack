<template>
  <AppLayout title="Modifier la catégorie">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Modifier la catégorie
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="form.put(route('categories.update', category.id))">
              <div class="mb-4">
                <InputLabel for="name" value="Nom" />
                <TextInput
                  id="name"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.name"
                  required
                />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>

              <div class="mb-4">
                <InputLabel for="description" value="Description" />
                <textarea
                  id="description"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                  v-model="form.description"
                  rows="3"
                />
                <InputError class="mt-2" :message="form.errors.description" />
              </div>

              <div class="flex items-center justify-end mt-4">
                <Link
                  :href="route('categories.index')"
                  class="mr-4 text-gray-600 hover:text-gray-900"
                >
                  Annuler
                </Link>
                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  Mettre à jour
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  category: {
    id: number
    name: string
    description: string | null
  }
}>()

const form = useForm({
  name: props.category.name,
  description: props.category.description || ''
})
</script>
