<script setup>
import Modal from '@/Components/UI/Modal.vue';
import { AlertTriangle, CheckCircle, Info, XCircle } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: '¿Estás seguro?'
    },
    message: {
        type: String,
        default: 'Esta acción no se puede deshacer.'
    },
    type: {
        type: String,
        default: 'danger', // danger, success, warning, info
        validator: (value) => ['danger', 'success', 'warning', 'info'].includes(value)
    },
    confirmText: {
        type: String,
        default: 'Confirmar'
    },
    cancelText: {
        type: String,
        default: 'Cancelar'
    }
});

const emit = defineEmits(['close', 'confirm']);

const colors = {
    danger: {
        icon: 'text-red-500',
        bg: 'bg-red-50',
        button: 'bg-red-600 hover:bg-red-700',
        ring: 'focus:ring-red-500'
    },
    warning: {
        icon: 'text-amber-500',
        bg: 'bg-amber-50',
        button: 'bg-amber-600 hover:bg-amber-700',
        ring: 'focus:ring-amber-500'
    },
    success: {
        icon: 'text-green-500',
        bg: 'bg-green-50',
        button: 'bg-green-600 hover:bg-green-700',
        ring: 'focus:ring-green-500'
    },
    info: {
        icon: 'text-blue-500',
        bg: 'bg-blue-50',
        button: 'bg-blue-600 hover:bg-blue-700',
        ring: 'focus:ring-blue-500'
    }
};

const getIcon = () => {
    switch (props.type) {
        case 'danger': return AlertTriangle;
        case 'warning': return AlertTriangle;
        case 'success': return CheckCircle;
        case 'info': return Info;
        default: return Info;
    }
};
</script>

<template>
    <Modal :show="show" maxWidth="md" @close="$emit('close')">
        <div class="p-6 text-center">
            <div 
                class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4"
                :class="colors[type].bg"
            >
                <component 
                    :is="getIcon()" 
                    :size="32" 
                    :class="colors[type].icon" 
                />
            </div>
            
            <h3 class="text-lg font-bold text-slate-900 mb-2">
                {{ title }}
            </h3>
            
            <p class="text-sm text-slate-500 mb-8">
                {{ message }}
            </p>
            
            <div class="flex gap-3 justify-center">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition focus:outline-none focus:ring-2 focus:ring-slate-200"
                >
                    {{ cancelText }}
                </button>
                <button
                    @click="$emit('confirm')"
                    class="px-4 py-2 text-white rounded-lg transition shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1"
                    :class="[colors[type].button, colors[type].ring]"
                >
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </Modal>
</template>
