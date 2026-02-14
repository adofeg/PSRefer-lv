<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'; // Added router
import { computed, ref, watch } from 'vue'; // Added watch, ref
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue'; // Added ConfirmModal
import { Search, Filter, Plus, Eye, Trash2, X } from 'lucide-vue-next'; // Additional icons
import { useFormatters } from '@/Composables/useFormatters';
import { normalizePaginated } from '@/Utils/inertia';

const props = defineProps({
    referrals: Object,
    filters: Object,
    statuses: Array // Added statuses prop
});

const referralsResource = computed(() => normalizePaginated(props.referrals));

const { formatCurrency, formatShortDate } = useFormatters();

// Filter State
const searchTerm = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');

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

const applyFilters = debounce(() => {
    router.get(
        route('admin.referrals.index'),
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
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search -->
                <div class="relative w-full md:w-96">
                    <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input 
                        v-model="searchTerm"
                        type="text" 
                        placeholder="Buscar por cliente o contacto..." 
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
                            <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
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
                                <th class="px-6 py-4 text-left">Cliente</th>
                                <th v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)" class="px-6 py-4 text-left">Referido Por</th>
                                <th class="px-6 py-4 text-left">Servicio / Categoría</th>
                                <th class="px-6 py-4 text-left">Fecha</th>
                                <th class="px-6 py-4 text-center">Estatus</th>
                                <th class="px-6 py-4 text-right">Comisión Est.</th>
                                <th class="px-6 py-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="referral in referralsResource.data" :key="referral.id" class="hover:bg-slate-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                                            {{ referral.client_name.substring(0,2).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ referral.client_name }}</div>
                                            <div class="text-xs text-slate-500">{{ referral.client_contact }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)" class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div v-if="referral.associate?.user?.logo_url" class="w-6 h-6 rounded-full bg-slate-200 overflow-hidden">
                                            <img :src="referral.associate.user.logo_url" :alt="referral.associate.user.name" class="w-full h-full object-cover">
                                        </div>
                                        <div class="text-sm font-medium text-slate-700">
                                            {{ referral.associate?.user?.name || 'Sistema' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-700">{{ referral.offering?.name || '---' }}</div>
                                    <div class="inline-flex mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500 uppercase tracking-wide">
                                        {{ referral.offering?.category || 'General' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ formatShortDate(referral.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <Badge :status="referral.status" class="shadow-sm" />
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-sm">
                                    <span v-if="referral.estimated_commission && referral.estimated_commission !== '-'" class="text-emerald-600 font-bold bg-emerald-50 px-2 py-1 rounded">
                                        {{ referral.estimated_commission }}
                                    </span>
                                    <span v-else-if="referral.offering?.base_commission" class="text-emerald-600 font-bold bg-emerald-50 px-2 py-1 rounded">
                                        {{ formatCurrency(referral.offering.base_commission) }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-60 group-hover:opacity-100 transition">
                                        <Link 
                                            :href="route('admin.referrals.show', referral.id)" 
                                            class="p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition"
                                            title="Ver Detalles Completos"
                                        >
                                            <Eye :size="18" />
                                        </Link>
                                        <button 
                                            v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)"
                                            @click="promptDelete(referral)"
                                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition"
                                            title="Eliminar"
                                        >
                                            <Trash2 :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="referralsResource.data.length === 0" class="p-8 text-center text-slate-400">
                        {{ ['admin', 'psadmin'].includes($page.props.auth.user.role) ? 'No hay referidos en el sistema.' : 'No has registrado referidos aún.' }}
                    </div>
                </div>
                 <!-- Pagination -->
                <div v-if="referralsResource.links.length > 3" class="flex justify-center p-4 border-t border-slate-100">
                     <div class="flex gap-1">
                         <Link
                            v-for="(link, i) in referralsResource.links"
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
