<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { normalizeResource } from '@/Utils/inertia';

const props = defineProps({
    commission: Object,
    associates: Array,
    statuses: Array
});

const commission = normalizeResource(props.commission, {});

const form = useForm({
    associate_id: commission.associate_id ?? '',
    referral_id: commission.referral_id ?? null,
    amount: commission.amount ?? '',
    commission_type: commission.commission_type ?? 'manual',
    status: commission.status ?? 'pending',
    notes: commission.metadata?.notes || '',
});

const commissionTypes = [
    { value: 'manual', label: 'Manual' },
    { value: 'bonus', label: 'Bono / Incentivo' },
    { value: 'adjustment', label: 'Ajuste' },
    { value: 'override', label: 'Override' },
    { value: 'direct', label: 'Directa (Estándar)' },
];

const submit = () => {
    form.put(route('admin.commissions.update', commission.id));
};

const destroy = () => {
    if (confirm('¿Estás seguro de que quieres anular esta comisión? Esta acción no se puede deshacer.')) {
        router.delete(route('admin.commissions.destroy', commission.id));
    }
};
</script>

<template>
    <Head title="Editar Comisión" />

    <AppLayout>
        <div class="max-w-2xl mx-auto space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Editar Comisión #{{ commission.id }}</h1>
                    <p class="text-slate-600">Modificar detalles de la comisión.</p>
                </div>
                <button 
                    v-if="commission.status !== 'void'"
                    @click="destroy"
                    type="button"
                    class="text-red-600 hover:text-red-700 font-medium text-sm flex items-center gap-1"
                >
                    Anular Comisión
                </button>
            </div>

            <Card>
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Read-only Referral Info if exists -->
                    <div v-if="commission.referral" class="bg-indigo-50 p-4 rounded-lg flex justify-between items-center border border-indigo-100">
                        <div>
                            <span class="text-xs font-bold text-indigo-500 uppercase">Vinculado al Referido</span>
                            <div class="text-indigo-900 font-medium">{{ commission.referral.client_name }}</div>
                            <div class="text-xs text-indigo-700">{{ commission.referral.offering?.name }}</div>
                        </div>
                    </div>

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
                            Estatus <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="form.status" value="pending" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-slate-700">Pendiente</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="form.status" value="paid" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-slate-700">Pagado</span>
                            </label>
                             <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="form.status" value="void" class="text-red-600 focus:ring-red-500">
                                <span class="text-sm text-slate-700">Anulada</span>
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
                            {{ form.processing ? 'Guardando...' : 'Actualizar Comisión' }}
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
