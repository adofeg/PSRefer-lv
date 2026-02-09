<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { useFormatters } from '@/Composables/useFormatters';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    stats: Object
});

const { formatCurrency } = useFormatters();

const chartData = {
    labels: props.stats.chart.labels,
    datasets: [
        {
            label: 'Comisiones Generadas',
            backgroundColor: '#4f46e5',
            data: props.stats.chart.data
        }
    ]
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
};
</script>

<template>
    <Head title="Reporte Financiero" />

    <AppLayout>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Reporte Financiero</h1>
                    <p class="text-slate-500">Resumen de comisiones y pagos.</p>
                </div>
                <div class="flex gap-2">
                     <Link
                        :href="route('admin.commissions.index')"
                        class="px-4 py-2 text-slate-600 hover:bg-slate-50 border border-slate-300 rounded-lg text-sm"
                    >
                        Volver al Listado
                    </Link>
                    <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm shadow-sm">
                        Exportar Excel
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Card class="border-l-4 border-l-emerald-500">
                    <div class="text-sm text-slate-500 font-medium">Total Pagado</div>
                    <div class="text-2xl font-bold text-slate-800 mt-1">{{ formatCurrency(stats.totals.paid) }}</div>
                    <div class="text-xs text-slate-400 mt-1">{{ stats.totals.count_paid }} transacciones</div>
                </Card>
                 <Card class="border-l-4 border-l-amber-500">
                    <div class="text-sm text-slate-500 font-medium">Pendiente de Pago</div>
                    <div class="text-2xl font-bold text-slate-800 mt-1">{{ formatCurrency(stats.totals.pending) }}</div>
                    <div class="text-xs text-slate-400 mt-1">{{ stats.totals.count_pending }} transacciones</div>
                </Card>
                 <Card class="border-l-4 border-l-slate-400">
                    <div class="text-sm text-slate-500 font-medium">Anulado / Void</div>
                    <div class="text-2xl font-bold text-slate-800 mt-1">{{ formatCurrency(stats.totals.void) }}</div>
                    <div class="text-xs text-slate-400 mt-1">N/A</div>
                </Card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart -->
                <Card class="lg:col-span-2 min-h-[400px]">
                    <h3 class="font-bold text-slate-800 mb-4">Comportamiento Anual</h3>
                    <div class="h-80">
                        <Bar :data="chartData" :options="chartOptions" />
                    </div>
                </Card>

                <!-- Top Associates -->
                <Card>
                    <h3 class="font-bold text-slate-800 mb-4">Top Asociados (Pagado)</h3>
                    <div class="space-y-4">
                        <div v-for="(assoc, index) in stats.top_associates" :key="index" class="flex items-center justify-between pb-3 border-b border-slate-50 last:border-0">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                    {{ index + 1 }}
                                </span>
                                <span class="text-sm font-medium text-slate-700">{{ assoc.name }}</span>
                            </div>
                            <span class="text-sm font-bold text-emerald-600">{{ formatCurrency(assoc.amount) }}</span>
                        </div>
                         <div v-if="stats.top_associates.length === 0" class="text-sm text-slate-400 text-center py-4">
                            Sin datos aún.
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
