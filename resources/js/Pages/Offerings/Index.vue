<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import { Search, Plus } from 'lucide-vue-next';

const props = defineProps({
    offerings: Object,
    auth: Object
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};
</script>

<template>
    <Head title="Catálogo de Ofertas" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Catálogo de Ofertas</h1>
                    <p class="text-slate-500">Explora productos y servicios para referir</p>
                </div>

                <div class="flex items-center gap-2">
                     <div class="relative">
                        <Search :size="20" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input type="text" placeholder="Buscar..." class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none w-full md:w-64" />
                     </div>
                     <Link v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)" :href="route('admin.offerings.index')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                        <Plus :size="20" /> Nuevo
                     </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="offering in offerings.data" :key="offering.id" class="flex flex-col h-full hover:shadow-md transition">
                   <div class="flex-1">
                       <div class="flex justify-between items-start mb-2">
                           <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold uppercase">{{ offering.category || 'General' }}</span>
                           <span v-if="offering.is_active" class="w-2 h-2 bg-green-500 rounded-full"></span>
                       </div>
                       <h3 class="font-bold text-xl text-slate-800 mb-2">{{ offering.name }}</h3>
                       <p class="text-slate-600 text-sm mb-4 line-clamp-3">{{ offering.description }}</p>

                       <div class="space-y-2 mb-4">
                           <div class="flex justify-between text-sm">
                               <span class="text-slate-500">Comisión Base:</span>
                               <span class="font-semibold text-green-600">
                                   {{ offering.commission_rate ? `${offering.commission_rate}%` : formatCurrency(offering.base_commission) }}
                               </span>
                           </div>
                            <div class="flex justify-between text-sm">
                               <span class="text-slate-500">Precio Base:</span>
                               <span class="font-medium text-slate-800">{{ offering.base_price ? formatCurrency(offering.base_price) : 'Variable' }}</span>
                           </div>
                       </div>
                   </div>

                   <div class="mt-4 pt-4 border-t border-slate-100">
                       <Link :href="route('admin.referrals.create', { offering_id: offering.id })" class="block w-full text-center bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                           Referir Ahora
                       </Link>
                   </div>
                </Card>
            </div>

            <!-- Pagination -->
            <div v-if="offerings.links.length > 3" class="flex justify-center mt-6">
                <div class="flex gap-1">
                     <Link
                        v-for="(link, i) in offerings.links"
                        :key="i"
                        :href="link.url"
                        v-html="link.label"
                        class="px-3 py-1 rounded border"
                        :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                     />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
