<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import Modal from '@/Components/UI/Modal.vue'; 
import { computed, ref } from 'vue';
import { Plus, Trash2, Edit } from 'lucide-vue-next';
import { normalizeCollection, normalizePaginated } from '@/Utils/inertia';

const props = defineProps({
    overrides: Object,
    associates: Array,
    offerings: Array,
});

const isModalOpen = ref(false);
const editingOverride = ref(null);
const overridesResource = computed(() => normalizePaginated(props.overrides));
const associatesList = computed(() => normalizeCollection(props.associates));
const offeringsList = computed(() => normalizeCollection(props.offerings));

const form = useForm({
    associate_id: '',
    offering_id: '',
    base_commission: '',
});

const openModal = (override = null) => {
    editingOverride.value = override;
    if (override) {
        form.associate_id = override.associate_id;
        form.offering_id = override.offering_id || ''; // Handle null (global override) if supported
        form.base_commission = override.base_commission;
    } else {
        form.reset();
    }
    isModalOpen.value = true;
};

const submit = () => {
    // Assuming backend uses same store for upsert
    form.post(route('admin.commissions.overrides.store'), {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        }
    });
};

const destroy = (id) => {
    if (confirm('¿Eliminar esta excepción?')) {
        router.delete(route('admin.commissions.overrides.destroy', id));
    }
};
</script>

<template>
    <Head title="Reglas y Excepciones" />

    <AppLayout>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Reglas y Excepciones</h1>
                    <p class="text-slate-500">Configura tasas de comisión personalizadas por Asociado y Oferta.</p>
                </div>
                <button 
                    @click="openModal()"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2 shadow-sm transition"
                >
                    <Plus :size="20" /> Nueva Regla
                </button>
            </div>

            <Card class="overflow-hidden p-0">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Asociado</th>
                            <th class="px-6 py-4">Oferta Específica</th>
                            <th class="px-6 py-4 text-right">Tasa Personalizada</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="override in overridesResource.data" :key="override.id" class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ override.associate?.user?.name }}
                                <div class="text-xs text-slate-500">{{ override.associate?.user?.email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span v-if="override.offering" class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold">
                                    {{ override.offering.name }}
                                </span>
                                <span v-else class="text-slate-400 italic">Todas las ofertas (Default)</span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-bold text-emerald-600">
                                {{ override.base_commission }}%
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <button @click="openModal(override)" class="text-indigo-600 hover:bg-indigo-50 p-1 rounded">
                                    <Edit :size="18" />
                                </button>
                                <button @click="destroy(override.id)" class="text-red-500 hover:bg-red-50 p-1 rounded">
                                    <Trash2 :size="18" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="overridesResource.data.length === 0" class="p-8 text-center text-slate-400">
                    No hay excepciones configuradas. Se usan las tasas estándar.
                </div>
            </Card>
        </div>

        <!-- Modal -->
        <Modal :show="isModalOpen" @close="isModalOpen = false">
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">
                    {{ editingOverride ? 'Editar Regla' : 'Nueva Excepción' }}
                </h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Asociado</label>
                        <select v-model="form.associate_id" class="w-full border-slate-300 rounded-lg" required>
                            <option value="" disabled>Seleccionar Asociado</option>
                            <option v-for="assoc in associatesList" :key="assoc.id" :value="assoc.id">
                                {{ assoc.name }}
                            </option>
                        </select>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Oferta (Opcional)</label>
                        <select v-model="form.offering_id" class="w-full border-slate-300 rounded-lg">
                            <option value="">Aplicar a todas las ofertas</option>
                            <option v-for="offer in offeringsList" :key="offer.id" :value="offer.id">
                                {{ offer.name }}
                            </option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Si se deja vacío, aplica como nueva tasa base para este asociado.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tasa de Comisión (%)</label>
                        <input type="number" step="0.01" v-model="form.base_commission" class="w-full border-slate-300 rounded-lg" required>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2 border rounded text-slate-600">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700" :disabled="form.processing">Guardar</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
