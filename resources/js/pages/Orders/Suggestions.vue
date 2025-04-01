<template>
  <AppSidebarLayout title="Suggested Orders">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Suggested Orders
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between mb-4">
              <Link
                :href="route('orders.index')"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
              >
                Back to Orders
              </Link>
            </div>

            <div v-if="suggestions.length === 0" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
              <p>No supplies need to be ordered at this time.</p>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Reference
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Location
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Current Stock
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Threshold
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Needed
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Supplier
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="suggestion in suggestions" :key="suggestion.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ suggestion.reference }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ suggestion.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ suggestion.location }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ suggestion.current_stock }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ suggestion.threshold }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ suggestion.needed_quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ suggestion.supplier?.name || 'No supplier assigned' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <template v-if="suggestion.supplier">
                        <Link
                          :href="route('orders.create', { supplier_id: suggestion.supplier.id, supply_id: suggestion.id })"
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          Create Order
                        </Link>
                      </template>
                      <span v-else class="text-gray-500">
                        No supplier assigned
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Link } from '@inertiajs/vue3'

interface Suggestion {
  id: number
  reference: string
  name: string
  location: string
  current_stock: number
  threshold: number
  needed_quantity: number
  supplier?: {
    id: number
    name: string
  }
}

defineProps<{
  suggestions: Suggestion[]
}>()
</script> 