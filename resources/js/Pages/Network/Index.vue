<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Network as NetworkIcon, Users, TrendingUp, Link as LinkIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    isAdmin: Boolean,
    members: Array,
    inviteLink: String,
    networkEarnings: Number
});

const copied = ref(false);

const copyLink = () => {
    navigator.clipboard.writeText(props.inviteLink);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount || 0);
};
</script>

<template>
    <Head title="Mi Red" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">{{ isAdmin ? 'Red Global' : 'Mi Red de Referidos' }}</h2>
                <p class="text-slate-600">Administra y visualiza tu equipo de Associates</p>
            </div>

            <!-- Dashboard View -->
            <div class="space-y-6">
                <!-- Invite Link Card -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center gap-3 mb-4">
                        <LinkIcon :size="24" />
                        <h3 class="text-lg font-semibold">Tu Link de Invitación</h3>
                    </div>
                    <div class="flex gap-3">
                        <input 
                            type="text" 
                            :value="inviteLink" 
                            readonly
                            class="flex-1 px-4 py-2 rounded-lg text-slate-800 bg-white/90"
                        >
                        <button 
                            @click="copyLink"
                            class="px-6 py-2 bg-white text-indigo-600 font-medium rounded-lg hover:bg-white/90 transition"
                        >
                            {{ copied ? '¡Copiado!' : 'Copiar' }}
                        </button>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <Users :size="24" class="text-blue-600" />
                            </div>
                            <div>
                                <p class="text-sm text-slate-600">Total Members</p>
                                <p class="text-2xl font-bold text-slate-800">{{ members.length }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-green-100 rounded-lg">
                                <TrendingUp :size="24" class="text-green-600" />
                            </div>
                            <div>
                                <p class="text-sm text-slate-600">Ventas de Red</p>
                                <p class="text-2xl font-bold text-slate-800">
                                    {{ formatCurrency(members.reduce((sum, m) => sum + (parseFloat(m.total_sales) || 0), 0)) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <TrendingUp :size="24" class="text-purple-600" />
                            </div>
                            <div>
                                <p class="text-sm text-slate-600">Mis Ganancias (Network)</p>
                                <p class="text-2xl font-bold text-slate-800">
                                    {{ formatCurrency(networkEarnings) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Members Table -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <NetworkIcon :size="24" class="text-indigo-600" />
                            <h3 class="text-lg font-semibold">Miembros de Mi Red</h3>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Fecha de Unión
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Ventas Totales
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <tr v-for="member in members" :key="member.email" class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-900">{{ member.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ member.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ formatDate(member.joined_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-slate-900">
                                        {{ formatCurrency(member.total_sales) }}
                                    </td>
                                </tr>

                                <tr v-if="members.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        Aún no tienes miembros en tu red. ¡Comparte tu link de invitación!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
