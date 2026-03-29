<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'; // Added router
import { computed, ref, watch } from 'vue'; // Added watch, ref
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue'; // Added ConfirmModal
import { Search, Filter, Plus, Eye, Trash2, X, Check } from 'lucide-vue-next'; // Additional icons including Check
import { useFormatters } from '@/Composables/useFormatters';
import { normalizePaginated } from '@/Utils/inertia';
import MultiSelectCombobox from '@/Components/UI/MultiSelectCombobox.vue';

const props = defineProps({
    referrals: Object,
    filters: Object,
    statuses: Array,
    offerings: Array,
    associates: Array,
    sectors: Array
});

const referralsResource = computed(() => normalizePaginated(props.referrals));

const { formatCurrency, formatShortDate } = useFormatters();

// Filter State
const searchTerm = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status && props.filters.status !== 'all' ? props.filters.status : null);
const offeringFilter = ref(props.filters?.offering_id && props.filters.offering_id !== 'all' ? props.filters.offering_id : null);
const associateFilter = ref(props.filters?.associate_id || null);
const sectorFilter = ref(props.filters?.sector_id && props.filters.sector_id !== 'all' ? props.filters.sector_id : null);

// Confirm Modal State
const confirmDeleteModal = ref(false);
const itemToDelete = ref(null);

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const isProcessing = ref(false);

const applyFilters = debounce(() => {
    router.get(
        route('admin.referrals.index'),
        { 
            search: searchTerm.value,
            status: statusFilter.value,
            offering_id: offeringFilter.value,
            associate_id: associateFilter.value,
            sector_id: sectorFilter.value
        },
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onBefore: () => isProcessing.value = true,
            onFinish: () => isProcessing.value = false,
        }
    );
}, 300);

const clearFilters = () => {
    searchTerm.value = '';
    statusFilter.value = null;
    offeringFilter.value = null;
    associateFilter.value = null;
    sectorFilter.value = null;
};

watch([searchTerm, statusFilter, offeringFilter, associateFilter, sectorFilter], () => {
    applyFilters();
});

const promptDelete = (referral) => {
    itemToDelete.value = referral;
    confirmDeleteModal.value = true;
};

const executeDelete = () => {
    if (!itemToDelete.value) return;

    router.delete(route('admin.referrals.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            confirmDeleteModal.value = false;
            itemToDelete.value = null;
        }
    });
};

const hasPaidCommission = (referral) => {
    return referral.commissions && referral.commissions.some(c => c.status === 'paid');
};
</script>

