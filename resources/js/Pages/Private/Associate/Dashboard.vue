<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Panel de Asociado" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 font-bold text-xl">
                        Bienvenido al Portal de Asociados de PSRefer.
                    </div>
                    <div class="p-6 text-gray-600">
                        Desde aquí podrás gestionar tus comisiones, ver tu red y acceder al centro de marketing.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
