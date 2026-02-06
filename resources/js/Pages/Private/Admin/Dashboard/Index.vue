<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';
import { DollarSign, ShoppingBag, BarChart3, FileText, ArrowRight, Phone, Send } from 'lucide-vue-next';
import RevenueChart from '@/Components/Dashboard/RevenueChart.vue';
import ClickStatsWidget from '@/Components/Dashboard/ClickStatsWidget.vue';
import QuickLinks from '@/Components/Dashboard/QuickLinks.vue';

const props = defineProps({
    stats: Object,
    recentReferrals: Array,
    monthlyRevenue: Array,
    accountManager: Object,
    auth: Object
});

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
        <div class="space-y-6 animate-fade-in">
            <!-- Hero Metric Card -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-2xl p-6 md:p-8 text-white shadow-xl shadow-indigo-900/10 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16 pointer-events-none"></div>

                <div class="flex items-center gap-6 z-10">
                    <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-sm border border-white/10">
                        <DollarSign :size="40" class="text-white" />
                    </div>
                    <div>
                        <p class="text-indigo-100 font-medium mb-1 uppercase tracking-wide text-xs">Total Earnings</p>
                        <h2 class="text-4xl md:text-5xl font-bold tracking-tight">{{ formatCurrency(stats.current_balance) }}</h2>
                        <div class="flex items-center gap-2 mt-2 text-indigo-100 text-sm">
                            <span class="bg-green-500/20 text-green-100 px-2 py-0.5 rounded text-xs font-bold">+12%</span>
                            <span>vs last month</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1 text-right z-10 bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/5 min-w-[140px]">
                    <span class="text-xs text-indigo-200 uppercase font-bold">Revenue Generated</span>
                    <span class="text-xl font-bold">{{ formatCurrency(stats.total_revenue) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <Link
                    :href="route('admin.offerings.index')"
                    class="p-6 bg-white border-2 border-slate-200 text-slate-700 rounded-xl hover:border-indigo-500 hover:text-indigo-600 transition flex items-center justify-between gap-3 group shadow-sm hover:shadow-md"
                >
                    <div class="flex items-center gap-4">
                        <div class="bg-slate-100 p-3 rounded-full group-hover:bg-indigo-100 transition">
                            <ShoppingBag :size="32" class="text-slate-600 group-hover:text-indigo-600" />
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-lg">Explorar Catálogo</div>
                            <div class="text-sm text-slate-500 group-hover:text-indigo-500">Descubre servicios para referir</div>
                        </div>
                    </div>
                    <ArrowRight :size="24" />
                </Link>
            </div>

            <!-- Quick Referral Links (Migration Parity) -->
            <div class="grid grid-cols-1 gap-6">
                <QuickLinks :user="auth.user" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Revenue Chart -->
                <Card class="lg:col-span-2">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <BarChart3 :size="20" class="text-indigo-500" />
                            Tendencia de Ingresos
                        </h3>
                        <span class="text-xs text-slate-400">Últimos meses</span>
                    </div>
                    <div class="h-64">
                        <RevenueChart :data="monthlyRevenue" />
                    </div>
                </Card>

                <!-- Click Tracking Stats -->
                <ClickStatsWidget class="lg:col-span-1" />

                <!-- Account Manager Widget -->
                <Card v-if="accountManager" class="lg:col-span-1 bg-gradient-to-br from-slate-800 to-slate-900 text-white border-none shadow-xl">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden border-2 border-white/20 bg-white/10 p-0.5">
                            <img v-if="accountManager.logo_url" :src="accountManager.logo_url" class="w-full h-full object-cover rounded-[14px]" />
                            <div v-else class="w-full h-full flex items-center justify-center bg-indigo-500 text-white font-black text-xl">
                                {{ accountManager.name.charAt(0) }}
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] text-indigo-300 uppercase font-black tracking-widest mb-0.5">Tu Account Manager</p>
                            <p class="font-bold text-lg leading-tight">{{ accountManager.name }}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <a v-if="accountManager.phone" :href="`tel:${accountManager.phone}`" class="w-full bg-white/10 hover:bg-white/20 py-3 rounded-xl flex items-center justify-center gap-2 text-xs font-black uppercase tracking-wider transition">
                            <Phone :size="14" /> {{ accountManager.phone }}
                        </a>
                        <a v-if="accountManager.email" :href="`mailto:${accountManager.email}`" class="w-full bg-indigo-600 hover:bg-indigo-700 py-3 rounded-xl flex items-center justify-center gap-2 text-xs font-black uppercase tracking-wider transition shadow-lg shadow-indigo-900/40">
                            <Send :size="14" /> ENVIAR EMAIL
                        </a>
                    </div>
                </Card>

                <!-- Recent Referrals -->
                <Card class="lg:col-span-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <FileText :size="20" class="text-slate-400" />
                            Referidos Recientes
                        </h3>
                        <Link :href="route('admin.referrals.index')" class="text-xs text-indigo-600 hover:underline">Ver Todos</Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-slate-500 uppercase font-medium">
                                <tr>
                                    <th class="px-4 py-3">Cliente</th>
                                    <th class="px-4 py-3">Servicio</th>
                                    <th class="px-4 py-3">Estatus</th>
                                    <th class="px-4 py-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="referral in recentReferrals" :key="referral.id" class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ referral.client_name }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ referral.offering?.name }}</td>
                                    <td class="px-4 py-3"><Badge :status="referral.status" /></td>
                                    <td class="px-4 py-3 text-right">
                                        <Link :href="route('admin.referrals.show', referral.id)" class="text-xs text-slate-400 hover:text-indigo-600">Ver</Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-if="recentReferrals.length === 0" class="text-center text-slate-400 py-4">No hay referidos recientes</p>
                    </div>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>