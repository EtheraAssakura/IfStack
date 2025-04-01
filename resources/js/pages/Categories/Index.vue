<template>
  <AppSideBarLayout title="Gestion des catégories">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestion des catégories
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between items-center mb-6">
            <div class="flex-1 max-w-lg">
              <input
                type="text"
                v-model="search"
                placeholder="Rechercher une catégorie..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
            <Link
              :href="route('categories.create')"
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
            >
              Nouvelle catégorie
            </Link>
          </div>

          <div class="overflow-x-auto">
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
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="category in filteredCategories" :key="category.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ category.name }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">
                      {{ category.description }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <Link
                      :href="route('categories.edit', category.id)"
                      class="text-indigo-600 hover:text-indigo-900 mr-4"
                    >
                      Modifier
                    </Link>
                    <button
                      @click="deleteCategory(category)"
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
      </div>
    </div>
  </AppSideBarLayout>
</template>

<script setup lang="ts">
import AppSideBarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

interface Category {
  id: number;
  name: string;
  description: string;
}

const props = defineProps<{
  categories: Category[];
}>();

const search = ref('');

const filteredCategories = computed(() => {
  if (!search.value) return props.categories;

  const searchLower = search.value.toLowerCase();
  return props.categories.filter(category => 
    category.name.toLowerCase().includes(searchLower) ||
    category.description.toLowerCase().includes(searchLower)
  );
});

const deleteCategory = (category: Category) => {
  if (confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${category.name}" ?`)) {
    router.delete(route('categories.destroy', category.id));
  }
};
</script>
