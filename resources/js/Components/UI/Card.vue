<template>
    <div :class="['bg-white/90 rounded-2xl shadow-sm border border-slate-200/80 p-6 transition-shadow hover:shadow-md', clazz]">
        <slot />
    </div>
</template>

<script setup>
defineProps({
    clazz: {
        type: String,
        default: ''
    }
});
</script>
