<template>
  <AppSideBarLayout title="Scanner un QR code">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Scanner un QR code
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">
                Scanner
              </h3>
              <div class="relative">
                <video
                  ref="video"
                  class="w-full rounded-lg"
                  autoplay
                  playsinline
                ></video>
                <canvas
                  ref="canvas"
                  class="hidden"
                  width="640"
                  height="480"
                ></canvas>
              </div>
              <div class="mt-4">
                <button
                  @click="startScanning"
                  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                  :disabled="isScanning"
                >
                  {{ isScanning ? 'Scan en cours...' : 'Démarrer le scan' }}
                </button>
              </div>
            </div>

            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">
                Derniers scans
              </h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fourniture
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Site
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Emplacement
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="scan in recentScans" :key="scan.id">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ new Date(scan.created_at).toLocaleString() }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ scan.stockItem.supply.name }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ scan.stockItem.supply.reference }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ scan.stockItem.site.name }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ scan.stockItem.location.name }}
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
    </div>
  </AppSideBarLayout>
</template>

<script setup lang="ts">
import AppSideBarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { router } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

interface Supply {
  id: number;
  name: string;
  reference: string;
}

interface Site {
  id: number;
  name: string;
}

interface Location {
  id: number;
  name: string;
}

interface StockItem {
  id: number;
  supply: Supply;
  site: Site;
  location: Location;
}

interface Scan {
  id: number;
  created_at: string;
  stockItem: StockItem;
}

interface Props {
  recentScans: Scan[];
}

const props = defineProps<Props>();

const isScanning = ref(false);
const stream = ref<MediaStream | null>(null);
const video = ref<HTMLVideoElement | null>(null);
const canvas = ref<HTMLCanvasElement | null>(null);

const startScanning = async () => {
  try {
    stream.value = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
    if (video.value) {
      video.value.srcObject = stream.value;
    }
    isScanning.value = true;
    scanLoop();
  } catch (error) {
    console.error('Erreur lors de l\'accès à la caméra:', error);
  }
};

const stopScanning = () => {
  if (stream.value) {
    stream.value.getTracks().forEach(track => track.stop());
    if (video.value) {
      video.value.srcObject = null;
    }
    stream.value = null;
  }
  isScanning.value = false;
};

const scanLoop = async () => {
  if (!isScanning.value || !video.value || !canvas.value) return;

  const context = canvas.value.getContext('2d');
  if (!context) return;

  canvas.value.width = video.value.videoWidth;
  canvas.value.height = video.value.videoHeight;
  context.drawImage(video.value, 0, 0, canvas.value.width, canvas.value.height);

  try {
    const imageData = context.getImageData(0, 0, canvas.value.width, canvas.value.height);
    // Ici, vous pouvez intégrer une bibliothèque de décodage de QR code
    // Par exemple, jsQR ou ZXing
    // const code = jsQR(imageData.data, imageData.width, imageData.height)
    
    // if (code) {
    //   handleQrCode(code.data)
    // }
  } catch (error) {
    console.error('Erreur lors du scan:', error);
  }

  if (isScanning.value) {
    requestAnimationFrame(() => scanLoop());
  }
};

const handleQrCode = (data: string) => {
  router.post(route('stock-items.scan'), {
    qr_code: data,
  });
};

onBeforeUnmount(() => {
  stopScanning();
});
</script> 