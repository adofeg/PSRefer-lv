<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { Search, Filter, X, ImageIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    offerings: Object,
    filters: Object,
    auth: Object,
    categories: Array
});

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
                <Link 
                    v-for="offering in offerings.data" 
                    :key="offering.id"
                    :href="route('associate.offerings.show', offering.id)"
                    class="block h-full"
                >
                    <Card class="flex flex-col h-full hover:shadow-md transition group">
                       <div class="flex-1">
                           <div class="flex justify-between items-start mb-2">
                               <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold uppercase">{{ offering.category || 'General' }}</span>
                           </div>
                           
                           <!-- Image Placeholder if needed -->
                           <div v-if="offering.image_url" class="mb-4 rounded-lg overflow-hidden h-40 w-full">
                               <img :src="offering.image_url" class="object-cover w-full h-full" alt="Offering Image">
                           </div>
                           <div v-else class="mb-4 rounded-lg overflow-hidden h-40 w-full bg-gray-100 flex items-center justify-center text-gray-400">
                               <ImageIcon :size="48" />
                           </div>

                           <h3 class="font-bold text-xl text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors">{{ offering.name }}</h3>
                           <p class="text-slate-600 text-sm mb-4 line-clamp-3">{{ offering.description }}</p>

                           <div class="space-y-2 mb-4">
                               <div class="flex justify-between text-sm">
                                   <span class="text-slate-500">Comisión Base:</span>
                                   <span class="font-semibold text-green-600">
                                       {{ offering.commission_rate ? `${offering.commission_rate}%` : formatCurrency(offering.base_commission) }}
                                   </span>
                               </div>
                                <div v-if="offering.base_price !== null" class="flex justify-between text-sm">
                                   <span class="text-slate-500">Precio Base:</span>
                                   <span class="font-medium text-slate-800">{{ formatCurrency(offering.base_price) }}</span>
                               </div>
                           </div>
                       </div>
                       
                       <div class="mt-4 pt-4 border-t border-slate-100">
                            <span class="block w-full text-center bg-indigo-50 text-indigo-600 py-2 rounded-lg group-hover:bg-indigo-100 transition font-medium">
                                Ver Detalles
                            </span>
                       </div>
                    </Card>
                </Link>
            </div>
            
            <!-- Pagination -->
             <!-- Simplified for brevity, assume similar to original or standard Layout -->
        </div>
    </AppLayout>
</template>