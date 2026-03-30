<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Briefcase, FileText, CheckCircle, AlertCircle, LayoutGrid, Loader } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { normalizeCollection, normalizeResource } from '@/Utils/inertia';
import DynamicForm from '@/Components/Forms/DynamicForm.vue';
import MultiSelectCombobox from '@/Components/UI/MultiSelectCombobox.vue';

const props = defineProps({
    offerings: Array,
    selectedOffering: Object, 
    sectors: Array,
});

const offeringsList = computed(() => normalizeCollection(props.offerings));
const selectedOffering = computed(() => normalizeResource(props.selectedOffering, null));

const form = useForm({
    offering_id: selectedOffering.value?.id || '',
    client_name: '',
    client_email: '',
    client_phone: '',
    sector_id: '',
    form_data: {},
    notes: '',
    consent_confirmed: false
});

const formattedSectors = computed(() => {
    return (props.sectors || []).map(s => ({ id: s.id, name: s.name }));
});

const formattedOfferings = computed(() => {
    return offeringsList.value.map(o => ({ 
        id: o.id, 
        name: o.name,
        sublabel: `Comisión: ${o.commission_type === 'percentage' ? `${o.base_commission}%` : formatCurrency(o.base_commission)}`
    }));
});

const isFormFinalStep = ref(true);
const isFormValid = ref(true);

const currentOffering = computed(() => {
    if (selectedOffering.value) return selectedOffering.value;
    if (form.offering_id) {
        return offeringsList.value.find(o => o.id == form.offering_id);
    }
    return null;
});

const submit = () => {
    form.post(route('associate.referrals.store'));
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};
</script>

<template>
    <Head title="Nuevo Referido" />

    <AppLayout>
        <div class="space-y-6">
            <!-- Header -->
             <div class="flex flex-col gap-2">
                <Link :href="route('associate.referrals.index')" class="flex items-center text-slate-500 hover:text-indigo-600 transition text-sm font-medium w-fit">
                    <ArrowLeft class="w-4 h-4 mr-1.5" />
                    Volver a Mis Referidos
                </Link>
                
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Registrar Nuevo Referido</h1>
                        <p class="text-slate-500">Ingresa los datos de tu cliente potencial.</p>
                    </div>
                </div>
            </div>

            <!-- Form Card with Refined Styling -->
            <div class="bg-white/70 backdrop-blur-md rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/60">
                <form @submit.prevent="submit" class="rounded-3xl">
                    <!-- Section 0: Offering & Sector Selection -->
                    <div class="p-10 border-b border-slate-100 bg-slate-50/30 relative z-20">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <!-- Offering Selection -->
                            <div>
                                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                     <LayoutGrid class="w-3.5 h-3.5 text-indigo-500" />
                                    Servicio de Interés
                                </h2>
                                <MultiSelectCombobox 
                                    v-model="form.offering_id"
                                    :options="formattedOfferings"
                                    placeholder="Buscar servicio..."
                                    required
                                />
                                <div v-if="form.errors.offering_id" class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1">
                                    <AlertCircle class="w-3 h-3" /> {{ form.errors.offering_id }}
                                </div>
                                <p class="text-[10px] text-slate-400 mt-3 ml-1 font-medium italic">Selecciona el servicio para habilitar los campos específicos.</p>
                            </div>

                            <!-- Sector Selection -->
                            <div>
                                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <Briefcase class="w-3.5 h-3.5 text-indigo-500" />
                                    Sector de Servicio
                                </h2>
                                <MultiSelectCombobox 
                                    v-model="form.sector_id"
                                    :options="formattedSectors"
                                    placeholder="Seleccionar sector..."
                                    required
                                />
                                <div v-if="form.errors.sector_id" class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1">
                                    <AlertCircle class="w-3 h-3" /> {{ form.errors.sector_id }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="currentOffering">
                        <!-- Standardized Dynamic Form -->
                        <div class="p-6 md:p-8 border-b border-slate-100">
                            <DynamicForm 
                                :schema="currentOffering.form_schema"
                                v-model="form.form_data"
                                v-model:client-name="form.client_name"
                                v-model:client-email="form.client_email"
                                v-model:client-phone="form.client_phone"
                                :is-referral-mode="true"
                                :errors="form.errors"
                                @update:is-final-step="isFormFinalStep = $event"
                                @update:is-valid="isFormValid = $event"
                            />
                        </div>

                        <!-- Section 2: Notes -->
                        <div v-if="isFormFinalStep" class="p-6 md:p-8 border-b border-slate-100 animate-fade-in">
                             <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                                 <FileText class="w-4 h-4 text-slate-400" />
                                Notas Adicionales
                            </h2>
                            <textarea v-model="form.notes" rows="4" placeholder="Cualquier detalle extra sobre el cliente o sus necesidades..." class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4 transition shadow-sm bg-slate-50/50"></textarea>
                        </div>

                        <!-- Consent Confirmation -->
                        <div v-if="isFormFinalStep" class="p-6 md:p-8 border-b border-slate-100 animate-fade-in bg-slate-50/30">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input 
                                        type="checkbox" 
                                        v-model="form.consent_confirmed" 
                                        class="w-5 h-5 border-slate-300 rounded text-indigo-600 focus:ring-indigo-600 transition shadow-sm group-hover:border-indigo-400"
                                    >
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-800">Confirmo que el cliente ha dado su consentimiento</span>
                                    <span class="text-xs text-slate-500 mt-0.5">He explicado los servicios y el cliente acepta ser contactado por nuestro equipo.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-10 bg-slate-50/50 border-t border-slate-100 flex justify-end gap-6 items-center">
                         <Link :href="route('associate.referrals.index')" class="px-6 py-3 text-slate-400 hover:text-slate-600 font-black text-xs uppercase tracking-widest transition">
                            CANCELAR
                        </Link>
                        <button v-if="isFormFinalStep" type="submit" :disabled="form.processing || !isFormValid" class="flex items-center gap-3 bg-indigo-600 text-white px-12 py-4 rounded-2xl hover:bg-indigo-700 transition font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-200 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed transform active:scale-95 animate-fade-in group">
                            <Loader v-if="form.processing" class="animate-spin w-4 h-4" />
                            <CheckCircle v-else class="w-4 h-4 group-hover:scale-110 transition-transform" />
                            <span>{{ form.processing ? 'Guardando...' : 'Crear Referido' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
