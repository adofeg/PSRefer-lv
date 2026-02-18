<script setup>
import { Link } from '@inertiajs/vue3';
import { DollarSign, User, Calendar } from 'lucide-vue-next';

const props = defineProps({
    referral: Object,
    showMoveButton: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['change-status']);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount || 0);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short'
    });
};
</script>

<template>
    <div class="bg-white/90 p-3 rounded-xl shadow-sm border border-slate-200/80 hover:shadow-md transition group">
        <!-- Header -->
        <div class="flex justify-between items-start mb-2">
            <span class="text-xs font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                {{ referral.offering?.name || 'General' }}
            </span>
            <span v-if="referral.revenue_generated" class="text-xs font-bold text-green-600">
                {{ formatCurrency(referral.revenue_generated) }}
            </span>
        </div>

        <!-- Client Info -->
        <h4 class="font-bold text-slate-800 mb-1">{{ referral.client_name }}</h4>
        <p class="text-xs text-slate-500 mb-2 flex items-center gap-1">
            <User :size="12" />
            {{ referral.client_contact || 'Sin contacto' }}
        </p>

        <!-- Associate -->
        <div class="flex items-center gap-1 text-xs text-indigo-700 mb-2">
            <span class="text-slate-400 text-[10px] uppercase font-bold mr-1">Referido por:</span>
            <span class="bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 font-medium">
                {{ referral.user?.name || 'Sin asignar' }}
            </span>
        </div>

        <!-- Footer with Actions -->
        <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between items-center">
            <div class="flex items-center gap-1 text-xs text-slate-400">
                <Calendar :size="12" />
                {{ formatDate(referral.created_at) }}
            </div>
            
            <div class="flex gap-2">
                <Link 
                    :href="route('admin.referrals.show', referral.id)" 
                    class="text-xs text-indigo-600 hover:underline"
                >
                    Ver
                </Link>
                <button 
                    v-if="showMoveButton"
                    @click="emit('change-status', referral)"
                    class="text-xs text-slate-500 hover:text-indigo-600 hover:underline"
                >
                    Mover...
                </button>
            </div>
        </div>
    </div>
</template>
