<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Plus, X, Eye, Mail, Phone, Calendar } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { normalizePaginated } from '@/Utils/inertia';

const props = defineProps({
    referrals: Object,
    auth: Object,
    filters: Object,
});

const referralsResource = computed(() => normalizePaginated(props.referrals));

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
        route('associate.referrals.index'),
        { search: searchTerm.value },
        { preserveState: true, preserveScroll: true, replace: true }
    );
}, 300);

watch(searchTerm, () => applyFilters());

const clearFilters = () => {
    searchTerm.value = '';
};

// Helper for status classes
const getStatusClasses = (statusName) => {
    switch (statusName) {
        case 'Prospecto': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'Contactado': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'Negociación': return 'bg-purple-100 text-purple-800 border-purple-200';
        case 'Cerrado': return 'bg-green-100 text-green-800 border-green-200';
        case 'Perdido': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-slate-100 text-slate-800 border-slate-200';
    }
};
</script>

<template>
    <Head title="Mis Referidos" />

    <AppLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Mis Referidos</h1>
                    <p class="text-slate-500">Gestiona y da seguimiento a tus referidos</p>
                </div>
                
                <Link :href="route('associate.referrals.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2 shadow-sm transition">
                    <Plus :size="20" /> Nuevo Referido
                </Link>
            </div>

            <!-- Search Bar -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="relative w-full md:w-96">
                    <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input 
                        v-model="searchTerm"
                        type="text" 
                        placeholder="Buscar por nombre o email..." 
                        class="pl-10 pr-4 py-2.5 w-full bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                    >
                </div>
                
                 <button 
                    v-if="searchTerm"
                    @click="clearFilters"
                    class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
                    title="Limpiar Filtros"
                >
                    <X :size="18" />
                </button>
            </div>

            <!-- Referrals List -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4">Servicio de Interés</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="referral in referralsResource.data" :key="referral.id" class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ referral.client_name }}</div>
                                    <div class="text-xs text-slate-500 flex flex-col gap-1 mt-1">
                                        <div class="flex items-center gap-1"><Mail :size="12" /> {{ referral.client_email }}</div>
                                        <div class="flex items-center gap-1"><Phone :size="12" /> {{ referral.client_phone }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800">{{ referral.offering?.name }}</div>
                                    <div class="text-xs text-slate-500">{{ referral.offering?.category?.name || 'General' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                        :class="getStatusClasses(referral.status)"
                                    >
                                        {{ referral.status || 'Desconocido' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1 text-slate-500">
                                        <Calendar :size="14" />
                                        {{ new Date(referral.created_at).toLocaleDateString() }}
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="referralsResource.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    No tienes referidos registrados aún.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="referralsResource.links.length > 3" class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-center">
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
            </div>
        </div>
    </AppLayout>
</template>
