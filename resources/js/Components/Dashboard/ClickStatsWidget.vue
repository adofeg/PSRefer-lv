<script setup>
import { ref, onMounted } from 'vue';
import { MousePointer2, TrendingUp, BarChart3, ExternalLink } from 'lucide-vue-next';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const stats = ref({
    total_clicks: 0,
    conversions: 0,
    conversion_rate: 0,
    by_offering: [],
    last_7_days: []
});

const loading = ref(true);

const fetchStats = async () => {
    try {
        const response = await axios.get(route('api.analytics.clicks'));
        stats.value = response.data;
    } catch (error) {
        console.error('Failed to fetch click stats:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchStats();
});
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <MousePointer2 :size="18" class="text-indigo-600" />
                Rendimiento de Clicks
            </h3>
            <button @click="fetchStats" class="text-slate-400 hover:text-indigo-600 transition">
                <svg :class="{ 'animate-spin': loading }" class="w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>

        <div v-if="loading" class="p-8 flex justify-center">
            <div class="h-8 w-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else class="p-6">
            <!-- Summary Stats -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                    <p class="text-xs text-indigo-600 font-medium uppercase tracking-wider mb-1">Clicks Totales</p>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold text-indigo-900">{{ stats.total_clicks }}</span>
                        <TrendingUp :size="20" class="text-indigo-400" />
                    </div>
                </div>
                <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                    <p class="text-xs text-green-600 font-medium uppercase tracking-wider mb-1">Tasa Conversión</p>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold text-green-900">{{ stats.conversion_rate }}%</span>
                        <ExternalLink :size="20" class="text-green-400" />
                    </div>
                </div>
            </div>

            <!-- Clicks by Offering Table -->
            <div>
                <div class="flex items-center gap-2 mb-4 text-sm font-semibold text-slate-700">
                    <BarChart3 :size="16" class="text-slate-400" />
                    Clicks por Oferta
                </div>
                
                <div v-if="stats.by_offering.length > 0" class="space-y-3">
                    <div 
                        v-for="item in stats.by_offering" 
                        :key="item.name"
                        class="group"
                    >
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-medium text-slate-700 truncate max-w-[180px]">
                                {{ item.name }}
                            </span>
                            <span class="text-xs font-bold text-indigo-600">
                                {{ item.count }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div 
                                class="bg-indigo-500 h-full rounded-full transition-all duration-1000"
                                :style="{ width: `${(item.count / stats.total_clicks) * 100}%` }"
                            ></div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8 bg-slate-50 rounded-lg border-2 border-dashed border-slate-200">
                    <p class="text-xs text-slate-500">Aún no hay clicks registrados</p>
                </div>
            </div>
            
            <!-- CTA for sharing -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-[11px] text-slate-500 mb-2 italic">
                    Comparte tus links del catálogo para ver estadísticas aquí.
                </p>
                <button 
                  @click="router.visit(route('admin.offerings.index'))"
                  class="w-full py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                >
                  Ver Catálogo
                </button>
            </div>
        </div>
    </div>
</template>
