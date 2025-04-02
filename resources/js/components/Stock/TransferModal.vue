<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Modal from '@/components/Modal.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import SelectInput from '@/components/SelectInput.vue';
import TextInput from '@/components/TextInput.vue';
import { onMounted, ref } from 'vue';

const props = defineProps<{
    show: boolean;
    stock: {
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
        estimated_quantity: number;
    };
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'transfer-complete'): void;
}>();

const locations = ref([]);
const form = ref({
    from_location_id: props.stock?.location.id,
    to_location_id: '',
    supply_id: props.stock?.supply.id,
    quantity: 1,
    reason: ''
});

const errors = ref({
    to_location_id: '',
    quantity: '',
    reason: ''
});

const loading = ref(false);

const fetchLocations = async () => {
    try {
        const response = await axios.get('/api/locations');
        locations.value = response.data.filter(
            location => location.id !== props.stock?.location.id
        );
    } catch (error) {
        console.error('Erreur lors de la récupération des emplacements:', error);
    }
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {
        to_location_id: '',
        quantity: '',
        reason: ''
    };

    try {
        await axios.post('/api/stocks/transfer', form.value);
        emit('transfer-complete');
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchLocations();
});
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Transfert de Stock
            </h2>

            <div class="mt-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Transfert depuis : <strong>{{ stock.location.name }}</strong> ({{ stock.location.site.name }})
                    </p>
                    <p class="text-sm text-gray-600">
                        Article : <strong>{{ stock.supply.reference }} - {{ stock.supply.name }}</strong>
                    </p>
                    <p class="text-sm text-gray-600">
                        Quantité disponible : <strong>{{ stock.estimated_quantity }}</strong>
                    </p>
                </div>

                <div class="mb-4">
                    <InputLabel for="to_location_id" value="Emplacement de destination" />
                    <SelectInput
                        id="to_location_id"
                        v-model="form.to_location_id"
                        class="mt-1 block w-full"
                    >
                        <option value="">Sélectionner un emplacement</option>
                        <option
                            v-for="location in locations"
                            :key="location.id"
                            :value="location.id"
                        >
                            {{ location.name }} ({{ location.site.name }})
                        </option>
                    </SelectInput>
                    <InputError :message="errors.to_location_id" class="mt-2" />
                </div>

                <div class="mb-4">
                    <InputLabel for="quantity" value="Quantité à transférer" />
                    <TextInput
                        id="quantity"
                        type="number"
                        v-model="form.quantity"
                        class="mt-1 block w-full"
                        min="1"
                        :max="stock.estimated_quantity"
                    />
                    <InputError :message="errors.quantity" class="mt-2" />
                </div>

                <div class="mb-4">
                    <InputLabel for="reason" value="Raison du transfert" />
                    <TextInput
                        id="reason"
                        type="text"
                        v-model="form.reason"
                        class="mt-1 block w-full"
                        placeholder="Raison optionnelle"
                    />
                    <InputError :message="errors.reason" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="$emit('close')">
                    Annuler
                </SecondaryButton>
                <PrimaryButton
                    :class="{ 'opacity-25': loading }"
                    :disabled="loading"
                    @click="handleSubmit"
                >
                    Transférer
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
