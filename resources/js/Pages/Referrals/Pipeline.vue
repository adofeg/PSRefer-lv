<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import KanbanColumn from '@/Components/KanbanColumn.vue';
import StatusChangeModal from '@/Components/StatusChangeModal.vue';
import { RefreshCw, LayoutGrid } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    referrals: Array
});

const showStatusModal = ref(false);
const selectedReferral = ref(null);

const columns = [
    { status: 'Prospecto', color: 'slate' },
    { status: 'Contactado', color: 'blue' },
    { status: 'En Proceso', color: 'yellow' },
    { status: 'Cerrado', color: 'green' },
    { status: 'Perdido', color: 'red' }
];

const groupedReferrals = computed(() => {
    return columns.map(col => ({
        ...col,
        referrals: props.referrals.filter(r => r.status === col.status)
    }));
});

const handleChangeStatus = (referral) => {
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
</script>

<template>
    <Head title="Pipeline de Referidos" />

    <AuthenticatedLayout>
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
                <button 
                    @click="refreshPipeline" 
                    class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-50 transition"
                >
                    <RefreshCw :size="16" />
                    Actualizar
                </button>
            </div>

            <!-- Kanban Board -->
            <div class="flex gap-4 overflow-x-auto pb-4 h-full">
                <KanbanColumn
                    v-for="column in groupedReferrals"
                    :key="column.status"
                    :status="column.status"
                    :color="column.color"
                    :referrals="column.referrals"
                    @change-status="handleChangeStatus"
                />
            </div>
        </div>

        <!-- Status Change Modal -->
        <StatusChangeModal 
            v-if="selectedReferral"
            :show="showStatusModal"
            :referral-id="selectedReferral.id"
            :current-status="selectedReferral.status"
            @close="showStatusModal = false"
            @updated="handleStatusUpdated"
        />
    </AuthenticatedLayout>
</template>
