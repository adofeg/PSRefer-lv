<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import Modal from '@/Components/UI/Modal.vue';
import { computed, ref, watch } from 'vue';
import { Search, Filter, FileText, X } from 'lucide-vue-next';
import { useFormatters } from '@/Composables/useFormatters';
import debounce from 'lodash/debounce';
import { normalizePaginated } from '@/Utils/inertia';

const props = defineProps({
    logs: Object,
    filters: Object,
    actions: Array,
});

const { formatDate } = useFormatters();
const logsResource = computed(() => normalizePaginated(props.logs));
const search = ref(props.filters?.search || '');
const actionFilter = ref(props.filters?.action || '');

const showDiffModal = ref(false);
const selectedLog = ref(null);

const performSearch = debounce(() => {
    router.get(route('admin.audit-logs.index'), { 
        search: search.value,
        action: actionFilter.value 
    }, { 
        preserveState: true, 
        preserveScroll: true,
        replace: true 
    });
}, 300);

watch([search, actionFilter], performSearch);

const clearFilters = () => {
    search.value = '';
    actionFilter.value = '';
};

const openDiffModal = (log) => {
    selectedLog.value = log;
    showDiffModal.value = true;
};

const formatEntity = (log) => {
    if (log.auditable_type) {
        const type = log.auditable_type.split('\\').pop();
        return `${type} #${log.auditable_id}`;
    }
    return log.entity || 'N/A';
};

const getActionColor = (action) => {
    const map = {
        'CREATE': 'bg-green-100 text-green-800 border-green-200',
        'UPDATE': 'bg-blue-100 text-blue-800 border-blue-200',
        'DELETE': 'bg-red-100 text-red-800 border-red-200',
        'LOGIN': 'bg-purple-100 text-purple-800 border-purple-200',
        'LOGOUT': 'bg-gray-100 text-gray-800 border-gray-200',
    };
    return map[action.toUpperCase()] || 'bg-slate-100 text-slate-800 border-slate-200';
};
</script>

<template>
    <Head title="Bitácora de Auditoría" />

    <AppLayout>
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                        <FileText :size="24" class="text-indigo-600" />
                        Bitácora de Auditoría
                    </h1>
                    <p class="text-slate-500 text-sm">Historial completo de acciones y cambios en el sistema.</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search -->
                <div class="relative w-full md:w-96">
                    <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input 
                        v-model="search"
                        type="text" 
                        placeholder="Buscar por descripción, entidad..." 
                        class="pl-10 pr-4 py-2.5 w-full bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                    >
                </div>

                <!-- Filters Group -->
                <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
                    <div class="relative min-w-[200px]">
                        <select 
                            v-model="actionFilter" 
                            class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 pr-8 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm cursor-pointer"
                        >
                            <option value="">Todas las Acciones</option>
                            <option v-for="act in actions" :key="act" :value="act">{{ act }}</option>
                        </select>
                        <Filter :size="14" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    </div>

                     <!-- Reset -->
                     <button 
                        v-if="search || actionFilter"
                        @click="clearFilters"
                        class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
                        title="Limpiar Filtros"
                    >
                        <X :size="18" />
                    </button>
                </div>
            </div>

            <!-- Table -->
            <Card class="overflow-hidden p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Usuario (Actor)</th>
                                <th class="px-6 py-4">Acción</th>
                                <th class="px-6 py-4">Entidad</th>
                                <th class="px-6 py-4">Descripción</th>
                                <th class="px-6 py-4 text-right">Detalles</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="log in logsResource.data" :key="log.id" class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                    <div class="font-medium text-slate-700">{{ formatDate(log.created_at) }}</div>
                                    <div class="text-xs text-slate-400">{{ new Date(log.created_at).toLocaleTimeString() }}</div>
                                </td>
                                <td class="px-6 py-4">
                                     <div v-if="log.actorable" class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 uppercase">
                                            {{ log.actorable.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-800">{{ log.actorable.name }}</div>
                                            <div class="text-xs text-slate-500">{{ log.actorable.email }}</div>
                                        </div>
                                     </div>
                                     <span v-else class="text-slate-400 italic text-xs">Sistema / Automático</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['px-2.5 py-1 rounded-full text-xs font-bold border', getActionColor(log.action)]">
                                        {{ log.action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                    {{ formatEntity(log) }}
                                </td>
                                <td class="px-6 py-4 text-slate-700 max-w-xs truncate" :title="log.description">
                                    {{ log.description }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button 
                                        @click="openDiffModal(log)"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-medium hover:underline disabled:opacity-50 disabled:cursor-not-allowed"
                                        :disabled="!log.new_data && !log.previous_data"
                                    >
                                        Ver Cambios
                                    </button>
                                </td>
                            </tr>
                             <tr v-if="logsResource.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <FileText :size="32" class="opacity-50" />
                                        <p>No se encontraron registros de auditoría.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination v-if="logsResource.data.length > 0" :links="logsResource.links" class="p-4 border-t border-slate-100" />
            </Card>
        </div>

        <!-- Diff Modal -->
        <Modal :show="showDiffModal" @close="showDiffModal = false" maxWidth="4xl">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Detalle de Cambios</h2>
                        <p class="text-sm text-slate-500 mt-1" v-if="selectedLog">
                            {{ selectedLog.description }} • {{ formatDate(selectedLog.created_at) }}
                        </p>
                    </div>
                    <button @click="showDiffModal = false" class="text-slate-400 hover:text-slate-600">
                        <X :size="24" />
                    </button>
                </div>

                <div v-if="selectedLog" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Old Values -->
                    <div class="space-y-2">
                        <h3 class="font-semibold text-slate-700 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Valor Anterior
                        </h3>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 font-mono text-xs text-slate-600 overflow-auto h-96">
                            <pre v-if="selectedLog.previous_data">{{ JSON.stringify(selectedLog.previous_data, null, 2) }}</pre>
                            <span v-else class="text-slate-400 italic">No hay datos anteriores (Creación o sin cambios registrados)</span>
                        </div>
                    </div>

                    <!-- New Values -->
                    <div class="space-y-2">
                         <h3 class="font-semibold text-slate-700 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Nuevo Valor
                        </h3>
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 font-mono text-xs text-indigo-900 overflow-auto h-96">
                            <pre v-if="selectedLog.new_data">{{ JSON.stringify(selectedLog.new_data, null, 2) }}</pre>
                             <span v-else class="text-slate-400 italic">No hay datos nuevos (Eliminación o sin cambios registrados)</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button 
                        @click="showDiffModal = false"
                        class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition font-medium text-sm"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
