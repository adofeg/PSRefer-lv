<script setup>
import { ref, watch } from 'vue';
import { X, Save, Trash2, Plus, DollarSign, Loader2 } from 'lucide-vue-next';
import Modal from '@/Components/UI/Modal.vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue';
import axios from 'axios';
import { normalizeCollection } from '@/Utils/inertia';

const props = defineProps({
    show: Boolean,
    user: Object
});

const emit = defineEmits(['close']);

const offerings = ref([]);
const overrides = ref([]);
const loading = ref(true);
const saving = ref(false);
const confirmDelete = ref(false);
const itemToDelete = ref(null);

const form = ref({
    offering_id: '',
    base_commission: ''
});

const loadData = async () => {
    if (!props.user) return;

    const associateId = props.user?.profileable_type?.includes('Associate')
        ? props.user.profileable_id
        : props.user?.associate_id;
    if (!associateId) return;
    
    // Clear previous data to prevent stale state
    overrides.value = [];
    loading.value = true;
    
    try {
        const [offeringsRes, overridesRes] = await Promise.all([
            axios.get(route('admin.offerings.index'), { params: { json: true } }),
            axios.get(route('admin.commissions.overrides.index'), { params: { associate_id: associateId } })
        ]);
        
        offerings.value = normalizeCollection(offeringsRes.data);
        overrides.value = normalizeCollection(overridesRes.data);
    } catch (error) {
        console.error('Error loading override data:', error);
    } finally {
        loading.value = false;
    }
};

watch(() => [props.show, props.user?.id], ([isOpen]) => {
    if (isOpen && props.user) {
        loadData();
    }
});

const handleSave = async () => {
    if (!form.value.offering_id || !form.value.base_commission) return;

    const associateId = props.user?.profileable_type?.includes('Associate')
        ? props.user.profileable_id
        : props.user?.associate_id;
    if (!associateId) return;

    saving.value = true;
    try {
        await axios.post(route('admin.commissions.overrides.store'), {
            associate_id: associateId,
            offering_id: form.value.offering_id,
            base_commission: parseFloat(form.value.base_commission)
        });
        
        form.value = { offering_id: '', base_commission: '' };
        await loadData();
    } catch (error) {
        console.error('Error saving override:', error);
    } finally {
        saving.value = false;
    }
};

const promptDelete = (id) => {
    itemToDelete.value = id;
    confirmDelete.value = true;
};

const executeDelete = async () => {
    if (!itemToDelete.value) return;
    
    try {
        await axios.delete(route('admin.commissions.overrides.destroy', itemToDelete.value));
        await loadData();
        confirmDelete.value = false;
        itemToDelete.value = null;
    } catch (error) {
        console.error('Error deleting override:', error);
    }
};

const getStandardRate = (offeringId) => {
    const off = offerings.value.find(o => o.id === offeringId);
    return off ? Number(off.base_commission || 0) : 0;
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="2xl">
        <div class="relative bg-white rounded-2xl overflow-hidden">
            <!-- Header with pattern/color -->
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-500/20 p-2 rounded-lg backdrop-blur-sm">
                        <DollarSign class="text-white" :size="24" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold leading-tight">Comisiones Personalizadas</h2>
                        <p class="text-indigo-100 text-xs opacity-90">{{ user?.name }}</p>
                    </div>
                </div>
                <button @click="$emit('close')" class="p-1 hover:bg-white/10 rounded-full transition">
                    <X :size="20" />
                </button>
            </div>

            <div class="p-6 space-y-8">
                <!-- Add New Section -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-sm font-bold text-slate-800 uppercase tracking-wider">
                        <Plus :size="16" class="text-indigo-600" />
                        Agregar Excepción
                    </div>
                    <div class="bg-slate-50 p-1 rounded-xl border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-7">
                            <select
                                v-model="form.offering_id"
                                class="w-full bg-white border-0 rounded-lg focus:ring-2 focus:ring-indigo-500 py-2.5 text-sm"
                            >
                                <option value="">Seleccionar producto...</option>
                                <option v-for="off in offerings" :key="off.id" :value="off.id">
                                    {{ off.name }} (Base: {{ off.base_commission }}%)
                                </option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <input
                                v-model="form.base_commission"
                                type="number"
                                step="0.1"
                                class="w-full bg-white border-0 rounded-lg focus:ring-2 focus:ring-indigo-500 py-2.5 text-sm"
                                placeholder="% Comisión"
                            />
                        </div>
                        <div class="md:col-span-2">
                            <button
                                @click="handleSave"
                                :disabled="saving || !form.offering_id || !form.base_commission"
                                class="w-full h-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <Loader2 v-if="saving" :size="18" class="animate-spin" />
                                <Save v-else :size="18" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- List Section -->
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        Configuraciones Activas
                        <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full">{{ overrides.length }}</span>
                    </h3>

                     <div v-if="loading" class="flex justify-center py-8">
                        <Loader2 :size="32" class="text-indigo-600 animate-spin" />
                    </div>

                    <div v-else-if="overrides.length === 0" class="text-center py-10 border-2 border-dashed border-slate-200 rounded-xl">
                        <DollarSign :size="32" class="mx-auto text-slate-300 mb-2" />
                        <p class="text-slate-400 text-sm">No hay comisiones personalizadas.</p>
                        <p class="text-slate-400 text-xs">Se aplicarán las tasas estándar del sistema.</p>
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="ov in overrides" :key="ov.id" class="bg-white border border-slate-100 hover:border-indigo-100 shadow-sm hover:shadow-md rounded-xl p-4 flex justify-between items-center transition group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600 font-bold text-xs ring-4 ring-white">
                                    {{ ov.base_commission }}%
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800">{{ ov.offering?.name }}</h4>
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <span>Estándar: {{ getStandardRate(ov.offering_id) }}%</span>
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span class="text-indigo-600 font-medium">+{{ (ov.base_commission - getStandardRate(ov.offering_id)).toFixed(1) }}% Mejora</span>
                                    </div>
                                </div>
                            </div>
                            
                            <button 
                                @click="promptDelete(ov.id)"
                                class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition opacity-0 group-hover:opacity-100"
                                title="Eliminar regla"
                            >
                                <Trash2 :size="18" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>

    <ConfirmModal 
        :show="confirmDelete"
        title="¿Eliminar Comisión Personalizada?"
        message="El usuario volverá a recibir la comisión estándar para este producto. ¿Estás seguro?"
        confirmText="Sí, Eliminar"
        @close="confirmDelete = false"
        @confirm="executeDelete"
    />
</template>
