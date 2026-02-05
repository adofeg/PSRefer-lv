<template>
    <div :class="['bg-white rounded-xl shadow-sm border border-slate-200 p-6', clazz]">
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
