<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { Search, Plus, Filter, X, Trash, Edit } from 'lucide-vue-next';
import { Switch } from '@headlessui/vue';
import { ref, watch } from 'vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue';

const props = defineProps({
    offerings: Object,
    auth: Object,
    filters: Object,
    categories: Array
});

const searchTerm = ref(props.filters?.search || '');
const categoryFilter = ref(props.filters?.category || '');
const statusFilter = ref(props.filters?.status || 'all');

const confirmDeleteModal = ref(false);
const confirmToggleModal = ref(false);
const itemToDelete = ref(null);
const itemToToggle = ref(null);

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const applyFilters = debounce(() => {
    router.get(
        route('admin.offerings.index'),
        { 
            search: searchTerm.value,
            category: categoryFilter.value,
            status: statusFilter.value
        },
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true 
        }
    );
}, 300);

watch([searchTerm, categoryFilter, statusFilter], () => {
    applyFilters();
});

const clearFilters = () => {
    searchTerm.value = '';
    categoryFilter.value = '';
    statusFilter.value = 'all';
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

const promptDelete = (item) => {
    itemToDelete.value = item;
    confirmDeleteModal.value = true;
};

const executeDelete = () => {
    if (!itemToDelete.value) return;

    router.delete(route('admin.offerings.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            confirmDeleteModal.value = false;
            itemToDelete.value = null;
        }
    });
};

const promptToggle = (item) => {
    itemToToggle.value = item;
    confirmToggleModal.value = true;
};

const executeToggle = () => {
    if (!itemToToggle.value) return;
    
    // Toggle to OPPOSITE state
    const newState = !itemToToggle.value.is_active;

    router.post(route('admin.offerings.toggle-status', itemToToggle.value.id), { is_active: newState }, {
        preserveScroll: true,
        onSuccess: () => {
            confirmToggleModal.value = false;
            itemToToggle.value = null;
        }
    });
};
</script>

<template>
    <Head title="Catálogo de Ofertas" />

    <AppLayout>
        <div class="space-y-6">
            <!-- Header & Actions -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Catálogo de Ofertas</h1>
                    <p class="text-slate-500">Explora productos y servicios para referir</p>
                </div>
                <Link v-if="['psadmin', 'admin'].includes($page.props.auth.user.role)" :href="route('admin.offerings.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2 shadow-sm transition">
                    <Plus :size="20" /> Nuevo Producto
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
                        placeholder="Buscar oferta..." 
                        class="pl-10 pr-4 py-2.5 w-full bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                    >
                </div>

                <!-- Filters Group -->
                <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
                    <!-- Category Filter -->
                    <div class="relative min-w-[140px]">
                        <select 
                            v-model="categoryFilter" 
                            class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 pr-8 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm cursor-pointer"
                        >
                            <option value="">Todas las Categorías</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                         <Filter :size="14" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    </div>

                    <!-- Status Filter -->
                    <div class="relative min-w-[140px]">
                        <select 
                            v-model="statusFilter" 
                            class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 pr-8 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm cursor-pointer"
                        >
                            <option value="all">Todos los Estados</option>
                            <option value="true">Activos</option>
                            <option value="false">Inactivos</option>
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                            <span v-if="statusFilter === 'true'" class="w-2 h-2 rounded-full bg-green-500 block"></span>
                            <span v-else-if="statusFilter === 'false'" class="w-2 h-2 rounded-full bg-slate-300 block"></span>
                            <Filter v-else :size="14" class="text-slate-400" />
                        </div>
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="offering in offerings.data" :key="offering.id" class="flex flex-col h-full hover:shadow-md transition">
                   <div class="flex-1">
                       <div class="flex justify-between items-start mb-2">
                           <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold uppercase">{{ offering.category || 'General' }}</span>
                           
                           <!-- Admin Status Toggle -->
                           <div v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)">
                               <Switch
                                    :model-value="offering.is_active"
                                    @click="promptToggle(offering)"
                                    :class="offering.is_active ? 'bg-green-500' : 'bg-slate-200'"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer"
                                >
                                    <span class="sr-only">Cambiar estado</span>
                                    <span
                                        :class="offering.is_active ? 'translate-x-5' : 'translate-x-1'"
                                        class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"
                                    />
                                </Switch>
                           </div>
                           <span v-else-if="offering.is_active" class="w-2 h-2 bg-green-500 rounded-full"></span>
                       </div>
                       <h3 class="font-bold text-xl text-slate-800 mb-2">{{ offering.name }}</h3>
                       <p class="text-slate-600 text-sm mb-4 line-clamp-3">{{ offering.description }}</p>

                       <div class="space-y-2 mb-4">
                           <div class="flex justify-between text-sm">
                               <span class="text-slate-500">Comisión Base:</span>
                               <span class="font-semibold text-green-600">
                                   {{ offering.commission_rate ? `${offering.commission_rate}%` : formatCurrency(offering.base_commission) }}
                               </span>
                           </div>
                            <div v-if="offering.base_price !== null && offering.base_price !== undefined" class="flex justify-between text-sm">
                               <span class="text-slate-500">Precio Base:</span>
                               <span class="font-medium text-slate-800">{{ formatCurrency(offering.base_price) }}</span>
                           </div>
                       </div>
                   </div>

                   <div class="mt-4 pt-4 border-t border-slate-100 flex gap-2">
                       <template v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)">
                           <Link :href="route('admin.offerings.edit', offering.id)" class="flex-1 text-center bg-white border border-slate-300 text-slate-700 py-2 rounded-lg hover:bg-slate-50 transition font-medium">
                               Editar
                           </Link>
                            <button v-if="$page.props.auth.user.role === 'admin'" @click="promptDelete(offering)" class="flex-1 text-center bg-red-50 text-red-600 py-2 rounded-lg hover:bg-red-100 transition font-medium">
                               Eliminar
                           </button>
                       </template>
                       <Link v-else :href="route('admin.referrals.create', { offering_id: offering.id })" class="block w-full text-center bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                           Referir Ahora
                       </Link>
                   </div>
                </Card>
            </div>

            <!-- Pagination -->
            <div v-if="offerings.links.length > 3" class="flex justify-center mt-6">
                <div class="flex gap-1">
                     <Link
                        v-for="(link, i) in offerings.links"
                        :key="i"
                        :href="link.url"
                        v-html="link.label"
                        class="px-3 py-1 rounded border"
                        :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                     />
                </div>
            </div>
        </div>

        <!-- Modals -->
        <ConfirmModal 
            :show="confirmDeleteModal"
            title="¿Eliminar Oferta?"
            :message="`Estás a punto de eliminar '${itemToDelete?.name}'. Esta acción no se puede deshacer.`"
            confirmText="Sí, Eliminar"
            type="danger"
            @close="confirmDeleteModal = false"
            @confirm="executeDelete"
        />

        <ConfirmModal 
            :show="confirmToggleModal"
            :title="itemToToggle?.is_active ? '¿Desactivar Oferta?' : '¿Activar Oferta?'"
            :message="itemToToggle?.is_active 
                ? `Estás a punto de ocultar '${itemToToggle?.name}' del catálogo. Los asociados ya no podrán verlo.` 
                : `Estás a punto de reactivar '${itemToToggle?.name}'.`"
            :confirmText="itemToToggle?.is_active ? 'Sí, Desactivar' : 'Sí, Activar'"
            type="warning"
            @close="confirmToggleModal = false"
            @confirm="executeToggle"
        />
    </AppLayout>
</template>