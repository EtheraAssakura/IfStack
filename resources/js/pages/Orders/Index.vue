<template>
  <AppLayout title="Commandes" :breadcrumbs="breadcrumbs">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Liste des commandes
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between mb-6">
              <Link
                :href="route('orders.create')"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle commande
              </Link>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Numéro
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Fournisseur
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Statut
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="order in orders" :key="order.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ order.order_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ order.supplier.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ order.order_date }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="{
                          'bg-yellow-100 text-yellow-800': order.status === 'pending',
                          'bg-green-100 text-green-800': order.status === 'validated',
                          'bg-red-100 text-red-800': order.status === 'cancelled'
                        }"
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      >
                        {{ order.status === 'pending' ? 'En attente' : 
                           order.status === 'validated' ? 'Validée' : 
                           order.status === 'cancelled' ? 'Annulée' : order.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end gap-2">
                        <DropdownMenu>
                          <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon" class="h-8 w-8">
                              <MoreVertical class="h-4 w-4" />
                            </Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent align="end">
                            <DropdownMenuItem asChild>
                              <Link :href="route('orders.show', order.id)" class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                  <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                Voir
                              </Link>
                            </DropdownMenuItem>

                            <template v-if="order.status === 'pending'">
                              <DropdownMenuItem asChild>
                                <Link :href="route('orders.edit', order.id)" class="flex items-center gap-2">
                                  <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                  </svg>
                                  Modifier
                                </Link>
                              </DropdownMenuItem>

                              <DropdownMenuItem @click="validateOrder(order)" class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Valider
                              </DropdownMenuItem>

                              <DropdownMenuItem @click="cancelOrder(order)" class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Annuler
                              </DropdownMenuItem>
                            </template>

                            <DropdownMenuItem @click="exportToExcel(order)" class="flex items-center gap-2">
                              <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                              </svg>
                              Exporter en Excel
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="exportToCSV(order)" class="flex items-center gap-2">
                              <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                              </svg>
                              Exporter en CSV
                            </DropdownMenuItem>
                          </DropdownMenuContent>
                        </DropdownMenu>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import AppLayout from '@/Layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import { MoreVertical } from 'lucide-vue-next'

const props = defineProps({
  orders: Array,
})

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Commandes', href: route('orders.index') },
]

const validateOrder = (order) => {
  router.post(route('orders.validate', order.id))
}

const cancelOrder = (order) => {
  router.post(route('orders.cancel', order.id))
}

const exportToExcel = async (order) => {
  try {
    const response = await axios.get(route('orders.export', order.id), {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `order_${order.order_number}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Error exporting to Excel:', error)
    alert('Une erreur est survenue lors de l\'exportation. Veuillez réessayer.')
  }
}

const exportToCSV = async (order) => {
      
      try {
        // Créer le contenu CSV avec uniquement la référence fournisseur et la quantité
        const csvContent = order.items
          .map(item => [
            item.supply.supplier_reference || '',
            item.quantity
          ])
          .map(row => row.join(';'))
          .join('\n')

        // Créer le blob et le lien de téléchargement
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `order_${order.order_number}_supplier_refs.csv`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)
        
      } catch (error) {
        console.error('Error exporting to CSV:', error)
        alert('Une erreur est survenue lors de l\'exportation. Veuillez réessayer.')
      }
    }
</script> 