<script setup>
const props = defineProps({
    status: String
});

const getStatusColor = (status) => {
    switch (status?.toLowerCase()) {
        case 'prospecto': return 'bg-blue-100 text-blue-700';
        case 'contactado': return 'bg-purple-100 text-purple-700';
        case 'en proceso': return 'bg-yellow-100 text-yellow-700';
        case 'cerrado': return 'bg-green-100 text-green-700'; // Won / Cerrado
        case 'pagado': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'perdido': return 'bg-red-100 text-red-700';
        default: return 'bg-slate-100 text-slate-600';
    }
};
</script>

<template>
    <span :class="['px-2.5 py-0.5 rounded-full text-xs font-bold border border-transparent', getStatusColor(status)]">
        {{ status }}
    </span>
</template>
