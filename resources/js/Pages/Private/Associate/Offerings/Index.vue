<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { Search, Filter, ImageIcon, ArrowRight } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import OfferingDetailsModal from './OfferingDetailsModal.vue'; // Import Modal
import { normalizePaginated } from '@/Utils/inertia';

const props = defineProps({
    offerings: Object,
    filters: Object,
    auth: Object,
    categories: Array,
    sectors: Array
});

const offeringsResource = computed(() => normalizePaginated(props.offerings));

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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="offering in offeringsResource.data" 
                    :key="offering.id"
                    class="group relative"
                    @click="openModal(offering)"
                >
                    <!-- Card with Glassmorphism -->
                    <div class="h-full bg-white/70 backdrop-blur-md border border-slate-200/60 rounded-3xl p-8 cursor-pointer transition-all duration-500 hover:shadow-2xl hover:shadow-indigo-100 hover:-translate-y-2 flex flex-col relative overflow-hidden group-hover:border-indigo-200/50">
                        <!-- Decorative background element -->
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-50/50 rounded-full blur-3xl group-hover:bg-indigo-100/50 transition-colors duration-500"></div>

                        <div class="flex-1 relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="p-4 bg-indigo-50/80 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500 transform group-hover:rotate-6">
                                    <ImageIcon v-if="offering.category === 'Marketing'" :size="28" />
                                    <Search v-else-if="offering.category === 'Investigación'" :size="28" />
                                    <Filter v-else :size="28" />
                                </div>
                                <span class="bg-slate-100/80 backdrop-blur-sm text-slate-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-200/50">
                                    {{ offering.category || 'General' }}
                                </span>
                            </div>

                            <h3 class="font-black text-xl text-slate-800 mb-3 group-hover:text-indigo-600 transition-colors tracking-tight leading-tight">
                                {{ offering.name }}
                            </h3>
                            <p class="text-slate-500 text-sm mb-8 line-clamp-3 leading-relaxed font-medium">
                                {{ offering.description }}
                            </p>

                            <!-- Commission Glass Detail -->
                            <div class="flex items-center gap-3 bg-indigo-50/30 backdrop-blur-sm p-4 rounded-2xl border border-indigo-100/30 group-hover:border-indigo-200/50 transition-all">
                                <div class="flex-1">
                                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Comisión Estimada</p>
                                    <p class="text-lg font-black text-slate-700">
                                        {{ offering.commission_type === 'percentage' ? `${offering.base_commission}%` : formatCurrency(offering.base_commission) }}
                                    </p>
                                </div>
                                <div class="bg-indigo-600 text-white p-2 rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-500 translate-x-4 group-hover:translate-x-0">
                                    <ArrowRight :size="16" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal -->
            <OfferingDetailsModal 
                :show="isModalOpen" 
                :offering="selectedOffering" 
                :sectors="sectors"
                @close="closeModal" 
            />

            <!-- Pagination -->
             <!-- Simplified for brevity, assume similar to original or standard Layout -->
        </div>
    </AppLayout>
</template>
