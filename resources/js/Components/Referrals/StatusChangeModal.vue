<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { X, RefreshCw } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    referralId: [String, Number],
    currentStatus: String
});

const emit = defineEmits(['close', 'updated']);

const statusOptions = [
    { value: 'Prospecto', label: 'Prospecto', color: 'bg-slate-100 text-slate-700' },
    { value: 'Contactado', label: 'Contactado', color: 'bg-blue-100 text-blue-700' },
    { value: 'En Proceso', label: 'En Proceso', color: 'bg-yellow-100 text-yellow-700' },
    { value: 'Cerrado', label: 'Cerrado (Ganado)', color: 'bg-green-100 text-green-700' },
    { value: 'Perdido', label: 'Perdido', color: 'bg-red-100 text-red-700' }
];

const form = useForm({
    status: '',
    notes: '',
    deal_value: null,
    contract_id: '',
    payment_method: '',
    down_payment: null,
    agency_fee: null,
});

const selectedStatusLabel = computed(() => {
    const status = statusOptions.find(s => s.value === form.status);
    return status ? status.label : '';
});

const submitStatusChange = () => {
    if (!form.status || !form.notes.trim()) {
        alert('Por favor selecciona un estado y agrega un comentario');
        return;
    }

    form.patch(route('admin.referrals.update', props.referralId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('updated');
            emit('close');
        }
    });
};

const closeModal = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 backdrop-blur-sm" @click.self="closeModal">
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div v-if="show" class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                        <!-- Header -->
                        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <div>
                                <h3 class="font-bold text-slate-800">Cambiar Estado del Referido</h3>
                                <p class="text-xs text-slate-500">Estado actual: {{ currentStatus }}</p>
                            </div>
                            <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition">
                                <X :size="20" />
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 space-y-4">
                            <!-- Status Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Nuevo Estado *</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        v-for="status in statusOptions"
                                        :key="status.value"
                                        @click="form.status = status.value"
                                        :class="[
                                            'px-3 py-2 rounded-lg font-medium text-sm transition border-2',
                                            form.status === status.value
                                                ? 'border-indigo-600 ' + status.color
                                                : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'
                                        ]"
                                    >
                                        {{ status.label }}
                                    </button>
                                </div>
                            </div>

                            <!-- Comment (Required) -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Comentario del Cambio *
                                    <span class="text-xs text-slate-500 font-normal">(Obligatorio)</span>
                                </label>
                                <textarea
                                    v-model="form.notes"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                                    placeholder="Ej: Cliente respondió positivamente, agendamos llamada para el lunes..."
                                    required
                                ></textarea>
                                <p class="text-xs text-slate-500 mt-1">Este comentario quedará registrado en la bitácora</p>
                            </div>

                            <!-- Financial Details (Visible for Closed) -->
                            <div v-if="form.status === 'Cerrado'" class="space-y-4 pt-4 border-t border-slate-100">
                                <h4 class="font-semibold text-slate-800 text-sm">Detalles Financieros</h4>
                                
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Método de Pago</label>
                                        <select
                                            v-model="form.payment_method"
                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                        >
                                            <option value="">Seleccionar...</option>
                                            <option value="ACH">Transferencia ACH</option>
                                            <option value="T. Crédito">Tarj. Crédito</option>
                                            <option value="Efectivo">Efectivo</option>
                                        </select>
                                        <div v-if="form.errors.payment_method" class="text-red-500 text-xs mt-1">
                                            {{ form.errors.payment_method }}
                                        </div>
                                    </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Valor Total de la Venta (Deal Value)</label>
                                        <input
                                            v-model="form.deal_value"
                                            type="number"
                                            step="0.01"
                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                            placeholder="0.00"
                                        />
                                        <div v-if="form.errors.deal_value" class="text-red-500 text-xs mt-1">
                                            {{ form.errors.deal_value }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Anticipo Recibido (Down Payment)</label>
                                        <input
                                            v-model="form.down_payment"
                                            type="number"
                                            step="0.01"
                                            :max="form.deal_value"
                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                            :class="{'border-red-300 focus:ring-red-500': form.down_payment > form.deal_value}"
                                            placeholder="0.00"
                                        />
                                        <div v-if="form.errors.down_payment" class="text-red-500 text-xs mt-1">
                                            {{ form.errors.down_payment }}
                                        </div>
                                        <div v-if="form.down_payment > form.deal_value" class="text-red-500 text-xs mt-1 font-bold">
                                            El anticipo no puede ser mayor al total.
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Status Feedback -->
                                <div v-if="form.deal_value > 0" class="p-3 rounded-lg text-sm border transition-all"
                                    :class="form.down_payment >= form.deal_value ? 'bg-green-50 border-green-200 text-green-700' : 'bg-amber-50 border-amber-200 text-amber-700'">
                                    <div class="flex items-center gap-2 font-bold">
                                        <span v-if="form.down_payment >= form.deal_value">✓ COMISIÓN GENERADA (PENDIENTE DE APROBACIÓN)</span>
                                        <span v-else>⚠ COMISIÓN QUEDARÁ PENDIENTE</span>
                                    </div>
                                    <p class="text-xs mt-1 opacity-90">
                                        <span v-if="form.down_payment >= form.deal_value">El pago está completo. La comisión ha sido registrada y está pendiente de aprobación por administración.</span>
                                        <span v-else>Solo hay un anticipo. La comisión se registrará pero no se procesará hasta completar el pago.</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-4 border-t border-slate-100 bg-slate-50 flex gap-2">
                            <button @click="closeModal" class="flex-1 py-2 bg-white border border-slate-200 text-slate-600 font-medium rounded-lg hover:bg-slate-100 transition">
                                Cancelar
                            </button>
                            <button
                                @click="submitStatusChange"
                                :disabled="!form.status || !form.notes.trim() || form.processing || (Number(form.down_payment) > Number(form.deal_value) && form.deal_value > 0)"
                                class="flex-1 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ form.processing ? 'Guardando...' : 'Confirmar Cambio' }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
