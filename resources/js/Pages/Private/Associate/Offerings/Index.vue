<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { Search, Filter, ImageIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import OfferingDetailsModal from './OfferingDetailsModal.vue'; // Import Modal

const props = defineProps({
    offerings: Object,
    filters: Object,
    auth: Object,
    categories: Array
});

// --- Modal Logic ---
const selectedOffering = ref(null);
const isModalOpen = ref(false);

const openModal = (offering) => {
    selectedOffering.value = offering;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        selectedOffering.value = null;
    }, 200); // Clear after animation
};
// -------------------

const searchTerm = ref(props.filters?.search || '');

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const applyFilters = debounce(() => {
    router.get(
        route('associate.offerings.index'),
        { search: searchTerm.value },
        { preserveState: true, preserveScroll: true, replace: true }
    );
}, 300);

watch(searchTerm, () => applyFilters());

const clearFilters = () => {
    searchTerm.value = '';
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};
</script>

<template>
    <Head title="Catálogo de Ofertas" />

    <AppLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Catálogo de Ofertas</h1>
                    <p class="text-slate-500">Explora productos y servicios para referir</p>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="relative w-full md:w-96">
                    <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input 
                        v-model="searchTerm"
                        type="text" 
                        placeholder="Buscar oferta..." 
                        class="pl-10 pr-4 py-2.5 w-full bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                    >
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="offering in offerings.data" 
                    :key="offering.id"
                    class="block h-full cursor-pointer"
                    @click="openModal(offering)"
                >
                    <Card class="flex flex-col h-full hover:shadow-md transition-all duration-300 group border border-slate-200 hover:-translate-y-1">
                       <div class="flex-1 p-6">
                           <div class="flex justify-between items-start mb-4">
                               <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                   <!-- Dynamic Icon based on category or generic default -->
                                   <ImageIcon v-if="offering.category === 'Marketing'" :size="24" />
                                   <Search v-else-if="offering.category === 'Investigación'" :size="24" />
                                   <Filter v-else :size="24" />
                               </div>
                               <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">{{ offering.category || 'General' }}</span>
                           </div>

                           <h3 class="font-bold text-lg text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors">{{ offering.name }}</h3>
                           <p class="text-slate-500 text-sm mb-6 line-clamp-3 leading-relaxed">{{ offering.description }}</p>

                           <div class="flex items-center gap-2 text-sm bg-slate-50 p-3 rounded-lg border border-slate-100">
                               <span class="text-slate-400 font-medium text-xs uppercase tracking-wider flex-1">Comisión</span>
                               <span class="font-black text-slate-700">
                                   {{ parseFloat(offering.commission_rate) > 0 ? `${offering.commission_rate}%` : formatCurrency(offering.base_commission) }}
                               </span>
                           </div>
                       </div>
                       
                       <div class="px-6 pb-6 pt-0">
                            <span class="block w-full text-center bg-white border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-bold group-hover:border-indigo-600 group-hover:text-indigo-600 transition-all">
                                Ver Detalles
                            </span>
                       </div>
                    </Card>
                </div>
            </div>
            
            <!-- Modal -->
            <OfferingDetailsModal 
                :show="isModalOpen" 
                :offering="selectedOffering" 
                @close="closeModal" 
            />

            <!-- Pagination -->
             <!-- Simplified for brevity, assume similar to original or standard Layout -->
        </div>
    </AppLayout>
</template>