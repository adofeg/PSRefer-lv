<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    offerings: Array,
    selectedOffering: Object, 
});

const form = useForm({
    client_name: '',
    client_email: '',
    client_phone: '',
    client_state: '',
    offering_id: props.selectedOffering?.id || '',
    notes: '',
});

const submit = () => {
    form.post(route('associate.referrals.store'));
};
</script>

<template>
    <Head title="Nuevo Referido" />

    <AppLayout>
        <div class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
             <div class="mb-6">
                <Link :href="route('associate.referrals.index')" class="flex items-center text-gray-500 hover:text-gray-700 transition">
                    <ArrowLeft class="w-5 h-5 mr-2" />
                    Volver a Mis Referidos
                </Link>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-bold text-slate-800">Registrar Nuevo Referido</h2>
                    <p class="text-sm text-slate-500">Ingresa los datos de tu cliente potencial</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Client Info -->
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre del Cliente <span class="text-red-500">*</span></label>
                            <input v-model="form.client_name" type="text" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" required />
                            <div v-if="form.errors.client_name" class="text-red-500 text-xs mt-1">{{ form.errors.client_name }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input v-model="form.client_email" type="email" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" required />
                                <div v-if="form.errors.client_email" class="text-red-500 text-xs mt-1">{{ form.errors.client_email }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono <span class="text-red-500">*</span></label>
                                <input v-model="form.client_phone" type="tel" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" required />
                                <div v-if="form.errors.client_phone" class="text-red-500 text-xs mt-1">{{ form.errors.client_phone }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Estado de Residencia <span class="text-red-500">*</span></label>
                                <input v-model="form.client_state" type="text" placeholder="Ej. Florida" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" required />
                                <div v-if="form.errors.client_state" class="text-red-500 text-xs mt-1">{{ form.errors.client_state }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Offering Selection -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Servicio de Interés <span class="text-red-500">*</span></label>
                        <select v-model="form.offering_id" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="" disabled>Selecciona un servicio</option>
                            <option v-for="offering in offerings" :key="offering.id" :value="offering.id">
                                {{ offering.name }} (Comisión: {{ offering.commission_rate ? `${offering.commission_rate}%` : `$${offering.base_commission}` }})
                            </option>
                        </select>
                        <div v-if="form.errors.offering_id" class="text-red-500 text-xs mt-1">{{ form.errors.offering_id }}</div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notas Adicionales</label>
                        <textarea v-model="form.notes" rows="3" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end pt-4 gap-3">
                         <Link :href="route('associate.referrals.index')" class="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg border border-slate-300 transition">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium shadow-sm disabled:opacity-50">
                            {{ form.processing ? 'Guardando...' : 'Crear Referido' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>