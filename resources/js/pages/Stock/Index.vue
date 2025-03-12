<script setup lang="ts">
import StockList from '@/Components/Stock/StockList.vue';
import StockMovementList from '@/Components/Stock/StockMovementList.vue';
import TransferModal from '@/Components/Stock/TransferModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const stocks = ref([]);
const movements = ref([]);
const showTransferModal = ref(false);
const selectedStock = ref(null);

const fetchStocks = async () => {
    try {
        const response = await axios.get('/api/stocks');
        stocks.value = response.data;
    } catch (error) {
        console.error('Erreur lors de la récupération des stocks:', error);
    }
};

const fetchMovements = async () => {
    try {
        const response = await axios.get('/api/stock-movements');
        movements.value = response.data.data;
    } catch (error) {
        console.error('Erreur lors de la récupération des mouvements:', error);
    }
};

const openTransferModal = (stock) => {
    selectedStock.value = stock;
    showTransferModal.value = true;
};

const handleTransferComplete = () => {
    showTransferModal.value = false;
    selectedStock.value = null;
    fetchStocks();
    fetchMovements();
};

onMounted(() => {
    fetchStocks();
    fetchMovements();
});
</script>

<template>
    <AppLayout>
        <Head>
            <title>Gestion des Stocks - ISFAC</title>
        </Head>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestion des Stocks
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Section des stocks -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">État des Stocks</h3>
                    <StockList
                        :stocks="stocks"
                        @transfer="openTransferModal"
                    />
                </div>

                <!-- Section des mouvements -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des Mouvements</h3>
                    <StockMovementList :movements="movements" />
                </div>
            </div>
        </div>

        <!-- Modal de transfert -->
        <TransferModal
            v-if="showTransferModal"
            :show="showTransferModal"
            :stock="selectedStock"
            @close="showTransferModal = false"
            @transfer-complete="handleTransferComplete"
        />
    </AppLayout>
</template>
