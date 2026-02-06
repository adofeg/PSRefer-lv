<script setup>
const props = defineProps({
    status: String
});

const getStatusColor = (status) => {
    switch (status?.toLowerCase()) {
        case 'prospecto': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'contactado': return 'bg-purple-100 text-purple-800 border-purple-200';
        case 'en proceso': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'cerrado': return 'bg-green-100 text-green-800 border-green-200'; // Won / Cerrado
        case 'pagado': return 'bg-emerald-100 text-emerald-900 border-emerald-200';
        case 'perdido': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};
</script>

<template>
    <span :class="['px-2.5 py-0.5 rounded-full text-xs font-semibold border', getStatusColor(status)]">
        {{ status }}
    </span>
</template>
