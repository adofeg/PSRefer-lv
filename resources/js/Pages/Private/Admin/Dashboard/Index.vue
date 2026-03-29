<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';
import { DollarSign, BarChart3, FileText, ArrowRight, Phone, Send, Trophy, Layers } from 'lucide-vue-next';
import LineChart from '@/Components/Dashboard/LineChart.vue';

const props = defineProps({
    stats: Object,
    recentReferrals: Array,
    monthlyRevenue: Array,
    accountManager: Object,
    auth: Object
});

const activeTab = ref('services');

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="space-y-6 lg:p-6">
            <!-- Hero Metric Card -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-900 rounded-2xl p-6 md:p-8 text-white shadow-2xl shadow-indigo-900/20 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden border border-white/5 shrink-0">
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/10 rounded-full -mr-32 -mt-32 blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/5 rounded-full -ml-16 -mb-16 blur-2xl pointer-events-none"></div>

                <div class="flex items-center gap-6 z-10 w-full md:w-auto">
                    <div class="bg-white/10 p-5 rounded-2xl backdrop-blur-md border border-white/10 shadow-inner">
                        <DollarSign :size="40" class="text-indigo-300" />
                    </div>
                    <!-- Admin View -->
                    <div v-if="stats.is_admin" class="flex-1">
                        <p class="text-indigo-300/80 font-bold mb-1 uppercase tracking-[0.2em] text-[10px]">Capital de Comisiones (Global)</p>
                        <h2 class="text-4xl md:text-6xl font-black tracking-tighter">{{ formatCurrency(stats.total_commissions) }}</h2>
                        <div class="flex items-center gap-4 mt-3">
                            <div class="flex items-center gap-1.5 bg-indigo-500/20 px-3 py-1 rounded-full border border-indigo-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest">{{ stats.total_users }} Usuarios</span>
                            </div>
                            <div class="flex items-center gap-1.5 bg-emerald-500/20 px-3 py-1 rounded-full border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span class="text-[10px] font-black text-emerald-100 uppercase tracking-widest">Tasa: {{ stats.conversion_rate }}%</span>
                            </div>
                        </div>
                    </div>
                    <!-- Associate View -->
                    <div v-else class="flex-1">
                        <p class="text-indigo-300/80 font-bold mb-1 uppercase tracking-[0.2em] text-[10px]">Mi Patrimonio Acumulado</p>
                        <h2 class="text-4xl md:text-6xl font-black tracking-tighter">{{ formatCurrency(stats.current_balance) }}</h2>
                        <div class="flex items-center gap-4 mt-3">
                            <div class="flex items-center gap-1.5 bg-emerald-500/20 px-3 py-1 rounded-full border border-emerald-500/30 text-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span class="text-[10px] font-black uppercase tracking-widest">Pago Activo</span>
                            </div>
                            <div class="text-[10px] font-bold text-indigo-300/60 uppercase tracking-widest ml-auto">
                                {{ stats.successful_referrals }} Éxitos totales
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-px bg-white/5 rounded-2xl overflow-hidden backdrop-blur-sm border border-white/10 z-10 w-full md:w-auto shrink-0">
                    <div class="bg-white/5 p-4 flex flex-col items-center justify-center min-w-[140px] hover:bg-white/10 transition-colors">
                        <span class="text-[9px] text-indigo-300/60 uppercase font-black tracking-widest mb-1">{{ stats.is_admin ? 'Liquidados' : 'Por Cobrar' }}</span>
                        <span class="text-lg font-black">{{ stats.is_admin ? formatCurrency(stats.paid_commissions) : formatCurrency(stats.pending_to_collect) }}</span>
                    </div>
                    <div class="bg-white/5 p-4 flex flex-col items-center justify-center min-w-[140px] hover:bg-white/10 transition-colors">
                        <span class="text-[9px] text-indigo-300/60 uppercase font-black tracking-widest mb-1">{{ stats.is_admin ? 'En Revisión' : 'En Gestión' }}</span>
                        <span class="text-lg font-black">{{ stats.is_admin ? formatCurrency(stats.pending_commissions) : stats.active_referrals }}</span>
                    </div>
                </div>
            </div>

            <!-- KPI Cards Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 shrink-0">
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors"><BarChart3 :size="20"/></div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Conversión</p>
                        <p class="text-xl font-black text-slate-800">{{ stats.conversion_rate }}%</p>
                    </div>
                </div>
                <div v-if="stats.is_admin" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors"><DollarSign :size="20"/></div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Comisión Prom.</p>
                        <p class="text-xl font-black text-slate-800">{{ formatCurrency(stats.avg_commission) }}</p>
                    </div>
                </div>
                <div v-if="!stats.is_admin" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition-colors"><Phone :size="20"/></div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Pendientes</p>
                        <p class="text-xl font-black text-slate-800">{{ stats.upcoming_reminders }}</p>
                    </div>
                </div>
                 <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                    <div class="p-3 bg-slate-50 text-slate-400 rounded-xl group-hover:bg-slate-600 group-hover:text-white transition-colors"><FileText :size="20"/></div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Total Referidos</p>
                        <p class="text-xl font-black text-slate-800">{{ stats.total_referrals || 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Middle Row: Chart + Tabbed Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart Area -->
                <div class="lg:col-span-2">
                    <Card class="h-full flex flex-col overflow-hidden">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-50 p-6 pb-4 shrink-0">
                            <h3 class="font-black text-slate-800 flex items-center gap-2 uppercase tracking-tight text-sm">
                                <BarChart3 :size="18" class="text-indigo-500" />
                                Tendencia de Pago
                            </h3>
                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Ciclo Anual</span>
                        </div>
                        <div class="p-6 pt-0 flex-1 min-h-[350px]">
                            <LineChart :labels="['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']" :values="monthlyRevenue" label="Desembolsos ($)" />
                        </div>
                    </Card>
                </div>

                <!-- Tabbed Sidebar -->
                <div class="lg:col-span-1 flex flex-col gap-6">
                    <Card class="flex-1 flex flex-col overflow-hidden">
                        <div class="p-6 flex flex-col h-full overflow-hidden">
                            <!-- Integrated Segmented Header -->
                            <div class="inline-flex p-1 bg-slate-100/50 rounded-xl w-full mb-6 shrink-0 border border-slate-100">
                                <button @click="activeTab = 'services'" 
                                    class="flex-1 py-2 px-3 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2"
                                    :class="activeTab === 'services' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400 hover:text-slate-600'">
                                    <Layers :size="14" /> Servicios
                                </button>
                                <button v-if="stats.is_admin" @click="activeTab = 'associates'" 
                                    class="flex-1 py-2 px-3 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2"
                                    :class="activeTab === 'associates' ? 'bg-white shadow-sm text-amber-600' : 'text-slate-400 hover:text-slate-600'">
                                    <Trophy :size="14" /> Asociados
                                </button>
                            </div>

                            <!-- Scrollable Sidebar Content -->
                            <div class="flex-1 space-y-6 overflow-y-auto pr-1 custom-scrollbar min-h-[300px]">
                                <!-- Tab: Services -->
                                <div v-show="activeTab === 'services'" class="space-y-5">
                                    <div v-for="(offering, idx) in stats.top_offerings" :key="idx" class="space-y-1.5 group">
                                        <div class="flex justify-between text-[11px] font-bold">
                                            <span class="text-slate-600 group-hover:text-indigo-600 transition-colors truncate max-w-[70%]">{{ offering.name }}</span>
                                            <span class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md text-[9px]">{{ offering.count }} ref</span>
                                        </div>
                                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-indigo-500 h-full rounded-full transition-all duration-700" :style="`width: ${ (offering.count / (stats.top_offerings[0]?.count || 1)) * 100 }%`"></div>
                                        </div>
                                    </div>
                                    <div v-if="!stats.top_offerings?.length" class="h-40 flex items-center justify-center text-[10px] text-slate-300 uppercase font-black tracking-widest">Sin datos</div>
                                </div>

                                <!-- Tab: Associates (Admin Only) -->
                                <div v-show="activeTab === 'associates'" class="space-y-3">
                                    <div v-for="(assoc, idx) in stats.top_associates" :key="idx" class="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-slate-50 transition-all duration-300 group border border-transparent hover:border-slate-100">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center font-black text-[10px] shrink-0 group-hover:bg-amber-100 group-hover:text-amber-600 transition-colors shadow-inner">
                                            {{ idx + 1 }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-black text-slate-800 truncate group-hover:text-amber-700 transition-colors">{{ assoc.name }}</p>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ assoc.count }} cierres efectivos</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100 shadow-sm">{{ formatCurrency(assoc.total_commissions) }}</p>
                                        </div>
                                    </div>
                                    <div v-if="!stats.top_associates?.length" class="h-40 flex items-center justify-center text-[10px] text-slate-300 uppercase font-black tracking-widest">Sin datos</div>
                                </div>
                            </div>
                        </div>
                    </Card>

                    <!-- Account Manager (Associate Only) -->
                    <Card v-if="!stats.is_admin && accountManager" class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white border-none shadow-xl shadow-indigo-950/20 p-6">
                         <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 rounded-2xl overflow-hidden border-2 border-white/20 bg-white/10 p-0.5">
                                <img v-if="accountManager.logo_url" :src="accountManager.logo_url" class="w-full h-full object-cover rounded-[14px]" />
                                <div v-else class="w-full h-full flex items-center justify-center bg-indigo-500 text-white font-black text-xl">
                                    {{ accountManager.name.charAt(0) }}
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] text-indigo-300 uppercase font-black tracking-widest mb-0.5">Account Manager</p>
                                <p class="font-bold text-lg leading-tight">{{ accountManager.name }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <a v-if="accountManager.phone" :href="`tel:${accountManager.phone}`" class="w-full bg-white/10 hover:bg-white/20 py-3 rounded-xl flex items-center justify-center gap-2 text-xs font-black uppercase tracking-wider transition">
                                <Phone :size="14" /> LLAMAR
                            </a>
                            <a v-if="accountManager.email" :href="`mailto:${accountManager.email}`" class="w-full bg-indigo-600 hover:bg-indigo-700 py-3 rounded-xl flex items-center justify-center gap-2 text-xs font-black uppercase tracking-wider transition">
                                <Send :size="14" /> MENSAJE
                            </a>
                        </div>
                    </Card>
                </div>
            </div>

            <!-- Recent Referrals -->
            <Card>
                <div class="flex justify-between items-center mb-6 p-6 pb-0">
                    <h3 class="font-black text-slate-800 flex items-center gap-2 uppercase tracking-tight text-sm">
                        <FileText :size="18" class="text-slate-400" />
                        Referidos Recientes
                    </h3>
                    <Link :href="route('admin.referrals.index')" class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-700 flex items-center gap-1 group">
                        Ver Todos <ArrowRight :size="12" class="group-hover:translate-x-1 transition-transform" />
                    </Link>
                </div>

                <div class="p-6 pt-2">
                    <div class="overflow-x-auto rounded-xl border border-slate-50">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50/50 text-slate-500 uppercase font-black tracking-widest text-[9px]">
                                <tr>
                                    <th class="px-6 py-4">Cliente</th>
                                    <th class="px-6 py-4">Servicio</th>
                                    <th class="px-6 py-4">Estatus</th>
                                    <th class="px-6 py-4 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="referral in recentReferrals" :key="referral.id" class="hover:bg-slate-50 transition group">
                                    <td class="px-6 py-4 font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ referral.client_name }}</td>
                                    <td class="px-6 py-4 text-slate-500 font-medium">{{ referral.offering?.name }}</td>
                                    <td class="px-6 py-4"><Badge :status="referral.status" /></td>
                                    <td class="px-6 py-4 text-right">
                                        <Link :href="route('admin.referrals.show', referral.id)" class="text-indigo-600 hover:text-indigo-800 font-black text-[10px] uppercase tracking-widest">
                                            Detalles
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="!recentReferrals?.length">
                                    <td colspan="4" class="px-6 py-10 text-center text-[10px] text-slate-400 uppercase font-black tracking-widest">No hay actividad reciente</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>