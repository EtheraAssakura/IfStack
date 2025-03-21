<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Stock } from '@/types/app';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    stock: Stock;
}>();

const form = useForm({
    estimated_quantity: props.stock.estimated_quantity,
});

const submit = () => {
    form.put(route('stocks.update', props.stock.id));
};
</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Détails du stock</h1>
                <p class="text-sm text-gray-500">
                    {{ stock.fourniture.name }} - {{ stock.emplacement.name }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Établissement</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ stock.emplacement.etablissement.name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Emplacement</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ stock.emplacement.name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fourniture</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ stock.fourniture.name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Référence</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ stock.fourniture.reference }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Conditionnement</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ stock.fourniture.packaging }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Quantité en stock</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <Label for="estimated_quantity">Quantité actuelle</Label>
                        <Input
                            id="estimated_quantity"
                            v-model="form.estimated_quantity"
                            type="number"
                            class="mt-1"
                            required
                        />
                        <p v-if="form.errors.estimated_quantity" class="mt-1 text-sm text-red-600">
                            {{ form.errors.estimated_quantity }}
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="form.processing">
                            Mettre à jour la quantité
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template> 