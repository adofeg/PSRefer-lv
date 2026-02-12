<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle } from 'lucide-vue-next';

defineProps({
    offering: Object,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};
</script>

<template>
    <Head :title="offering.name" />

    <AppLayout>
        <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <Link :href="route('associate.offerings.index')" class="flex items-center text-gray-500 hover:text-gray-700 transition">
                    <ArrowLeft class="w-5 h-5 mr-2" />
                    Volver al Catálogo
                </Link>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Header / Banner -->
                <div class="bg-slate-50 px-8 py-8 border-b border-slate-100">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                        <div>
                             <span class="inline-block bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-bold tracking-wide uppercase mb-3">
                                {{ offering.category?.name || 'Servicio' }}
                            </span>
                            <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ offering.name }}</h1>
                            <p class="text-lg text-slate-600 max-w-2xl">{{ offering.description }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm min-w-[200px] text-center">
                            <p class="text-sm text-slate-500 uppercase font-bold tracking-wider mb-1">Tu Comisión</p>
                            <p class="text-3xl font-extrabold text-green-600">
                                {{ offering.commission_rate ? `${offering.commission_rate}%` : formatCurrency(offering.base_commission) }}
                            </p>
                            <p v-if="offering.base_price" class="text-xs text-slate-400 mt-2">
                                Ref: {{ formatCurrency(offering.base_price) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Details -->
                    <div class="md:col-span-2 space-y-8">
                        <section>
                            <h3 class="text-lg font-bold text-slate-900 mb-4">Detalles del Servicio</h3>
                            <div class="prose prose-slate max-w-none text-slate-600">
                                <!-- Placeholder for extended details if existing in metadata -->
                                <p>{{ offering.details || 'No hay detalles adicionales disponibles para esta oferta.' }}</p>
                            </div>
                        </section>

                        <section>
                            <h3 class="text-lg font-bold text-slate-900 mb-4">¿Cómo funciona?</h3>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <CheckCircle class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" />
                                    <span class="text-slate-600">Refiere clientes interesados en este servicio.</span>
                                </li>
                                <li class="flex items-start">
                                    <CheckCircle class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" />
                                    <span class="text-slate-600">Nuestros expertos cerrarán la venta.</span>
                                </li>
                                <li class="flex items-start">
                                    <CheckCircle class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" />
                                    <span class="text-slate-600">Recibe tu comisión automáticamente.</span>
                                </li>
                            </ul>
                        </section>
                    </div>

                    <!-- Action Sidebar -->
                    <div class="md:col-span-1">
                        <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-100">
                            <h3 class="font-bold text-indigo-900 mb-2">¿Listo para referir?</h3>
                            <p class="text-sm text-indigo-700 mb-6">Comienza ahora y aumenta tus ingresos.</p>
                            
                            <!-- Placeholder for Refer Button Action -->
                            <Link 
                                :href="route('associate.referrals.create', { offering_id: offering.id })"
                                class="block w-full text-center bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-200"
                            >
                                Crear Referido
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
