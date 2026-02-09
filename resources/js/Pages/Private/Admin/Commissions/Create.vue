<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { ref } from 'vue';

const props = defineProps({
    associates: Array,
    statuses: Array
});

const form = useForm({
    associate_id: '',
    referral_id: null,
    amount: '',
    commission_type: 'manual', // manual, bonus, adjustment
    status: 'pending',
    notes: ''
});

const commissionTypes = [
    { value: 'manual', label: 'Manual' },
    { value: 'bonus', label: 'Bono / Incentivo' },
    { value: 'adjustment', label: 'Ajuste' },
    { value: 'override', label: 'Override' },
];

const submit = () => {
    form.post(route('admin.commissions.store'));
};
</script>

<template>
    <Head title="Registrar Comisión" />

    <AppLayout>
        <div class="max-w-2xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Registrar Comisión</h1>
                <p class="text-slate-600">Crea un registro de comisión manual para un asociado.</p>
            </div>

            <Card>
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Associate -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Asociado Beneficiario <span class="text-red-500">*</span>
                        </label>
                        <select 
                            v-model="form.associate_id" 
                            required
                            class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">Seleccionar Asociado...</option>
                            <option v-for="assoc in associates" :key="assoc.id" :value="assoc.id">
                                {{ assoc.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.associate_id" class="text-red-500 text-xs mt-1">{{ form.errors.associate_id }}</div>
                    </div>

                    <!-- Details Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Monto <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">$</span>
                                <input 
                                    v-model="form.amount" 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    required
                                    placeholder="0.00"
                                    class="pl-7 w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                            <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Tipo de Comisión <span class="text-red-500">*</span>
                            </label>
                            <select 
                                v-model="form.commission_type" 
                                required
                                class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option v-for="type in commissionTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                            <div v-if="form.errors.commission_type" class="text-red-500 text-xs mt-1">{{ form.errors.commission_type }}</div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Estatus Inicial <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="form.status" value="pending" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-slate-700">Pendiente (Por pagar)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="form.status" value="paid" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-slate-700">Pagado (Ya desembolsado)</span>
                            </label>
                        </div>
                         <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notas / Referencia</label>
                        <textarea 
                            v-model="form.notes" 
                            rows="2" 
                            placeholder="Detalles adicionales..."
                            class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-4">
                        <button 
                            type="button" 
                            @click="$inertia.visit(route('admin.commissions.index'))" 
                            class="px-4 py-2 text-slate-600 hover:bg-slate-50 border border-slate-300 rounded-lg"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Guardando...' : 'Registrar Comisión' }}
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
