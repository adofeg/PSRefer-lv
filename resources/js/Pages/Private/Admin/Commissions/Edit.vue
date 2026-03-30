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
    commission_type: commission.commission_type ?? 'variable',
    status: commission.status ?? 'pending',
    notes: commission.metadata?.notes || '',
    receipt_file: null,
});

const commissionTypes = [
    { value: 'percentage', label: 'Porcentaje (Venta)' },
    { value: 'fixed', label: 'Monto Fijo' },
    { value: 'variable', label: 'Variable (Manual)' },
];

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('admin.commissions.update', commission.id), {
        forceFormData: true,
    });
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
                    v-if="commission.status !== 'void' && !commission.referral_id"
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
                    <div v-if="commission.referral" class="bg-indigo-50 p-4 rounded-xl flex flex-col gap-3 border border-indigo-100 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Referido Vinculado</span>
                                <div class="text-indigo-900 font-bold text-lg leading-tight">{{ commission.referral.client_name }}</div>
                                <div class="text-xs text-indigo-700 font-medium">{{ commission.referral.offering?.name }}</div>
                            </div>
                            <div class="bg-white/80 backdrop-blur-sm px-3 py-2 rounded-lg border border-indigo-200 text-right">
                                <span class="text-[10px] font-bold text-slate-500 uppercase block mb-0.5">Regla de Comisión</span>
                                <div v-if="commission.referral.offering?.commission_type === 'fixed'" class="text-indigo-600 font-black">
                                    ${{ commission.referral.offering?.base_commission }} (Fijo)
                                </div>
                                <div v-else-if="commission.referral.offering?.commission_type === 'percentage'" class="text-indigo-600 font-black">
                                    {{ commission.referral.offering?.base_commission }}% (Porcentaje)
                                </div>
                                <div v-else class="text-indigo-600 font-black">
                                    Variable / Manual
                                </div>
                            </div>
                        </div>
                        <div v-if="commission.referral.offering?.commission_type !== 'fixed'" class="text-[10px] text-indigo-400 font-medium italic">
                            * El monto se creó en $0 porque debe calcularse basado en el valor final de la venta.
                        </div>
                    </div>

                    <!-- Associate -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Asociado Beneficiario <span class="text-red-500">*</span>
                        </label>
                        <!-- Editable if Manual (no referral) -->
                        <select 
                            v-if="!commission.referral_id"
                            v-model="form.associate_id" 
                            required
                            class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option v-for="assoc in associates" :key="assoc.id" :value="assoc.id">
                                {{ assoc.name }}
                            </option>
                        </select>
                        <!-- Readonly if Automatic (has referral) -->
                        <div v-else class="p-3 bg-slate-100/50 border border-slate-200 rounded-lg text-slate-700 font-medium">
                            <span v-if="commission.associate?.user">
                                {{ commission.associate.user.name }} ({{ commission.associate.user.email }})
                            </span>
                            <span v-else class="text-slate-400 italic">Asociado desconocido</span>
                        </div>
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
                            <!-- Editable if Manual -->
                            <select 
                                v-if="!commission.referral_id"
                                v-model="form.commission_type" 
                                required
                                class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option v-for="type in commissionTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                            <!-- Readonly if Automatic -->
                            <div v-else class="p-3 bg-slate-100/50 border border-slate-200 rounded-lg text-slate-700 font-medium uppercase text-xs tracking-wide">
                                {{ commissionTypes.find(t => t.value === form.commission_type)?.label || 'Desconocido' }}
                            </div>
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
                        </div>
                         <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</div>
                    </div>

                    <!-- Recibo / Comprobante -->
                    <div class="border-t pt-4 mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Comprobante de Pago</label>
                        
                        <div v-if="commission.receipt" class="mb-3 p-3 bg-slate-50 border rounded-lg flex justify-between items-center text-sm">
                            <div class="flex items-center gap-2 text-slate-600">
                                <span class="font-medium">Archivo actual:</span>
                                <a :href="route('assets.download', commission.receipt.id)" target="_blank" class="text-indigo-600 hover:underline">Ver Comprobante</a>
                            </div>
                        </div>

                        <input 
                            @input="form.receipt_file = $event.target.files[0]"
                            type="file" 
                            accept="image/*,application/pdf"
                            class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                            "
                        />
                        <div v-if="form.errors.receipt_file" class="text-red-500 text-xs mt-1">{{ form.errors.receipt_file }}</div>
                        <p class="text-xs text-slate-400 mt-1">Sube una imagen o PDF del comprobante de pago.</p>
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
