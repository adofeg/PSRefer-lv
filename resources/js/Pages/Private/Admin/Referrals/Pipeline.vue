<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import KanbanColumn from '@/Components/Referrals/KanbanColumn.vue';
import StatusChangeModal from '@/Components/Referrals/StatusChangeModal.vue';
import { RefreshCw, LayoutGrid } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    referrals: Array,
    categories: Array,
    sectors: Array,
    filters: Object
});

const showStatusModal = ref(false);
const selectedReferral = ref(null);
const page = usePage();
const isPsAdmin = computed(() => page.props.auth?.user?.role === 'psadmin');

const columns = [
    { status: 'Prospecto', color: 'slate' },
    { status: 'Contactado', color: 'blue' },
    { status: 'En Proceso', color: 'yellow' },
    { status: 'Contactar más tarde', color: 'purple' },
    { status: 'Cerrado', color: 'green' },
    { status: 'Perdido', color: 'red' }
];

const headerColors = {
    slate: 'text-slate-700',
    blue: 'text-blue-700',
    yellow: 'text-yellow-700',
    green: 'text-green-700',
    red: 'text-red-700',
    purple: 'text-purple-700'
};

const groupedReferrals = computed(() => {
    return columns.map(col => ({
        ...col,
        referrals: props.referrals.filter(r => r.status === col.status)
    }));
});

const handleChangeStatus = (referral) => {
    if (!isPsAdmin.value) return;
    selectedReferral.value = referral;
    showStatusModal.value = true;
};

const handleStatusUpdated = () => {
    showStatusModal.value = false;
    selectedReferral.value = null;
    // Inertia auto-refreshes on success
};

const refreshPipeline = () => {
    router.reload({ only: ['referrals'] });
};

const selectedCategory = ref(props.filters?.category_id || '');
const selectedSector = ref(props.filters?.sector_id || '');

const applyFilters = () => {
    router.get(route('admin.referrals.pipeline'), { 
        category_id: selectedCategory.value,
        sector_id: selectedSector.value
    }, { 
        preserveState: true,
        preserveScroll: true 
    });
};
</script>

<template>
    <Head title="Pipeline de Referidos" />

    <AppLayout>
        <div class="h-[calc(100vh-120px)] flex flex-col">
            <!-- Header -->
            <div class="mb-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <LayoutGrid :size="28" class="text-indigo-600" />
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Pipeline de Referencias</h2>
                        <p class="text-sm text-slate-500">Vista de tablero Kanban</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Category Filter -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-slate-600 font-bold uppercase tracking-tighter text-[10px]">Servicio:</label>
                        <select 
                            v-model="selectedCategory" 
                            @change="applyFilters"
                            class="text-xs border-slate-200 rounded-lg focus:ring-indigo-500 min-w-[140px] appearance-none pr-8 bg-slate-50 font-bold"
                        >
                            <option value="">Todos</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Sector Filter -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-slate-600 font-bold uppercase tracking-tighter text-[10px]">Sector:</label>
                        <select 
                            v-model="selectedSector" 
                            @change="applyFilters"
                            class="text-xs border-slate-200 rounded-lg focus:ring-indigo-500 min-w-[140px] appearance-none pr-8 bg-indigo-50/50 font-black text-indigo-700"
                        >
                            <option value="">Todos</option>
                            <option v-for="sector in sectors" :key="sector.id" :value="sector.id">
                                {{ sector.name }}
                            </option>
                        </select>
                    </div>

                    <button 
                        @click="refreshPipeline" 
                        class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-50 transition"
                    >
                        <RefreshCw :size="16" />
                        Actualizar
                    </button>
                </div>
            </div>

            <!-- Summary Bar -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
                <div 
                    v-for="column in groupedReferrals" 
                    :key="column.status"
                    class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center"
                >
                    <span :class="['text-[10px] uppercase tracking-wider font-bold mb-1', headerColors[column.color]]">
                        {{ column.status }}
                    </span>
                    <span class="text-xl font-black text-slate-800">{{ column.referrals.length }}</span>
                </div>
            </div>

            <!-- Kanban Board -->
            <div class="flex gap-4 overflow-x-auto pb-4 h-full">
                <KanbanColumn
                    v-for="column in groupedReferrals"
                    :key="column.status"
                    :status="column.status"
                    :color="column.color"
                    :referrals="column.referrals"
                    :can-change-status="isPsAdmin"
                    @change-status="handleChangeStatus"
                />
            </div>
        </div>

        <!-- Status Change Modal -->
        <StatusChangeModal 
            v-if="selectedReferral && isPsAdmin"
            :show="showStatusModal"
            :referral-id="selectedReferral.id"
            :current-status="selectedReferral.status"
            @close="showStatusModal = false"
            @updated="handleStatusUpdated"
        />
    </AppLayout>
</template>