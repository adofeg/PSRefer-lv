<script setup>
import Page from '@/Pages/Private/Admin/Offerings/Create.vue';

const props = defineProps({
    offering: Object,
    categories: Array,
    commissionable_roles: Array,
    all_associates: Array,
});
</script>

<template>
    <Page 
        :offering="offering" 
        :categories="categories" 
        :commissionable_roles="commissionable_roles"
        :all_associates="all_associates"
    />
</template>