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
import { Line } from 'vue-chartjs';

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
    data: {
        type: Object, // { 1: 500, 2: 700 } (month: amount)
        default: () => ({})
    }
});

const chartData = computed(() => {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Map data to array
    const values = months.map((_, index) => {
        // Backend key is month number (1-12)
        const monthNum = index + 1;
        // Handle both string and number keys just in case
        return props.data[monthNum] || props.data[String(monthNum)] || 0;
    });

    return {
        labels: months,
        datasets: [
            {
                label: 'Revenue',
                backgroundColor: 'rgba(99, 102, 241, 0.1)', // Indigo-500 low opacity
                borderColor: '#6366f1', // Indigo-500
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#6366f1',
                pointHoverBackgroundColor: '#6366f1',
                pointHoverBorderColor: '#ffffff',
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                data: values,
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
                        label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
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
                callback: function(value) {
                     if (value >= 1000) return '$' + value/1000 + 'k';
                     return '$' + value;
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
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>
