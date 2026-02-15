<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import DynamicForm from '@/Components/Forms/DynamicForm.vue';
import { computed, watch, ref } from 'vue';
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
    client_name: '',
    client_email: '',
    client_phone: '',
    form_data: {},
    notes: ''
});

const isFormFinalStep = ref(true);
const isFormValid = ref(true);

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
        <div class="space-y-6">
            <div class="flex flex-col gap-1">
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Registrar Referido</h1>
                <p class="text-sm font-medium text-slate-500">Complete la información del cliente y los datos requeridos para procesar el lead.</p>
            </div>

            <Card clazz="p-8 shadow-xl border-slate-200/50 bg-white/50 backdrop-blur-sm rounded-3xl">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Offering Selector (if not pre-selected) -->
                    <div v-if="!selectedOffering && offeringsList.length">
                        <label class="block text-xs font-black uppercase text-slate-400 tracking-widest mb-2">
                            Selecciona la Oferta <span class="text-red-500">*</span>
                        </label>
                        <select 
                            v-model="form.offering_id" 
                            required
                            class="w-full border-slate-200 rounded-2xl p-4 text-base font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white transition-all cursor-pointer"
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
                        <label class="block text-xs font-black uppercase text-slate-400 tracking-widest mb-2">
                            Asignar a Asociado (Opcional)
                        </label>
                        <select 
                            v-model="form.associate_id" 
                            class="w-full border-slate-200 rounded-2xl p-4 text-base font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white transition-all cursor-pointer"
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
                    <div v-if="selectedOffering" class="bg-indigo-50/50 border border-indigo-100/50 rounded-2xl p-6 shadow-sm">
                        <label class="block text-[10px] font-black uppercase text-indigo-400 tracking-widest mb-2">Refiriendo a:</label>
                        <p class="text-xl font-black text-indigo-900">{{ selectedOffering.name }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-black uppercase tracking-tight">
                                {{ selectedOffering.commission_type === 'percentage' ? `${selectedOffering.base_commission}%` : `$${selectedOffering.base_commission}` }} Comisión
                            </span>
                        </div>
                    </div>

                    <div v-if="currentOffering" class="space-y-6">
                        <!-- Standardized Dynamic Form (Step 1: Datos Personales + Step 2+: Detalles del Servicio) -->
                        <div class="border-t border-slate-100 pt-10">
                            <DynamicForm 
                                :schema="currentOffering.form_schema"
                                v-model="form.form_data"
                                v-model:client-name="form.client_name"
                                v-model:client-email="form.client_email"
                                v-model:client-phone="form.client_phone"
                                :is-referral-mode="true"
                                :errors="form.errors"
                                @update:is-final-step="(val) => isFormFinalStep = val"
                                @update:is-valid="(val) => isFormValid = val"
                            />
                        </div>

                        <!-- Notes -->
                        <div v-if="isFormFinalStep" class="border-t border-slate-100 pt-10 animate-fade-in">
                            <label class="block text-xs font-black uppercase text-slate-400 tracking-widest mb-2">
                                Notas Adicionales
                            </label>
                            <textarea 
                                v-model="form.notes" 
                                rows="3" 
                                placeholder="Información adicional sobre este referido..."
                                class="w-full border-slate-200 rounded-2xl p-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white transition-all"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-4 pt-10 border-t border-slate-100">
                        <button 
                            type="button" 
                            @click="$inertia.visit(route('admin.referrals.index'))" 
                            class="px-8 py-4 text-sm font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition"
                        >
                            CANCELAR
                        </button>
                        <button 
                            v-if="isFormFinalStep"
                            type="submit" 
                            class="bg-indigo-600 text-white px-10 py-4 rounded-2xl hover:bg-indigo-700 font-black text-sm uppercase tracking-widest transition shadow-xl shadow-indigo-200 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed animate-fade-in" 
                            :disabled="form.processing || !isFormValid"
                        >
                            {{ form.processing ? 'Guardando...' : 'Registrar Referido' }}
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
