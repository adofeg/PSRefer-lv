<script setup>
import Page from '@/Pages/Private/Admin/Offerings/Create.vue';

defineProps({
    offering: Object,
    categories: Array,
});
</script>

<template>
    <Page :offering="offering" :categories="categories" />
</template>
