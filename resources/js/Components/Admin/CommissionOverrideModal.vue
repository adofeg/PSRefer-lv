<script setup>
import { ref, watch, onMounted } from 'vue';
import { X, Save, Trash2, Plus, DollarSign, Loader2 } from 'lucide-vue-next';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    user: Object
});

const emit = defineEmits(['close']);

const offerings = ref([]);
const overrides = ref([]);
const loading = ref(true);
const saving = ref(false);

const form = ref({
    offering_id: '',
    commission_rate: ''
});

const loadData = async () => {
    if (!props.user) return;

    const associateId = props.user?.profileable_type?.includes('Associate')
        ? props.user.profileable_id
        : props.user?.associate_id;
    if (!associateId) return;
    
    loading.ref = true;
    try {
        const [offeringsRes, overridesRes] = await Promise.all([
            axios.get(route('admin.offerings.index'), { params: { json: true } }),
            axios.get(route('admin.commissions.overrides.index'), { params: { associate_id: associateId } })
        ]);
        
        // Handling possibly different response structures
        offerings.value = offeringsRes.data.data || offeringsRes.data;
        overrides.value = overridesRes.data;
    } catch (error) {
        console.error('Error loading override data:', error);
    } finally {
        loading.value = false;
    }
};

watch(() => props.show, (newVal) => {
    if (newVal && props.user) {
        loadData();
    }
});

const handleSave = async () => {
    if (!form.value.offering_id || !form.value.commission_rate) return;

    const associateId = props.user?.profileable_type?.includes('Associate')
        ? props.user.profileable_id
        : props.user?.associate_id;
    if (!associateId) return;

    saving.value = true;
    try {
        await axios.post(route('admin.commissions.overrides.store'), {
            associate_id: associateId,
            offering_id: form.value.offering_id,
            commission_rate: parseFloat(form.value.commission_rate)
        });
        
        form.value = { offering_id: '', commission_rate: '' };
        await loadData();
    } catch (error) {
        console.error('Error saving override:', error);
    } finally {
        saving.value = false;
    }
};

const deleteOverride = async (id) => {
    if (!confirm('¿Eliminar esta comisión personalizada?')) return;
    
    try {
        await axios.delete(route('admin.commissions.overrides.destroy', id));
        await loadData();
    } catch (error) {
        console.error('Error deleting override:', error);
    }
};

const getStandardRate = (offeringId) => {
    const off = offerings.value.find(o => o.id === offeringId);
    return off ? off.commission_rate : 0;
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Comisiones: {{ user?.name }}</h2>
                    <p class="text-sm text-slate-500">Configura comisiones personalizadas por producto</p>
                </div>
                <button @click="$emit('close')" class="p-2 hover:bg-slate-100 rounded-lg text-slate-500">
                    <X :size="24" />
                </button>
            </div>

            <div class="space-y-6">
                <!-- Add New Override -->
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Oferta / Producto</label>
                            <select
                                v-model="form.offering_id"
                                class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Seleccionar producto...</option>
                                <option v-for="off in offerings" :key="off.id" :value="off.id">
                                    {{ off.name }} (Std: {{ off.commission_rate }}%)
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Comisión %</label>
                            <div class="flex gap-2">
                                <input
                                    v-model="form.commission_rate"
                                    type="number"
                                    step="0.1"
                                    class="flex-1 p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Rate especial"
                                />
                                <button
                                    @click="handleSave"
                                    :disabled="saving || !form.offering_id || !form.commission_rate"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg flex items-center gap-2 hover:bg-indigo-700 disabled:opacity-50 transition"
                                >
                                    <Plus v-if="!saving" :size="18" />
                                    <Loader2 v-else :size="18" class="animate-spin" />
                                    {{ saving ? '...' : 'Añadir' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List Overrides -->
                <div class="space-y-3">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 px-1">
                        <DollarSign :size="18" class="text-green-600" />
                        Comisiones Personalizadas Activas
                    </h3>

                    <div v-if="loading" class="flex justify-center py-8">
                        <Loader2 :size="32" class="text-indigo-600 animate-spin" />
                    </div>

                    <div v-else-if="overrides.length === 0" class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <p class="text-slate-400 italic">Este usuario usa las comisiones estándar en todos los productos</p>
                    </div>

                    <div v-else class="border border-slate-200 rounded-xl divide-y divide-slate-100 overflow-hidden shadow-sm">
                        <div v-for="ov in overrides" :key="ov.id" class="p-4 flex justify-between items-center group hover:bg-slate-50 transition">
                            <div>
                                <div class="font-bold text-slate-900">{{ ov.offering?.name }}</div>
                                <div class="text-sm text-slate-500">
                                    Estándar: {{ getStandardRate(ov.offering_id) }}%
                                    <span class="mx-2">→</span>
                                    <span class="font-bold text-green-600">Personalizado: {{ ov.commission_rate }}%</span>
                                </div>
                            </div>
                            <button 
                                @click="deleteOverride(ov.id)"
                                class="p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                title="Eliminar override"
                            >
                                <Trash2 :size="18" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>
