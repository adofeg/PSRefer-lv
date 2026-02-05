<script setup>
import { computed } from 'vue';
import { Clock, User, MessageSquare } from 'lucide-vue-next';

const props = defineProps({
    history: {
        type: Array,
        default: () => []
    }
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};

const getStatusColor = (status) => {
    const colors = {
        'Prospecto': 'bg-slate-100 text-slate-600',
        'Contactado': 'bg-blue-100 text-blue-600',
        'En Proceso': 'bg-yellow-100 text-yellow-600',
        'Cerrado': 'bg-green-100 text-green-600',
        'Perdido': 'bg-red-100 text-red-600'
    };
    return colors[status] || 'bg-slate-100 text-slate-600';
};
</script>

<template>
    <div class="space-y-4">
        <div v-if="history.length === 0" class="text-center text-slate-400 py-8">
            <MessageSquare :size="48" class="mx-auto mb-2 opacity-30" />
            <p>No hay historial registrado aún.</p>
        </div>

        <div v-for="(entry, index) in history" :key="entry.id || index" class="relative pl-8 pb-6">
            <!-- Timeline Line -->
            <div v-if="index < history.length - 1" class="absolute left-[11px] top-6 bottom-0 w-0.5 bg-slate-200"></div>
            
            <!-- Timeline Dot -->
            <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-indigo-100 border-2 border-indigo-600 flex items-center justify-center">
                <div class="w-2 h-2 rounded-full bg-indigo-600"></div>
            </div>

            <!-- Content -->
            <div class="space-y-2">
                <!-- Timestamp -->
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <Clock :size="14" />
                    {{ formatDate(entry.created_at) }}
                </div>

                <!-- Status Change -->
                <div class="flex items-center gap-2 text-sm font-medium">
                    <span :class="['px-2 py-0.5 rounded text-xs', getStatusColor(entry.old_value || 'Inicio')]">
                        {{ entry.old_value || 'Inicio' }}
                    </span>
                    <span class="text-slate-400">→</span>
                    <span :class="['px-2 py-0.5 rounded text-xs', getStatusColor(entry.new_value)]">
                        {{ entry.new_value }}
                    </span>
                </div>

                <!-- Comment/Note -->
                <div v-if="entry.metadata?.notes || entry.metadata?.note" class="mt-2 text-sm text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <MessageSquare :size="14" class="inline mr-1 text-slate-400" />
                    {{ entry.metadata?.notes || entry.metadata?.note }}
                </div>

                <!-- User Info -->
                <div class="flex items-center gap-1 text-xs text-slate-400">
                    <User :size="12" />
                    <span>{{ entry.user?.name || 'Sistema' }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
