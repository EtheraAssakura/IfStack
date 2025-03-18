<script setup lang="ts">
import TextInput from '@/Components/TextInput.vue';
import { formatDate } from '@/utils/date';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    movements: Array<{
        id: number;
        supply: {
            id: number;
            name: string;
            reference: string;
        };
        location: {
            id: number;
            name: string;
            site: {
                name: string;
            };
        };
        user: {
            id: number;
            name: string;
        };
        type: string;
        quantity: number;
        previous_quantity: number;
        new_quantity: number;
        reason: string;
        created_at: string;
    }>;
}>();

const search = ref('');
const filteredMovements = computed(() => {
    const searchLower = search.value.toLowerCase();
    return props.movements.filter(movement =>
        movement.supply.name.toLowerCase().includes(searchLower) ||
        movement.supply.reference.toLowerCase().includes(searchLower) ||
        movement.location.name.toLowerCase().includes(searchLower) ||
        movement.location.site.name.toLowerCase().includes(searchLower) ||
        movement.reason.toLowerCase().includes(searchLower)
    );
});

const getTypeLabel = (type: string): string => {
    switch (type) {
        case 'adjustment':
            return 'Ajustement';
        case 'transfer_in':
            return 'Transfert entrant';
        case 'transfer_out':
            return 'Transfert sortant';
        default:
            return type;
    }
};

const getTypeColor = (type: string): string => {
    switch (type) {
        case 'adjustment':
            return 'bg-yellow-100 text-yellow-800';
        case 'transfer_in':
            return 'bg-green-100 text-green-800';
        case 'transfer_out':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <div>
        <!-- Barre de recherche -->
        <div class="mb-4">
            <TextInput
                v-model="search"
                type="text"
                class="w-full"
                placeholder="Rechercher par référence, nom, emplacement, site ou raison..."
            />
        </div>

        <!-- Tableau des mouvements -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fourniture
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Emplacement
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantité
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Utilisateur
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Raison
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="movement in filteredMovements" :key="movement.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatDate(movement.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getTypeColor(movement.type)]">
                                {{ getTypeLabel(movement.type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <Link :href="`/supplies/${movement.supply.id}`" class="text-indigo-600 hover:text-indigo-900">
                                {{ movement.supply.reference }} - {{ movement.supply.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ movement.location.name }}
                            <span class="text-gray-400 text-xs block">
                                {{ movement.location.site.name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span :class="movement.quantity >= 0 ? 'text-green-600' : 'text-red-600'">
                                {{ movement.quantity >= 0 ? '+' : '' }}{{ movement.quantity }}
                            </span>
                            <span class="text-gray-400 text-xs block">
                                {{ movement.previous_quantity }} → {{ movement.new_quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ movement.user.name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ movement.reason }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>