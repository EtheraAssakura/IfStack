<script setup lang="ts">
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Link } from '@inertiajs/vue3';
import { MoreVertical, Pencil, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

defineOptions({
    name: 'StockList'
});

const props = defineProps<{
    stocks: Array<{
        id: number;
        supply: {
            id: number;
            name: string;
            reference: string;
            packaging: string;
        };
        location: {
            id: number;
            name: string;
            site: {
                name: string;
            };
        };
        estimated_quantity: number;
        local_alert_threshold: number | null;
    }>;
    showTakeButton?: boolean;
}>();

const emit = defineEmits<{
    (e: 'transfer', stock: any): void;
    (e: 'edit', stock: any): void;
    (e: 'delete', stock: any): void;
}>();

const search = ref('');
const filteredStocks = computed(() => {
    const searchLower = search.value.toLowerCase();
    return props.stocks.filter(stock =>
        stock.supply.name.toLowerCase().includes(searchLower) ||
        stock.supply.reference.toLowerCase().includes(searchLower) ||
        stock.location.name.toLowerCase().includes(searchLower) ||
        stock.location.site.name.toLowerCase().includes(searchLower)
    );
});
</script>

<template>
    <div>
        <!-- Barre de recherche -->
        <div class="mb-4">
            <TextInput
                v-model="search"
                type="text"
                class="w-full"
                placeholder="Rechercher par référence, nom, emplacement ou site..."
            />
        </div>

        <!-- Tableau des stocks -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Référence
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fourniture
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Emplacement
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Site
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantité
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Seuil d'alerte
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="stock in filteredStocks" :key="stock.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ stock.supply.reference }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <Link :href="`/supplies/${stock.supply.id}`" class="text-indigo-600 hover:text-indigo-900">
                                {{ stock.supply.name }}
                            </Link>
                            <span class="text-gray-400 text-xs block">
                                {{ stock.supply.packaging }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ stock.location.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ stock.location.site.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ stock.estimated_quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ stock.local_alert_threshold || '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <SecondaryButton
                                    v-if="showTakeButton"
                                    @click="$emit('transfer', stock)"
                                >
                                    Prendre
                                </SecondaryButton>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <MoreVertical class="h-5 w-5" />
                                        </button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem @click="$emit('edit', stock)" class="flex items-center gap-2">
                                            <Pencil class="h-4 w-4" />
                                            Modifier
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="$emit('delete', stock)" class="text-red-500 focus:text-red-500 flex items-center gap-2">
                                            <Trash2 class="h-4 w-4" />
                                            Supprimer
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
</template>