<template>
    <Head title="Mis Referidos" />

    <AppLayout>
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                  <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        {{ ['admin', 'psadmin'].includes($page.props.auth.user.role) ? 'Gestión de Referidos' : 'Mis Referidos' }}
                    </h1>
                    <p class="text-slate-500">
                        {{ ['admin', 'psadmin'].includes($page.props.auth.user.role) 
                            ? 'Administra y monitorea todos los referidos del sistema' 
                            : 'Gestiona y rastrea el estado de tus referidos' }}
                    </p>
                </div>
                  <Link
                      v-if="$page.props.auth.user.role !== 'psadmin'"
                      :href="route('admin.referrals.create')"
                      class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2 shadow-sm transition"
                  >
                    <Plus :size="20" /> Nuevo Referido
                 </Link>
            </div>

            <!-- Professional Filter Bar -->
            <div class="bg-slate-50/50 p-3 rounded-2xl border border-slate-200/60 backdrop-blur-sm flex flex-col md:flex-row gap-4 items-center justify-between relative z-30">
                <!-- Search -->
                <div class="relative w-full md:w-96 group">
                    <Search :size="16" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-400 transition-colors" />
                    <input 
                        v-model="searchTerm"
                        type="text" 
                        placeholder="Buscar por cliente o contacto..." 
                        class="pl-11 pr-4 py-2 w-full bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-200 transition-all text-xs"
                    >
                </div>

                <!-- Filters Group -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="flex flex-row flex-wrap gap-2 items-center">
                        <!-- Ofrecimiento Filter -->
                        <div class="relative w-full sm:w-auto sm:min-w-[150px] sm:max-w-[220px]">
                            <MultiSelectCombobox 
                                v-model="offeringFilter"
                                :options="offerings"
                                placeholder="Ofertas"
                                :multiple="false"
                            />
                        </div>

                        <!-- Asociado Filter (Searchable) -->
                        <div class="relative w-full sm:w-auto sm:min-w-[170px] sm:max-w-[240px]">
                            <MultiSelectCombobox 
                                v-model="associateFilter"
                                :options="associates"
                                placeholder="Asociados"
                                :multiple="false"
                            />
                        </div>

                        <!-- Sector Filter -->
                        <div class="relative w-full sm:w-auto sm:min-w-[130px] sm:max-w-[180px]">
                            <MultiSelectCombobox 
                                v-model="sectorFilter"
                                :options="sectors"
                                placeholder="Sectores"
                                :multiple="false"
                            />
                        </div>

                        <!-- Status Filter -->
                        <div class="relative w-full sm:w-auto sm:min-w-[130px] sm:max-w-[180px]">
                            <MultiSelectCombobox 
                                v-model="statusFilter"
                                :options="statuses"
                                placeholder="Estados"
                                :multiple="false"
                            />
                        </div>

                        <!-- Clear Filters Button -->
                        <button 
                            v-if="searchTerm || statusFilter || offeringFilter || associateFilter || sectorFilter"
                            @click="clearFilters"
                            class="p-2 text-slate-300 hover:text-red-400 transition-all hover:scale-110 active:scale-95"
                            title="Limpiar filtros"
                        >
                            <X :size="18" />
                        </button>
                    </div>
                </div>
            </div>

            <Card class="overflow-hidden p-0 relative border-slate-200/60 shadow-sm rounded-2xl z-0">
                <!-- Loading Overlay -->
                <div 
                    v-if="isProcessing" 
                    class="absolute inset-0 z-10 bg-white/60 backdrop-blur-[2px] flex items-center justify-center transition-all duration-300"
                >
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 h-10 border-[3px] border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em]">Actualizando</span>
                    </div>
                </div>

                <div class="overflow-x-auto" :class="{ 'opacity-40 grayscale-[0.5]': isProcessing }">
                    <table class="w-full text-[13px] text-left">
                        <thead class="bg-slate-50/80 text-slate-400 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Cliente</th>
                                <th v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)" class="px-6 py-4 text-left">Referido Por</th>
                                <th class="px-6 py-4 text-left">Servicio</th>
                                <th class="px-6 py-4 text-left">Fecha</th>
                                <th class="px-6 py-4 text-center">Estatus</th>
                                <th class="px-6 py-4 text-right">Comisión</th>
                                <th class="px-6 py-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="referral in referralsResource.data" :key="referral.id" class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-50/50 flex items-center justify-center text-indigo-600 font-bold text-xs border border-indigo-100/50 shadow-inner">
                                            {{ (referral.client_name || '??').substring(0,2).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-700 leading-tight">{{ referral.client_name || 'Sin Nombre' }}</div>
                                            <div class="text-[11px] text-slate-400 mt-0.5">{{ referral.client_contact || 'Sin Contacto' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)" class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div v-if="referral.associate?.user?.logo_url" class="w-6 h-6 rounded-lg bg-slate-100 overflow-hidden ring-1 ring-slate-200/50">
                                            <img :src="referral.associate.user.logo_url" :alt="referral.associate.user.name" class="w-full h-full object-cover">
                                        </div>
                                        <div class="text-[12px] font-medium text-slate-600">
                                            {{ referral.associate?.user?.name || 'Sistema' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-600">{{ referral.offering?.name || '---' }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium uppercase tracking-wider mt-0.5">
                                        {{ referral.offering?.category || 'General' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[12px] text-slate-400">
                                    {{ formatShortDate(referral.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <Badge :status="referral.status" class="shadow-none border border-slate-100" />
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-[12px]">
                                    <span v-if="referral.estimated_commission && referral.estimated_commission !== '-'" class="text-emerald-600 font-black">
                                        {{ referral.estimated_commission }}
                                    </span>
                                    <span v-else-if="referral.offering?.base_commission" class="text-emerald-600 font-black">
                                        {{ formatCurrency(referral.offering.base_commission) }}
                                    </span>
                                    <span v-else class="text-slate-300">-</span>
                                    
                                    <div v-if="hasPaidCommission(referral)" class="mt-1 flex justify-end">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-50 text-emerald-600 uppercase tracking-widest border border-emerald-100">
                                            <Check :size="9" /> PAGADO
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                        <Link 
                                            :href="route('admin.referrals.show', referral.id)" 
                                            class="p-2 text-indigo-400 hover:text-indigo-600 bg-white hover:bg-indigo-50 rounded-xl border border-slate-100 transition-all shadow-sm"
                                            title="Ver Detalles"
                                        >
                                            <Eye :size="16" />
                                        </Link>
                                        <button 
                                            v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)"
                                            @click="promptDelete(referral)"
                                            class="p-2 text-slate-300 hover:text-red-500 bg-white hover:bg-red-50 rounded-xl border border-slate-100 transition-all shadow-sm"
                                            title="Eliminar"
                                        >
                                            <Trash2 :size="16" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="referralsResource.data.length === 0" class="flex flex-col items-center justify-center p-24 text-center animate-fade-in">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-5 border border-slate-100 shadow-inner">
                            <Search :size="32" class="text-slate-200" />
                        </div>
                        <h3 class="text-base font-black text-slate-800 mb-1">Sin resultados</h3>
                        <p class="text-xs text-slate-400 max-w-[200px] mb-8 leading-relaxed">
                            Ajusta los filtros para refinar tu búsqueda.
                        </p>
                        <button 
                            @click="clearFilters"
                            class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-500 hover:text-indigo-700 bg-indigo-50/50 px-6 py-3 rounded-xl border border-indigo-100/50 transition-all active:scale-95 shadow-sm"
                        >
                            Limpiar Filtros
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="referralsResource.links.length > 3" class="flex justify-center p-6 border-t border-slate-50 bg-slate-50/30">
                     <div class="flex gap-1.5">
                         <Link
                            v-for="(link, i) in referralsResource.links"
                            :key="i"
                            :href="link.url"
                            v-html="link.label"
                            class="min-w-[32px] h-8 flex items-center justify-center rounded-xl border text-[11px] font-black transition-all"
                            :class="link.active 
                                ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-200' 
                                : 'bg-white text-slate-400 border-slate-100 hover:bg-slate-50 hover:text-slate-600 shadow-sm'"
                         />
                    </div>
                </div>
            </Card>
        </div>

        <ConfirmModal 
            :show="confirmDeleteModal"
            title="¿Eliminar Referido?"
            :message="`Estás a punto de eliminar el referido de '${itemToDelete?.client_name}'. Esta acción no se puede deshacer.`"
            confirmText="Sí, Eliminar"
            type="danger"
            @close="confirmDeleteModal = false"
            @confirm="executeDelete"
        />
    </AppLayout>
</template>
