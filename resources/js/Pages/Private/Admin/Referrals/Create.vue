<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import DynamicForm from '@/Components/Forms/DynamicForm.vue';
import { computed, watch } from 'vue';
import { normalizeCollection, normalizeResource } from '@/Utils/inertia';

const props = defineProps({
    offering: Object, // Optional pre-selected offering
    offerings: Array, // List for dropdown if needed
    associates: Array // List of associates for Admin selection
});

const selectedOffering = computed(() => normalizeResource(props.offering, null));
const offeringsList = computed(() => normalizeCollection(props.offerings));
const associatesList = computed(() => normalizeCollection(props.associates));

const form = useForm({
    offering_id: selectedOffering.value?.id || '',
    associate_id: '',
    form_data: {},
    notes: ''
});

// If offering is pre-selected, watch for it
const currentOffering = computed(() => {
    if (selectedOffering.value) {
        return selectedOffering.value;
    }
    if (form.offering_id && offeringsList.value.length > 0) {
        return offeringsList.value.find((item) => item.id === form.offering_id) || null;
    }
    return null;
});

// Reset form_data when offering changes
watch(() => form.offering_id, () => {
    form.form_data = {};
});

const submit = () => {
    form.post(route('admin.referrals.store'));
};
</script>

<template>
    <Head title="Nuevo Referido" />

    <AppLayout>
        <div class="max-w-3xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Registrar Referido</h1>
                <p class="text-slate-600 mt-1">Complete la información del cliente y los datos requeridos</p>
            </div>

            <Card>
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Offering Selector (if not pre-selected) -->
                    <div v-if="!selectedOffering && offeringsList.length">
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Selecciona la Oferta <span class="text-red-500">*</span>
                        </label>
                        <select 
                            v-model="form.offering_id" 
                            required
                            class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">Seleccionar...</option>
                            <option v-for="off in offeringsList" :key="off.id" :value="off.id">
                                {{ off.name }} - {{ off.commission_type === 'percentage' ? `${off.base_commission}%` : `$${off.base_commission}` }} comisión
                            </option>
                        </select>
                        <div v-if="form.errors.offering_id" class="text-red-500 text-xs mt-1">
                            {{ form.errors.offering_id }}
                        </div>
                    </div>

                    <!-- Associate Selector (Admin Only) -->
                    <div v-if="associatesList.length">
                         <label class="block text-sm font-medium text-slate-700 mb-1">
                            Asignar a Asociado <span class="text-red-500">*</span>
                        </label>
                        <select 
                            v-model="form.associate_id" 
                            required
                            class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">Seleccionar Asociado...</option>
                            <option v-for="assoc in associatesList" :key="assoc.id" :value="assoc.id">
                                {{ assoc.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.associate_id" class="text-red-500 text-xs mt-1">
                            {{ form.errors.associate_id }}
                        </div>
                    </div>

                    <!-- Pre-selected Offering Display -->
                    <div v-if="selectedOffering" class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <p class="text-sm text-indigo-700 font-medium mb-1">Refiriendo a:</p>
                        <p class="text-lg font-semibold text-indigo-900">{{ selectedOffering.name }}</p>
                        <p class="text-sm text-indigo-600 mt-1">Comisión: {{ selectedOffering.commission_type === 'percentage' ? `${selectedOffering.base_commission}%` : `$${selectedOffering.base_commission}` }}</p>
                    </div>

                    <div v-if="currentOffering" class="space-y-6">
                        <!-- Dynamic Form (Catalog-Driven) -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">
                                {{ currentOffering.form_schema?.length > 4 ? 'Información del Cliente y Detalles' : 'Información del Cliente' }}
                            </h3>
                            <DynamicForm 
                                :schema="currentOffering.form_schema"
                                v-model="form.form_data"
                                :errors="form.errors"
                            />
                        </div>

                        <!-- Notes -->
                        <div class="border-t pt-6">
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Notas Adicionales
                            </label>
                            <textarea 
                                v-model="form.notes" 
                                rows="3" 
                                placeholder="Información adicional sobre este referido..."
                                class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button 
                            type="button" 
                            @click="$inertia.visit(route('admin.referrals.index'))" 
                            class="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg border border-slate-300"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium disabled:opacity-50" 
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Guardando...' : 'Registrar Referido' }}
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
