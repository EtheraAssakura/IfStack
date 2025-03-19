<template>
  <AppLayout :title="'Modifier la Commande'" :breadcrumbs="breadcrumbs">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Modifier la Commande
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between mb-4">
              <Link
                :href="route('orders.show', order.id)"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
              >
                Retour aux Détails
              </Link>
            </div>

            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Fournisseur
                  </label>
                  <select
                    v-model="form.supplier_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  >
                    <option value="">Sélectionner un fournisseur</option>
                    <option
                      v-for="supplier in suppliers"
                      :key="supplier.id"
                      :value="supplier.id"
                    >
                      {{ supplier.name }}
                    </option>
                  </select>
                  <div v-if="errors.supplier_id" class="text-red-500 text-sm mt-1">
                    {{ errors.supplier_id }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Date de Commande
                  </label>
                  <input
                    type="date"
                    v-model="form.order_date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  />
                  <div v-if="errors.order_date" class="text-red-500 text-sm mt-1">
                    {{ errors.order_date }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Date de Livraison Prévue
                  </label>
                  <input
                    type="date"
                    v-model="form.expected_delivery_date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <div v-if="errors.expected_delivery_date" class="text-red-500 text-sm mt-1">
                    {{ errors.expected_delivery_date }}
                  </div>
                </div>
              </div>

              <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-medium text-gray-900">Articles de la Commande</h3>
                  <button
                    type="button"
                    @click="openAddItemModal"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    Ajouter un Article
                  </button>
                </div>
                <div v-if="form.items.length === 0" class="text-center py-4 text-gray-500">
                  Aucun article ajouté. Cliquez sur "Ajouter un Article" pour commencer.
                </div>

                <div v-else class="space-y-4">
                  <div v-for="(item, index) in form.items" :key="index" class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                      <div class="text-sm font-medium text-gray-900">{{ getSupplyName(item.supply_id) }}</div>
                      <div class="text-sm text-gray-500">Quantité: {{ item.quantity }}</div>
                      <div class="text-sm text-gray-500">Prix Unitaire: {{ formatPrice(item.unit_price) }}</div>
                      <div class="text-sm text-gray-500">Conditionnement: {{ getSupplyPackaging(item.supply_id) }}</div>
                      <div class="text-sm font-medium text-gray-900">Total: {{ formatPrice(item.quantity * item.unit_price) }}</div>
                    </div>
                    <div class="flex space-x-2">
                      <button 
                        type="button" 
                        @click="editItem(index)" 
                        class="text-blue-600 hover:text-blue-800"
                      >
                        <Pen class="h-5 w-5" />
                      </button>
                      <button 
                        type="button" 
                        @click="removeItem(index)" 
                        class="text-red-600 hover:text-red-800"
                      >
                        <Trash2 class="h-5 w-5" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                  Mettre à Jour la Commande
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal pour ajouter un article -->
    <div v-if="showAddItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ editingItemIndex !== null ? 'Modifier un Article' : 'Ajouter un Article' }}
          </h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Article</label>
              <select
                v-model="newItem.supply_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
              >
                <option value="">Sélectionner un article</option>
                <option
                  v-for="supply in availableSupplies"
                  :key="supply.id"
                  :value="supply.id"
                >
                  {{ supply.name }} ({{ supply.reference }}) - {{ supply.packaging }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Quantité</label>
              <input
                type="number"
                v-model="newItem.quantity"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                min="1"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Prix Unitaire</label>
              <input
                type="number"
                v-model="newItem.unit_price"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                step="0.01"
                min="0"
                required
              />
            </div>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button @click="closeAddItemModal" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Annuler
          </button>
          <button @click="addItem" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ editingItemIndex !== null ? 'Modifier' : 'Ajouter' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Link, useForm } from '@inertiajs/vue3'
import { Pen, Trash2 } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'

interface Props {
  order: {
    id: number
    supplier_id: number
    order_date: string
    expected_delivery_date: string
    items: Array<{
      supply_id: number
      quantity: number
      unit_price: number
    }>
  }
  suppliers: Array<{
    id: number
    name: string
  }>
  supplies: Array<{
    id: number
    name: string
    reference: string
    packaging: string
    suppliers: Array<{
      id: number
      unit_price: number
    }>
  }>
  errors: Record<string, string>
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
  {
    title: 'Commandes',
    href: route('orders.index'),
  },
  {
    title: 'Commande #' + props.order.id,
    href: route('orders.show', props.order.id),
  },
  {
    title: 'Modifier',
    href: route('orders.edit', props.order.id),
  },
]

const form = useForm({
  supplier_id: props.order.supplier_id,
  order_date: props.order.order_date,
  expected_delivery_date: props.order.expected_delivery_date,
  items: props.order.items,
})

const showAddItemModal = ref(false)
const showEditItemModal = ref(false)
const editingItemIndex = ref<number | null>(null)
const newItem = ref({
  supply_id: '',
  quantity: 1,
  unit_price: 0,
})

const availableSupplies = computed(() => {
  return props.supplies.filter(supply => {
    const notInOrder = !form.items.some(item => item.supply_id === supply.id) || 
      (editingItemIndex.value !== null && form.items[editingItemIndex.value].supply_id === supply.id)
    const hasSelectedSupplier = supply.suppliers.some(s => s.id === form.supplier_id)
    return notInOrder && hasSelectedSupplier
  })
})

const getSupplyName = (supplyId: number) => {
  const supply = props.supplies.find(s => s.id === supplyId)
  return supply ? `${supply.name} (${supply.reference})` : 'Article inconnu'
}

const getSupplyPackaging = (supplyId: number) => {
  const supply = props.supplies.find(s => s.id === supplyId)
  return supply ? supply.packaging : 'Non spécifié'
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
  }).format(price)
}

