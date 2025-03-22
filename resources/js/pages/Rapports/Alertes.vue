<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h1 class="text-2xl font-semibold mb-6">Alertes de Stock</h1>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fourniture</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité Actuelle</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seuil d'Alerte</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="alerte in alertes" :key="alerte.id" class="bg-red-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ alerte.fourniture }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ alerte.quantite }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ alerte.seuil_alerte }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ alerte.location }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  alertes: {
    type: Array,
    required: true
  }
})
</script> 