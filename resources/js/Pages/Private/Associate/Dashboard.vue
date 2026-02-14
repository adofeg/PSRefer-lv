<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Badge from '@/Components/UI/Badge.vue';
import { DollarSign, Clock, AlertCircle, TrendingUp, FileText, ArrowRight } from 'lucide-vue-next';
import LineChart from '@/Components/Dashboard/LineChart.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    total_earned: Number,
    pending_earned: Number,
    lost_potential: Number,
    total_referrals: Number,
    conversion_rate: Number,
    recent_activity: Array,
    chart_data: Object, // { labels: [], values: [] }
    filters: Object // { year: 2024, month: null, available_years: [] }
});

const activeTab = ref('stats');
const selectedYear = ref(props.filters?.year || new Date().getFullYear());
const selectedMonth = ref(props.filters?.month || null);
const monthsList = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

const monthName = (m) => monthsList[m - 1];

const applyFilters = () => {
    router.get(
        route('associate.dashboard'),
        { 
            year: selectedYear.value,
            month: selectedMonth.value
        },
        { 
            preserveState: true, 
            preserveScroll: true, 
            only: ['chart_data', 'filters'] 
        }
    );
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};
</script>

<template>
    <Head title="Panel de Asociado" />

    <AppLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Panel de Control</h1>
                    <p class="text-slate-500">Resumen de tu actividad y comisiones</p>
                </div>
                 <!-- No Quick Action Buttons as requested -->
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- 1. Ganadas (Paid) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-32 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-green-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div>
                        <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-1">Comisiones Pagadas</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ formatCurrency(total_earned) }}</h3>
                    </div>
                    <div class="flex items-center gap-2 z-10">
                        <div class="p-1.5 bg-green-100 text-green-600 rounded-lg">
                            <DollarSign :size="16" />
                        </div>
                        <span class="text-xs text-slate-500">Total histórico</span>
                    </div>
                </div>

                <!-- 2. Pendientes (Closed/In Process) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-32 relative overflow-hidden group">
                     <div class="absolute right-0 top-0 w-24 h-24 bg-yellow-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div>
                        <p class="text-xs font-bold text-yellow-600 uppercase tracking-wider mb-1">Pendiente de Pago</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ formatCurrency(pending_earned) }}</h3>
                    </div>
                    <div class="flex items-center gap-2 z-10">
                         <div class="p-1.5 bg-yellow-100 text-yellow-600 rounded-lg">
                            <Clock :size="16" />
                        </div>
                        <span class="text-xs text-slate-500">En proceso o cierre</span>
                    </div>
                </div>

                <!-- 3. Perdidas (Lost) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-32 relative overflow-hidden group">
                     <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div>
                        <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Potencial Perdido</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ formatCurrency(lost_potential) }}</h3>
                    </div>
                    <div class="flex items-center gap-2 z-10">
                         <div class="p-1.5 bg-red-100 text-red-600 rounded-lg">
                            <AlertCircle :size="16" />
                        </div>
                        <span class="text-xs text-slate-500">Referidos no cerrados</span>
                    </div>
                </div>

                <!-- 4. Conversion Rate -->
                 <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-32 relative overflow-hidden group">
                     <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div>
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Tasa de Cierre</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ conversion_rate }}%</h3>
                    </div>
                    <div class="flex items-center gap-2 z-10">
                         <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg">
                            <TrendingUp :size="16" />
                        </div>
                        <span class="text-xs text-slate-500">{{ total_referrals }} referidos totales</span>
                    </div>
                </div>
            </div>

            <!-- Tabs Section (Chart & Activity) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="border-b border-slate-100 px-6 pt-6 flex custom-scrollbar overflow-x-auto gap-8 justify-between items-center">
                    <div class="flex gap-8">
                        <button 
                            @click="activeTab = 'stats'"
                            class="pb-4 text-sm font-bold uppercase tracking-wide border-b-2 transition-colors whitespace-nowrap"
                            :class="activeTab === 'stats' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
                        >
                            Estadísticas
                        </button>
                        <button 
                            @click="activeTab = 'activity'"
                            class="pb-4 text-sm font-bold uppercase tracking-wide border-b-2 transition-colors whitespace-nowrap"
                            :class="activeTab === 'activity' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
                        >
                            Actividad Reciente
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Tab 1: Stats (Chart) -->
                    <div v-if="activeTab === 'stats'" class="animate-fade-in space-y-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <h3 class="font-bold text-slate-800">
                                Rendimiento de Referidos 
                                <span class="text-slate-400 font-normal ml-2">
                                    {{ filters.month ? `${monthName(filters.month)} ${filters.year}` : `Año ${filters.year}` }}
                                </span>
                            </h3>
                            
                            <!-- Filters -->
                            <div class="flex items-center gap-3">
                                <select 
                                    v-model="selectedMonth" 
                                    @change="applyFilters"
                                    class="text-xs border-slate-200 rounded-lg py-1.5 pl-2 pr-8 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 text-slate-600 font-medium"
                                >
                                    <option :value="null">Todo el Año</option>
                                    <option v-for="(m, i) in monthsList" :key="i" :value="i + 1">{{ m }}</option>
                                </select>

                                <select 
                                    v-model="selectedYear" 
                                    @change="applyFilters"
                                    class="text-xs border-slate-200 rounded-lg py-1.5 pl-2 pr-8 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 text-slate-600 font-medium"
                                >
                                    <option v-for="year in filters.available_years" :key="year" :value="year">{{ year }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Line Chart Component -->
                        <div class="h-80 w-full relative">
                            <LineChart 
                                :labels="chart_data.labels" 
                                :values="chart_data.values" 
                                :label="filters.month ? 'Referidos (Por Día)' : 'Referidos (Por Mes)'"
                            />
                        </div>
                    </div>

                    <!-- Tab 2: Recent Activity -->
                    <div v-if="activeTab === 'activity'" class="animate-fade-in">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <FileText :size="20" class="text-slate-400" />
                                Últimos Movimientos
                            </h3>
                            <Link :href="route('associate.referrals.index')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                                Ver Todos <ArrowRight :size="12" />
                            </Link>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-slate-100">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs">
                                    <tr>
                                        <th class="px-6 py-4">Cliente</th>
                                        <th class="px-6 py-4">Servicio</th>
                                        <th class="px-6 py-4">Estado</th>
                                        <th class="px-6 py-4 text-right">Comisión Est.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="activity in recent_activity" :key="activity.id" class="hover:bg-slate-50 transition group">
                                        <td class="px-6 py-4 font-medium text-slate-800">{{ activity.client_name }}</td>
                                        <td class="px-6 py-4 text-slate-500">{{ activity.offering_name }}</td>
                                        <td class="px-6 py-4"><Badge :status="activity.status" /></td>
                                        <td class="px-6 py-4 text-right font-mono text-slate-600 font-bold group-hover:text-indigo-600 transition">
                                            {{ formatCurrency(activity.amount) }}
                                        </td>
                                    </tr>
                                    <tr v-if="recent_activity.length === 0">
                                        <td colspan="4" class="text-center py-12 text-slate-400">
                                            No hay actividad reciente para mostrar.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
