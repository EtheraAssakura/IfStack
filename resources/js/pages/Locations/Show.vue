<template>
  <AppLayout :title="location.name" :breadcrumbs="breadcrumbs">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ location.name }}
        </h2>
        <Link
          :href="route('locations.edit', location.id)"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
        >
          Modifier
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Informations de l'emplacement -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Photo et informations de base -->
              <div>
                <div v-if="location.qr_code_url" class="mb-4">
                  <h3 class="text-lg font-medium text-gray-900 mb-2">QR Code</h3>
                  <div class="bg-white p-4 rounded-lg shadow-md inline-block">
                    <img :src="location.qr_code_url" :alt="`QR Code de ${location.name}`" class="w-64 h-64" />
                    
                  </div>
                  <div class="mt-4">
                    <Button variant="outline" size="sm" @click="downloadQRCode">
                      Télécharger le QR Code
                    </Button>
                  </div>
                </div>
                <div v-else class="mb-4 w-full max-w-md h-48 bg-gray-100 rounded-lg flex items-center justify-center">
                  <MapPin class="h-16 w-16 text-gray-400" />
                </div>
                <div class="space-y-4">
                  <div>
                    <h3 class="text-lg font-medium text-gray-900">Site</h3>
                    <p class="mt-1 text-gray-600">{{ location.etablissement.name }}</p>
                  </div>
                  <div>
                    <h3 class="text-lg font-medium text-gray-900">Description</h3>
                    <p class="mt-1 text-gray-600">{{ location.description || 'Aucune description' }}</p>
                  </div>
                </div>
              </div>

              <!-- Liste des stocks -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Stocks</h3>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Fourniture
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Catégorie
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Quantité
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Dernière mise à jour
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="item in location.stock_items" :key="item.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm font-medium text-gray-900">{{ item.supply.name }}</div>
                          <div class="text-sm text-gray-500">{{ item.supply.reference }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ item.supply.category }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ item.estimated_quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ item.last_update }}
                        </td>
                      </tr>
                      <tr v-if="location.stock_items.length === 0">
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                          Aucun stock dans cet emplacement
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';
import { MapPin } from 'lucide-vue-next';

const props = defineProps<{
  location: {
    id: number
    name: string
    description: string | null
    etablissement: {
      id: number
      name: string
    }
    photo_url: string | null
    qr_code: string
    qr_code_url: string | null
    stock_items: Array<{
      id: number
      supply: {
        name: string
        reference: string
        category: string
      }
      estimated_quantity: number
      last_update: string
    }>
  }
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  {
    title: 'Établissements',
    href: route('etablissements.index'),
  },
  {
    title: props.location.etablissement.name,
    href: route('etablissements.show', props.location.etablissement.id),
  },
  {
    title: props.location.name,
    href: route('etablissements.locations.show', {
      etablissement: props.location.etablissement.id,
      location: props.location.id
    }),
  },
];

const downloadQRCode = async () => {
  if (!props.location.qr_code_url) return;
  
  try {
    // Créer un canvas temporaire
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    if (!ctx) throw new Error('Impossible de créer le contexte du canvas');

    // Créer une image à partir du SVG
    const img = new Image();
    img.crossOrigin = 'anonymous'; // Pour éviter les problèmes CORS

    // Attendre que l'image soit chargée
    await new Promise((resolve, reject) => {
      img.onload = resolve;
      img.onerror = reject;
      img.src = props.location.qr_code_url!;
    });

    // Définir la taille du canvas
    canvas.width = img.width;
    canvas.height = img.height;

    // Dessiner l'image sur le canvas
    ctx.drawImage(img, 0, 0);

    // Convertir le canvas en PNG
    const pngDataUrl = canvas.toDataURL('image/png');

    // Créer un lien de téléchargement
    const a = document.createElement('a');
    a.href = pngDataUrl;
    a.download = `qr-code-${props.location.name.toLowerCase().replace(/\s+/g, '-')}.png`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  } catch (error) {
    console.error('Erreur lors du téléchargement du QR Code:', error);
  }
}
</script>
