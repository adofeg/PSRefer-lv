<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
        'Cerrado': 'bg-green-100 text-green-700',
        'Ganado': 'bg-blue-100 text-blue-700',
        'Pagado': 'bg-purple-100 text-purple-700'
    };
    return colors[status] || 'bg-slate-100 text-slate-700';
};

const totalAgencyFees = props.commissions.reduce((sum, c) => sum + parseFloat(c.agency_fee || 0), 0);
</script>

<template>
    <Head title="Mis Comisiones" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Header with Total Earnings (Gradient Style) -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 md:p-8 text-white shadow-xl shadow-emerald-900/10 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-16 -mt-16 pointer-events-none"></div>

                <div class="flex items-center gap-6 z-10">
                    <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-sm border border-white/10">
                        <DollarSign :size="40" class="text-white" />
                    </div>
                    <div>
                        <p class="text-emerald-100 font-medium mb-1 uppercase tracking-wide text-xs">Ganancias Totales</p>
                        <h2 class="text-4xl md:text-5xl font-bold tracking-tight">{{ formatCurrency(totalEarned) }}</h2>
                    </div>
                </div>

                <div class="flex flex-col gap-1 text-right z-10 bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/5 min-w-[160px]">
                    <span class="text-xs text-emerald-200 uppercase font-bold">Cuotas de Agencia</span>
                    <span class="text-xl font-bold">{{ formatCurrency(totalAgencyFees) }}</span>
                </div>
            </div>

            <!-- Detail Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <DollarSign :size="20" class="text-emerald-500" />
                        Detalle de Ganancias
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-4"><div class="flex items-center gap-1"><Calendar :size="12" /> Fecha</div></th>
                                <th class="px-4 py-4"><div class="flex items-center gap-1"><User :size="12" /> Asegurado</div></th>
                                <th class="px-4 py-4"><div class="flex items-center gap-1"><Building2 :size="12" /> Servicio</div></th>
                                <th class="px-4 py-4"><div class="flex items-center gap-1"><Hash :size="12" /> Póliza/ID</div></th>
                                <th class="px-4 py-4"><div class="flex items-center gap-1"><CreditCard :size="12" /> Pago</div></th>
                                <th class="px-4 py-4 text-right">Eng. Inicial</th>
                                <th class="px-4 py-4 text-right">Cuota Agencia</th>
                                <th class="px-4 py-4 text-right">Total</th>
                                <th class="px-4 py-4 text-right">Real</th>
                                <th class="px-4 py-4 text-right">Dif.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="commission in commissions" :key="commission.id" class="hover:bg-slate-50/50 transition duration-200">
                                <td class="px-4 py-4 whitespace-nowrap text-xs font-medium text-slate-500">
                                    {{ formatDate(commission.closed_at || commission.updated_at) }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-800">{{ commission.client_name }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium">{{ commission.client_contact }}</div>
                                </td>
                                <td class="px-4 py-4 text-slate-600 font-medium">
                                    {{ commission.offering?.name || 'General' }}
                                </td>
                                <td class="px-4 py-4 font-mono text-xs text-slate-400">
                                    {{ commission.contract_id || '-' }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-bold text-slate-500">{{ commission.payment_method || '-' }}</span>
                                        <span :class="['px-2 py-0.5 text-[9px] font-black uppercase rounded-full w-fit', getStatusColor(commission.status)]">
                                            {{ commission.status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right font-medium text-slate-600">
                                    {{ formatCurrency(commission.down_payment) }}
                                </td>
                                <td class="px-4 py-4 text-right font-medium text-slate-600">
                                    {{ formatCurrency(commission.agency_fee) }}
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-slate-900">
                                    {{ formatCurrency(commission.revenue_generated) }}
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-emerald-600">
                                    {{ formatCurrency((commission.revenue_generated || 0) - (commission.agency_fee || 0)) }}
                                </td>
                                <td class="px-4 py-4 text-right font-bold" :class="((commission.revenue_generated || 0) - (commission.agency_fee || 0) - (commission.down_payment || 0)) >= 0 ? 'text-emerald-500' : 'text-rose-500'">
                                    {{ formatCurrency((commission.revenue_generated || 0) - (commission.agency_fee || 0) - (commission.down_payment || 0)) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="commissions.length > 0" class="bg-slate-50 font-black text-slate-700">
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-right text-xs uppercase">Totales Actuales:</td>
                                <td class="px-4 py-4 text-right">{{ formatCurrency(commissions.reduce((sum, c) => sum + parseFloat(c.down_payment || 0), 0)) }}</td>
                                <td class="px-4 py-4 text-right">{{ formatCurrency(totalAgencyFees) }}</td>
                                <td class="px-4 py-4 text-right">{{ formatCurrency(totalEarned) }}</td>
                                <td class="px-4 py-4 text-right text-emerald-600">
                                    {{ formatCurrency(commissions.reduce((sum, c) => sum + (parseFloat(c.revenue_generated || 0) - parseFloat(c.agency_fee || 0)), 0)) }}
                                </td>
                                <td class="px-4 py-4 text-right">-</td>
                            </tr>
                        </tfoot>
                    </table>
                    <div v-if="commissions.length === 0" class="p-12 text-center text-slate-400">
                        No hay comisiones registradas
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
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
