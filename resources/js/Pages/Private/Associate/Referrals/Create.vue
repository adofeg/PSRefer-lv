<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, User, Mail, Phone, MapPin, Briefcase, FileText, CheckCircle, AlertCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import { normalizeCollection, normalizeResource } from '@/Utils/inertia';

const props = defineProps({
    offerings: Array,
    selectedOffering: Object, 
});

const offeringsList = computed(() => normalizeCollection(props.offerings));
const selectedOffering = computed(() => normalizeResource(props.selectedOffering, null));

const form = useForm({
    client_name: '',
    client_email: '',
    client_phone: '',
    client_state: '',
    offering_id: selectedOffering.value?.id || '',
    notes: '',
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
                    <!-- Section 1: Client Info -->
                    <div class="p-8 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-6 flex items-center gap-2">
                            <User class="w-4 h-4 text-slate-400" />
                            Información del Cliente
                        </h2>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Name -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nombre Completo <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <User class="h-4 w-4 text-slate-400" />
                                    </div>
                                    <input v-model="form.client_name" type="text" placeholder="Ej. Juan Pérez" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition shadow-sm bg-slate-50/50" required />
                                </div>
                                <div v-if="form.errors.client_name" class="text-red-500 text-xs mt-1 font-medium flex items-center gap-1"><AlertCircle class="w-3 h-3" /> {{ form.errors.client_name }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Email -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Email <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <Mail class="h-4 w-4 text-slate-400" />
                                        </div>
                                        <input v-model="form.client_email" type="email" placeholder="cliente@ejemplo.com" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition shadow-sm bg-slate-50/50" required />
                                    </div>
                                    <div v-if="form.errors.client_email" class="text-red-500 text-xs mt-1 font-medium flex items-center gap-1"><AlertCircle class="w-3 h-3" /> {{ form.errors.client_email }}</div>
                                </div>
                                <!-- Phone -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Teléfono <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <Phone class="h-4 w-4 text-slate-400" />
                                        </div>
                                        <input v-model="form.client_phone" type="tel" placeholder="(555) 123-4567" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition shadow-sm bg-slate-50/50" required />
                                    </div>
                                    <div v-if="form.errors.client_phone" class="text-red-500 text-xs mt-1 font-medium flex items-center gap-1"><AlertCircle class="w-3 h-3" /> {{ form.errors.client_phone }}</div>
                                </div>
                            </div>
                            <!-- State -->
                             <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Estado de Residencia <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <MapPin class="h-4 w-4 text-slate-400" />
                                    </div>
                                    <input v-model="form.client_state" type="text" placeholder="Ej. Florida" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition shadow-sm bg-slate-50/50" required />
                                </div>
                                <div v-if="form.errors.client_state" class="text-red-500 text-xs mt-1 font-medium flex items-center gap-1"><AlertCircle class="w-3 h-3" /> {{ form.errors.client_state }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Offering & Notes -->
                    <div class="p-8 bg-slate-50/50">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Offering Selection -->
                            <div>
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
                                <p class="text-xs text-slate-500 mt-2 ml-1">Selecciona el servicio que le interesa al cliente. Tu comisión se calculará automáticamente.</p>
                            </div>

                            <!-- Notes -->
                            <div>
                                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                                     <FileText class="w-4 h-4 text-slate-400" />
                                    Notas Adicionales
                                </h2>
                                <textarea v-model="form.notes" rows="4" placeholder="Cualquier detalle extra sobre el cliente o sus necesidades..." class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4 transition shadow-sm bg-white"></textarea>
                            </div>
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
