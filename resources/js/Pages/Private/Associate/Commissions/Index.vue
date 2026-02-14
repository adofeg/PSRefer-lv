<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { DollarSign, TrendingUp, Clock, CheckCircle, Calendar, User, Building2, CreditCard, Hash } from 'lucide-vue-next';

const props = defineProps({
    commissions: Array,
    totalEarned: Number,
    pendingPayment: Number,
    paidTotal: Number
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount || 0);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getStatusColor = (status) => {
    const colors = {
        'Cerrado': 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        'Ganado': 'bg-indigo-100 text-indigo-700 border border-indigo-200',
        'Pagado': 'bg-blue-100 text-blue-700 border border-blue-200',
        'Prospecto': 'bg-amber-100 text-amber-700 border border-amber-200',
        'Enviado': 'bg-slate-100 text-slate-700 border border-slate-200',
        'Perdido': 'bg-rose-100 text-rose-700 border border-rose-200'
    };
    return colors[status] || 'bg-slate-50 text-slate-500 border border-slate-100';
};

const totalAgencyFees = props.commissions.reduce((sum, c) => sum + parseFloat(c.agency_fee || 0), 0);
</script>

<template>
    <Head title="Mis Comisiones" />

    <AppLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Header with Total Earnings (Gradient Style) -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl p-8 md:p-10 text-white shadow-2xl shadow-emerald-900/10 flex flex-col md:flex-row items-center justify-between gap-8 relative overflow-hidden border border-emerald-400/20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-24 -mt-24 blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-400/10 rounded-full -ml-16 -mb-16 blur-2xl pointer-events-none"></div>

                <div class="flex items-center gap-8 z-10 w-full md:w-auto">
                    <div class="bg-white/10 p-5 rounded-2xl backdrop-blur-md border border-white/20 shadow-inner">
                        <DollarSign :size="48" class="text-emerald-100" />
                    </div>
                    <div>
                        <p class="text-emerald-200 font-bold mb-1 uppercase tracking-[0.2em] text-[10px]">Mis Ganancias Pagadas</p>
                        <h2 class="text-5xl md:text-6xl font-black tracking-tighter">{{ formatCurrency(paidTotal) }}</h2>
                    </div>
                </div>

                <div class="flex flex-col gap-2 text-right z-10 bg-black/20 p-6 rounded-2xl backdrop-blur-md border border-white/10 min-w-[200px] shadow-lg">
                    <span class="text-[10px] text-emerald-300 uppercase font-black tracking-widest">En Proceso / Cierre</span>
                    <span class="text-3xl font-black text-white px-2">{{ formatCurrency(pendingPayment) }}</span>
                </div>
            </div>

            <!-- Detail Table -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/60 overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                    <h3 class="font-black text-slate-800 flex items-center gap-3 text-lg">
                        <div class="bg-emerald-500 text-white p-2 rounded-lg shadow-lg shadow-emerald-500/20">
                            <TrendingUp :size="20" />
                        </div>
                        Historial de Comisiones
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-black text-[10px] tracking-[0.15em]">
                            <tr>
                                <th class="px-6 py-5">Fecha</th>
                                <th class="px-6 py-5">Referencia</th>
                                <th class="px-6 py-5 text-center">Tipo</th>
                                <th class="px-6 py-5 text-right">Base / Trato</th>
                                <th class="px-6 py-5 text-center">Estatus</th>
                                <th class="px-6 py-5 text-right font-black text-slate-900 bg-slate-100/30">Mi Comisión</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 italic-hover">
                            <tr v-for="commission in commissions" :key="commission.id" class="hover:bg-slate-50/80 transition-all duration-300 group">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-800">{{ formatDate(commission.closed_at || commission.updated_at) }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ commission.contract_id || '#' + commission.id }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-black text-xs group-hover:bg-indigo-50 group-hover:text-indigo-400 transition-colors">
                                            {{ commission.client_name?.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 text-sm tracking-tight">{{ commission.client_name }}</div>
                                            <div class="text-[11px] text-slate-500 font-semibold flex items-center gap-1">
                                                <Building2 :size="10" /> {{ commission.offering?.name || 'Servicio' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span v-if="commission.offering?.base_commission > 0" class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-wider">FIJA</span>
                                    <span v-else class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-wider">% VARIABLE</span>
                                </td>
                                <td class="px-6 py-5 text-right font-bold text-slate-600">
                                    <div class="flex flex-col items-end">
                                        <span>{{ formatCurrency(commission.deal_value || 0) }}</span>
                                        <span v-if="commission.offering?.commission_rate > 0" class="text-[9px] text-slate-400 font-black">@ {{ commission.offering.commission_rate }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span :class="['px-3 py-1 text-[10px] font-black uppercase rounded-full shadow-sm', getStatusColor(commission.status)]">
                                        {{ commission.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right font-black text-slate-900 bg-slate-50/50 text-lg tracking-tighter group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-all">
                                    {{ formatCurrency(commission.commissions?.reduce((sum, c) => sum + parseFloat(c.amount), 0) || 0) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="commissions.length === 0" class="p-20 text-center bg-slate-50/50">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 text-slate-300 mb-4">
                            <Clock :size="32" />
                        </div>
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Aún no hay comisiones registradas</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>