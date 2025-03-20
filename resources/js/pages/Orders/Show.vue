<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbItems">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Détails de la Commande
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
                Retour aux Commandes
              </Link>
              <div>
                <button
                  @click="exportToCSV"
                  class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded mr-2"
                >
                  Exporter en CSV
                </button>
                <button
                  @click="exportToExcel"
                  class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2"
                >
                  Exporter en Excel
                </button>
                <template v-if="order.status === 'pending'">
                  <Link
                    :href="route('orders.edit', order.id)"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2 inline-block"
                  >
                    Modifier
                  </Link>
                  <form @submit.prevent="validateOrder" class="inline mr-2">
                    <button
                      type="submit"
                      class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                      Valider
                    </button>
                  </form>
                  <form @submit.prevent="cancelOrder" class="inline">
                    <button
                      type="submit"
                      class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                    >
                      Annuler
                    </button>
                  </form>
                </template>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de la Commande</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Numéro de Commande</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ order.order_number }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                    <dd class="mt-1">
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
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Date de Commande</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ order.order_date }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Date de Livraison Prévue</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ order.expected_delivery_date }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Créé par</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ order.user.name }}</dd>
                  </div>
                </dl>
              </div>

              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du Fournisseur</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Nom</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ order.supplier.name }}</dd>
                  </div>
                  <div v-if="order.supplier.catalog_url">
                    <dt class="text-sm font-medium text-gray-500">Catalogue</dt>
                    <dd class="mt-1">
                      <a
                        :href="order.supplier.catalog_url"
                        target="_blank"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        Voir le Catalogue
                      </a>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Articles de la Commande</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Référence
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Référence Fournisseur
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nom
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Conditionnement
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Quantité
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Prix Unitaire
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in order.items" :key="item.id">
                      <td class="px-6 py-4 whitespace-nowrap">
                        {{ item.supply.reference }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        {{ item.supply.supplier_reference || '-' }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        {{ item.supply.name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        {{ item.supply.packaging }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        {{ item.quantity }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        {{ formatPrice(item.unit_price) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        {{ formatPrice(item.quantity * item.unit_price) }}
                      </td>
                    </tr>
                  </tbody>
                  <tfoot class="bg-gray-50">
                    <tr>
                      <td colspan="6" class="px-6 py-3 text-right text-sm font-medium text-gray-500">
                        Montant Total
                      </td>
                      <td class="px-6 py-3 text-sm font-medium text-gray-900">
                        {{ formatPrice(order.total_amount) }}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div v-if="order.deliveries.length > 0">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Livraisons</h3>
              <div class="space-y-4">
                <div
                  v-for="delivery in order.deliveries"
                  :key="delivery.id"
                  class="bg-gray-50 p-4 rounded-lg"
                >
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Date de Livraison</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ delivery.delivery_date }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Statut</dt>
                      <dd class="mt-1">
                        <span
                          :class="{
                            'bg-yellow-100 text-yellow-800': delivery.status === 'pending',
                            'bg-green-100 text-green-800': delivery.status === 'completed',
                            'bg-red-100 text-red-800': delivery.status === 'cancelled'
                          }"
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        >
                          {{ delivery.status === 'pending' ? 'En attente' : 
                             delivery.status === 'completed' ? 'Terminée' : 
                             delivery.status === 'cancelled' ? 'Annulée' : delivery.status }}
                        </span>
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Articles Livrés</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ delivery.items.length }} articles
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Créé par</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ delivery.user.name }}</dd>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { Link, useForm } from '@inertiajs/vue3'
import axios from 'axios'

interface Supply {
  reference: string
  supplier_reference?: string
  name: string
  packaging: string
}

interface OrderItem {
  id: number
  quantity: number
  unit_price: number
  supply: Supply
}

interface User {
  name: string
}

interface Supplier {
  name: string
  catalog_url?: string
}

interface Delivery {
  id: number
  delivery_date: string
  status: 'pending' | 'completed' | 'cancelled'
  items: any[]
  user: User
}

interface Order {
  id: number
  order_number: string
  status: 'pending' | 'validated' | 'cancelled'
  order_date: string
  expected_delivery_date: string
  total_amount: number
  user: User
  supplier: Supplier
  items: OrderItem[]
  deliveries: Delivery[]
}

const props = defineProps<{
  order: Order
}>()

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Commandes',
    href: route('orders.index')
  },
  {
    title: `Commande ${props.order.order_number}`,
    href: route('orders.show', props.order.id)
  }
]

const formatPrice = (price: number): string => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
  }).format(price)
}

const validateOrder = () => {
  useForm({}).post(route('orders.validate', props.order.id))
}

const cancelOrder = () => {
  useForm({}).post(route('orders.cancel', props.order.id))
}

const exportToExcel = async () => {
  try {
    const response = await axios.get(route('orders.export', props.order.id), {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `order_${props.order.order_number}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Error exporting to Excel:', error)
    alert('Une erreur est survenue lors de l\'exportation. Veuillez réessayer.')
  }
}

const exportToCSV = async () => {
  try {
    const csvContent = props.order.items
      .map(item => [
        item.supply.supplier_reference || '',
        item.quantity
      ])
      .map(row => row.join(';'))
      .join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `order_${props.order.order_number}_supplier_refs.csv`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error exporting to CSV:', error)
    alert('Une erreur est survenue lors de l\'exportation. Veuillez réessayer.')
  }
}

// Log des items avec leur conditionnement
console.log('Order items with packaging:', props.order.items.map(item => ({
  name: item.supply.name,
  packaging: item.supply.packaging
})))
</script> 