const openAddItemModal = () => {
  if (!form.supplier_id) {
    alert('Veuillez sélectionner un fournisseur d\'abord')
    return
  }
  showAddItemModal.value = true
  newItem.value = {
    supply_id: '',
    quantity: 1,
    unit_price: 0,
  }
}

const closeAddItemModal = () => {
  showAddItemModal.value = false
  editingItemIndex.value = null
  newItem.value = {
    supply_id: '',
    quantity: 1,
    unit_price: 0,
  }
}

const editItem = (index: number) => {
  editingItemIndex.value = index
  newItem.value = {
    supply_id: form.items[index].supply_id,
    quantity: form.items[index].quantity,
    unit_price: form.items[index].unit_price,
  }
  showAddItemModal.value = true
}

const addItem = () => {
  if (!newItem.value.supply_id || !newItem.value.quantity || !newItem.value.unit_price) {
    return
  }

  if (editingItemIndex.value !== null) {
    // Mise à jour de l'article existant
    form.items[editingItemIndex.value] = {
      supply_id: newItem.value.supply_id,
      quantity: newItem.value.quantity,
      unit_price: newItem.value.unit_price,
    }
    editingItemIndex.value = null
  } else {
    // Ajout d'un nouvel article
    form.items.push({
      supply_id: newItem.value.supply_id,
      quantity: newItem.value.quantity,
      unit_price: newItem.value.unit_price,
    })
  }

  closeAddItemModal()
}

const removeItem = (index: number) => {
  form.items.splice(index, 1)
}

const submit = () => {
  form.put(route('orders.update', props.order.id))
}

const updateUnitPrice = () => {
  if (!newItem.value.supply_id || !form.supplier_id) {
    newItem.value.unit_price = 0
    return
  }

  const supply = props.supplies.find(s => s.id === newItem.value.supply_id)
  if (!supply) {
    newItem.value.unit_price = 0
    return
  }

  const supplier = supply.suppliers.find(s => s.id === form.supplier_id)
  if (supplier) {
    newItem.value.unit_price = supplier.unit_price
  } else {
    newItem.value.unit_price = 0
  }
}

watch(() => [newItem.value.supply_id, form.supplier_id], updateUnitPrice)
</script> 