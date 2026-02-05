<script setup>
import ReferralCard from './ReferralCard.vue';

const props = defineProps({
    status: String,
    referrals: Array,
    color: {
        type: String,
        default: 'slate'
    }
});

const emit = defineEmits(['change-status']);

const colorClasses = {
    slate: 'bg-slate-50 border-slate-200',
    blue: 'bg-blue-50 border-blue-200',
    yellow: 'bg-yellow-50 border-yellow-200',
    green: 'bg-green-50 border-green-200',
    red: 'bg-red-50 border-red-200'
};

const headerColors = {
    slate: 'text-slate-700',
    blue: 'text-blue-700',
    yellow: 'text-yellow-700',
    green: 'text-green-700',
    red: 'text-red-700'
};
</script>

<template>
    <div :class="['flex-1 min-w-[280px] rounded-xl flex flex-col h-full border', colorClasses[color]]">
        <!-- Column Header -->
        <div class="p-3 border-b rounded-t-xl flex justify-between items-center bg-white/50">
            <span :class="['font-bold', headerColors[color]]">{{ status }}</span>
            <span class="bg-white px-2 py-0.5 rounded-full text-xs font-bold shadow-sm">
                {{ referrals.length }}
            </span>
        </div>

        <!-- Cards Container -->
        <div class="p-2 space-y-2 overflow-y-auto flex-1">
            <ReferralCard 
                v-for="referral in referrals" 
                :key="referral.id"
                :referral="referral"
                @change-status="emit('change-status', $event)"
            />
            
            <!-- Empty State -->
            <div v-if="referrals.length === 0" class="text-center text-slate-400 py-8 text-sm">
                No hay referidos
            </div>
        </div>
    </div>
</template>
