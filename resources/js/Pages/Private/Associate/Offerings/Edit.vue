<script setup>
import Page from '@/Pages/Private/Admin/Offerings/Create.vue';

const props = defineProps({
    offering: Object,
    categories: Array
});
</script>

<template>
    <Page :offering="offering" :categories="categories" />
</template>