<template>
  <AppLayout title="Gestion des catégories">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Gestion des catégories
        </h2>
        <Link
          :href="route('categories.create')"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
        >
          Nouvelle catégorie
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

            <!-- Table des catégories -->
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
                    Nombre de fournitures
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="category in categories" :key="category.id">
                  <td class="px-6 py-4 whitespace-nowrap">{{ category.name }}</td>
                  <td class="px-6 py-4">{{ category.description || '-' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ category.supplies_count }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link
                      :href="route('categories.edit', category.id)"
                      class="text-indigo-600 hover:text-indigo-900 mr-3"
                    >
                      Modifier
                    </Link>
                    <button
                      @click="deleteCategory(category)"
                      class="text-red-600 hover:text-red-900"
                      :disabled="category.supplies_count > 0"
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
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  categories: Array
})

const deleteCategory = (category) => {
  if (category.supplies_count > 0) {
    return
  }

  if (confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${category.name}" ?`)) {
    useForm().delete(route('categories.destroy', category.id))
  }
}
</script>
