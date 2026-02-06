<script setup>
import PublicHeader from '@/Components/Layout/PublicLayout/Header.vue';
import PublicFooter from '@/Components/Layout/PublicLayout/Footer.vue';
</script>

<template>
    <div class="min-h-screen flex flex-col bg-slate-50">
        <PublicHeader />
        <main class="flex-1">
            <slot />
        </main>
        <PublicFooter />
    </div>
</template>
