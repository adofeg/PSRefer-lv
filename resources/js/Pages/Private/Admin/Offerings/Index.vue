<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { Search, Plus, Filter, X, Trash, Edit } from 'lucide-vue-next';
import { Switch } from '@headlessui/vue';
import { computed, ref, watch } from 'vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue';
import MultiSelectCombobox from '@/Components/UI/MultiSelectCombobox.vue';
import { normalizePaginated } from '@/Utils/inertia';

const props = defineProps({
    offerings: Object,
    auth: Object,
    filters: Object,
    categories: Array
});

const offeringsResource = computed(() => normalizePaginated(props.offerings));

const searchTerm = ref(props.filters?.search || '');
const categoryFilter = ref(props.filters?.category || null);
const statusFilter = ref(props.filters?.status || null);

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
    categoryFilter.value = null;
    statusFilter.value = null;
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
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <div class="flex flex-row flex-wrap gap-3 items-center">
                        <!-- Category Filter -->
                        <div class="relative w-full sm:w-auto sm:min-w-[150px] sm:max-w-[220px]">
                             <MultiSelectCombobox
                                  v-model="categoryFilter"
                                  :options="categories"
                                  placeholder="Categorías"
                                  wrapperClass="bg-slate-50 border-slate-200 rounded-lg shadow-none hover:border-indigo-300 transition-all cursor-pointer"
                                  inputClass="py-2.5 px-4 text-sm text-slate-700 bg-transparent placeholder-slate-400 font-medium"
                             />
                        </div>

                        <!-- Status Filter -->
                        <div class="relative w-full sm:w-auto sm:min-w-[130px] sm:max-w-[180px]">
                             <MultiSelectCombobox
                                  v-model="statusFilter"
                                  :options="[
                                      { id: 'true', name: 'Activos' },
                                      { id: 'false', name: 'Inactivos' }
                                  ]"
                                  placeholder="Estados"
                                  align="right"
                                  wrapperClass="bg-slate-50 border-slate-200 rounded-lg shadow-none hover:border-indigo-300 transition-all cursor-pointer"
                                  inputClass="py-2.5 px-4 text-sm text-slate-700 bg-transparent placeholder-slate-400 font-medium"
                             />
                        </div>

                        <!-- Reset -->
                         <button 
                            v-if="searchTerm || statusFilter !== null || categoryFilter !== null"
                            @click="clearFilters"
                            class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
                            title="Limpiar Filtros"
                        >
                            <X :size="18" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="offering in offeringsResource.data" :key="offering.id" class="flex flex-col h-full hover:shadow-md transition">
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
                               <span class="text-slate-500">Comisión Base (Informativo):</span>
                               <span class="font-semibold" :class="{
                                   'text-green-600': ['fixed', 'percentage'].includes(offering.commission_type),
                                   'text-indigo-600': offering.commission_type === 'variable'
                               }">
                                    <template v-if="offering.commission_type === 'variable'">Variable (Manual)</template>
                                    <template v-else>
                                        {{ offering.commission_type === 'percentage' ? `${offering.base_commission}%` : formatCurrency(offering.base_commission) }}
                                    </template>
                               </span>
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
            <div v-if="offeringsResource.links.length > 3" class="flex justify-center mt-6">
                <div class="flex gap-1">
                     <Link
                        v-for="(link, i) in offeringsResource.links"
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
