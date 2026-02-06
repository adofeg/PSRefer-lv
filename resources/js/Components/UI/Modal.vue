<script setup>
import { onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    closeOnEscape: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

const close = () => emit('close');

const onKeydown = (event) => {
    if (!props.show || !props.closeOnEscape) return;
    if (event.key === 'Escape') {
        close();
    }
};

watch(
    () => props.show,
    (value) => {
        if (typeof document === 'undefined') return;
        document.body.classList.toggle('overflow-hidden', value);
    },
    { immediate: true }
);

onMounted(() => {
    if (typeof window !== 'undefined') {
        window.addEventListener('keydown', onKeydown);
    }
});

onUnmounted(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', onKeydown);
    }
    if (typeof document !== 'undefined') {
        document.body.classList.remove('overflow-hidden');
    }
});
</script>

<template>
    <teleport to="body">
        <transition name="modal-fade">
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="close"></div>
                <div class="relative z-10 w-full max-w-2xl mx-4">
                    <div class="bg-white/95 rounded-2xl shadow-2xl border border-slate-200">
                        <slot />
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
</style>
