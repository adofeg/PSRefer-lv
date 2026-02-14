<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Briefcase, FileText, CheckCircle, AlertCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import { normalizeCollection, normalizeResource } from '@/Utils/inertia';
import DynamicForm from '@/Components/Forms/DynamicForm.vue';

const props = defineProps({
    offerings: Array,
    selectedOffering: Object, 
});

const offeringsList = computed(() => normalizeCollection(props.offerings));
const selectedOffering = computed(() => normalizeResource(props.selectedOffering, null));

const form = useForm({
    offering_id: selectedOffering.value?.id || '',
    form_data: {},
    notes: '',
});

const currentOffering = computed(() => {
    if (selectedOffering.value) return selectedOffering.value;
    if (form.offering_id) {
        return offeringsList.value.find(o => o.id === form.offering_id);
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
        <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb / Header -->
             <div class="mb-8">
                <Link :href="route('associate.referrals.index')" class="flex items-center text-slate-500 hover:text-indigo-600 transition mb-4 text-sm font-medium">
                    <ArrowLeft class="w-4 h-4 mr-1.5" />
                    Volver a Mis Referidos
                </Link>
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-100 p-2 rounded-lg">
                        <Briefcase class="w-6 h-6 text-indigo-600" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Registrar Nuevo Referido</h1>
                        <p class="text-slate-500 text-sm">Ingresa los datos de tu cliente potencial para iniciar el proceso.</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
                <form @submit.prevent="submit">
                    <!-- Section 0: Offering Selection (NOW AT TOP) -->
                    <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                             <Briefcase class="w-4 h-4 text-slate-400" />
                            Servicio de Interés
                        </h2>
                        <div class="relative">
                            <select v-model="form.offering_id" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4 transition shadow-sm bg-white" required>
                                <option value="" disabled>Selecciona un servicio del catálogo...</option>
                                <option v-for="offering in offeringsList" :key="offering.id" :value="offering.id">
                                    {{ offering.name }} — Comisión: {{ offering.commission_type === 'percentage' ? `${offering.base_commission}%` : formatCurrency(offering.base_commission) }}
                                </option>
                            </select>
                        </div>
                        <div v-if="form.errors.offering_id" class="text-red-500 text-xs mt-1 font-medium flex items-center gap-1"><AlertCircle class="w-3 h-3" /> {{ form.errors.offering_id }}</div>
                        <p class="text-xs text-slate-500 mt-2 ml-1">Selecciona el servicio que le interesa al cliente para desbloquear el formulario.</p>
                    </div>

                    <div v-if="currentOffering">
                        <!-- Dynamic Form (Catalog-Driven) -->
                        <div class="p-8 border-b border-slate-100">
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-6 flex items-center gap-2">
                                 <FileText class="w-4 h-4 text-slate-400" />
                                {{ currentOffering.form_schema?.length > 4 ? 'Información del Cliente y Detalles' : 'Información del Cliente' }}
                            </h2>
                            <DynamicForm 
                                :schema="currentOffering.form_schema"
                                v-model="form.form_data"
                                :errors="form.errors"
                            />
                        </div>

                        <!-- Section 2: Notes -->
                        <div class="p-8 border-b border-slate-100">
                             <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                                 <FileText class="w-4 h-4 text-slate-400" />
                                Notas Adicionales
                            </h2>
                            <textarea v-model="form.notes" rows="4" placeholder="Cualquier detalle extra sobre el cliente o sus necesidades..." class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4 transition shadow-sm bg-slate-50/50"></textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-6 bg-slate-100/50 border-t border-slate-200 flex justify-end gap-3 items-center">
                         <Link :href="route('associate.referrals.index')" class="px-5 py-2.5 text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg font-medium text-sm transition">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing" class="flex items-center gap-2 bg-indigo-600 text-white px-8 py-2.5 rounded-xl hover:bg-indigo-700 transition font-bold text-sm shadow-lg shadow-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed transform active:scale-95">
                            <CheckCircle v-if="!form.processing" class="w-4 h-4" />
                            <span v-else class="animate-pulse">Guardando...</span>
                            {{ form.processing ? '' : 'Crear Referido' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
