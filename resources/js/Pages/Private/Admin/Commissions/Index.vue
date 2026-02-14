<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue'; // Assuming Badge handles generic status or I need to update it
import { Search, Filter, X, DollarSign, User, FileText, Calendar, Plus } from 'lucide-vue-next';
import { useFormatters } from '@/Composables/useFormatters';
import { normalizePaginated } from '@/Utils/inertia';

const props = defineProps({
    commissions: Object,
    filters: Object,
    statuses: Array
});

const commissionsResource = computed(() => normalizePaginated(props.commissions));

const { formatCurrency, formatShortDate } = useFormatters();

// Filter State
const searchTerm = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const applyFilters = debounce(() => {
    router.get(
        route('admin.commissions.index'),
        { 
            search: searchTerm.value,
            status: statusFilter.value 
        },
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true 
        }
    );
}, 300);

const clearFilters = () => {
    searchTerm.value = '';
    statusFilter.value = 'all';
};

watch([searchTerm, statusFilter], () => {
    applyFilters();
});

// Helper for Badge Colors (if generic Badge doesn't cover it)
const getStatusColor = (status) => {
    switch(status) {
        case 'paid': return 'success';
        case 'pending': return 'warning';
        case 'void': return 'danger';
        default: return 'neutral';
    }
};
</script>

<template>
    <Head title="Gestión de Comisiones" />

    <AppLayout>
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Gestión de Comisiones</h1>
                    <p class="text-slate-500">Administra y audita los pagos de comisiones a los asociados.</p>
                </div>
                <div class="flex gap-3">
                     <Link
                        :href="route('admin.commissions.report')"
                        class="bg-white text-slate-600 border border-slate-300 px-4 py-2 rounded-lg hover:bg-slate-50 flex items-center gap-2 shadow-sm transition"
                    >
                        <FileText :size="20" /> Reportes
                    </Link>
                     <Link
                            v-if="$page.props.auth.user.role !== 'psadmin'"
                           :href="route('admin.commissions.create')"
                           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2 shadow-sm transition"
                       >
                         <Plus :size="20" /> Nueva Comisión
                      </Link>
                </div>
            </div>

            <!-- Professional Filter Bar -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search -->
                <div class="relative w-full md:w-96">
                    <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input 
                        v-model="searchTerm"
                        type="text" 
                        placeholder="Buscar por asociado o cliente..." 
                        class="pl-10 pr-4 py-2.5 w-full bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                    >
                </div>

                <!-- Filters Group -->
                <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
                    <!-- Status Filter -->
                    <div class="relative min-w-[140px]">
                        <select 
                            v-model="statusFilter" 
                            class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 pr-8 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm cursor-pointer"
                        >
                            <option value="all">Todos los Estados</option>
                            <option v-for="status in statuses" :key="status" :value="status" class="capitalize">{{ status }}</option>
                        </select>
                        <Filter :size="14" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    </div>

                    <!-- Reset -->
                     <button 
                        v-if="searchTerm || statusFilter !== 'all'"
                        @click="clearFilters"
                        class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
                        title="Limpiar Filtros"
                    >
                        <X :size="18" />
                    </button>
                </div>
            </div>

            <Card class="overflow-hidden p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left">Asociado</th>
                                <th class="px-6 py-4 text-left">Referencia / Oferta</th>
                                <th class="px-6 py-4 text-left">Tipo</th>
                                <th class="px-6 py-4 text-center">Estatus</th>
                                <th class="px-6 py-4 text-left">Fecha Creación</th>
                                <th class="px-6 py-4 text-right">Monto</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="commission in commissionsResource.data" :key="commission.id" class="hover:bg-slate-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                            <User :size="16" />
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ commission.associate?.user?.name }}</div>
                                            <div class="text-xs text-slate-500">{{ commission.associate?.user?.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                     <div v-if="commission.referral" class="flex flex-col">
                                        <span class="font-medium text-slate-700">{{ commission.referral.client_name }}</span>
                                        <span class="text-xs text-slate-500">{{ commission.referral.offering?.name || 'Oferta Desconocida' }}</span>
                                     </div>
                                     <span v-else class="text-slate-400 italic">Sin Referencia</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200 capitalize">
                                        {{ commission.commission_type || 'Estándar' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <Badge :status="commission.status" class="shadow-sm" />
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ formatShortDate(commission.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-sm">
                                    <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-1 rounded">
                                        {{ formatCurrency(commission.amount) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link 
                                        v-if="$page.props.auth.user.role !== 'psadmin'"
                                        :href="route('admin.commissions.edit', commission.id)"
                                        class="text-indigo-600 hover:text-indigo-900 font-medium text-sm"
                                    >
                                        Editar
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="commissionsResource.data.length === 0" class="p-8 text-center text-slate-400">
                        No se encontraron comisiones.
                    </div>
                </div>
                 <!-- Pagination -->
                <div v-if="commissionsResource.links.length > 3" class="flex justify-center p-4 border-t border-slate-100">
                     <div class="flex gap-1">
                         <Link
                            v-for="(link, i) in commissionsResource.links"
                            :key="i"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1 rounded border text-xs font-medium transition"
                            :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                         />
                    </div>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>
