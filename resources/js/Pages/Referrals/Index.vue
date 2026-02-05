<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import { Search, Filter, Plus } from 'lucide-vue-next';

const props = defineProps({
    referrals: Object,
    filters: Object
});
</script>

<template>
    <Head title="Mis Referidos" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Mis Referidos</h1>
                    <p class="text-slate-500">Gestiona y rastrea el estado de tus referidos</p>
                </div>
                 <Link :href="route('offerings.index')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                    <Plus :size="20" /> Nuevo Referido
                 </Link>
            </div>

            <Card class="overflow-hidden p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-medium border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4">Servicio</th>
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Estatus</th>
                                <th class="px-6 py-4">Comisión Est.</th>
                                <th class="px-6 py-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="referral in referrals.data" :key="referral.id" class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ referral.client_name }}
                                    <div class="text-xs text-slate-400 font-normal">{{ referral.client_contact }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ referral.offering?.name || 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ new Date(referral.created_at).toLocaleDateString() }}</td>
                                <td class="px-6 py-4"><Badge :status="referral.status" /></td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ referral.revenue_generated ? `$${referral.revenue_generated}` : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="route('referrals.show', referral.id)" class="text-indigo-600 hover:text-indigo-800 font-medium">Ver Detalle</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                     <div v-if="referrals.data.length === 0" class="p-8 text-center text-slate-400">
                        No has registrado referidos aún.
                    </div>
                </div>
                 <!-- Pagination -->
                <div v-if="referrals.links.length > 3" class="flex justify-center p-4 border-t border-slate-100">
                     <div class="flex gap-1">
                         <Link
                            v-for="(link, i) in referrals.links"
                            :key="i"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1 rounded border text-xs"
                            :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                         />
                    </div>
                </div>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
