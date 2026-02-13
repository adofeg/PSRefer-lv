<script setup>
import { computed } from 'vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';
import { Line as LineChartComponent } from 'vue-chartjs';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

const props = defineProps({
    labels: {
        type: Array,
        default: () => []
    },
    values: {
        type: Array,
        default: () => []
    },
    label: {
        type: String,
        default: 'Referidos'
    }
});

const chartData = computed(() => {
    return {
        labels: props.labels,
        datasets: [
            {
                label: props.label,
                backgroundColor: 'rgba(99, 102, 241, 0.1)', // Indigo-500 low opacity
                borderColor: '#6366f1', // Indigo-500
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#6366f1',
                pointHoverBackgroundColor: '#6366f1',
                pointHoverBorderColor: '#ffffff',
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                data: props.values,
                fill: true,
                tension: 0.4
            }
        ]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: '#1e293b',
            padding: 12,
            titleFont: { size: 13 },
            bodyFont: { size: 13 },
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += context.parsed.y;
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            border: { display: false },
            grid: {
                color: '#f1f5f9', // Slate-100
                drawBorder: false,
            },
            ticks: {
                font: { size: 11 },
                color: '#64748b', // Slate-500
                precision: 0, // Force integers
                callback: function(value) {
                     if (value >= 1000) return value/1000 + 'k';
                     return value;
                }
            }
        },
        x: {
            grid: {
                display: false,
                drawBorder: false,
            },
            ticks: {
                font: { size: 11 },
                color: '#64748b'
            }
        }
    }
};
</script>

<template>
    <div class="w-full h-full min-h-[250px]">
        <LineChartComponent :data="chartData" :options="chartOptions" />
    </div>
</template>
