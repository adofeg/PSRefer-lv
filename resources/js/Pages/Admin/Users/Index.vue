<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Users, UserPlus, Search, MoreHorizontal, DollarSign, Shield, UserX, UserCheck } from 'lucide-vue-next';
import Card from '@/Components/Card.vue';
import CommissionOverrideModal from '@/Components/Admin/CommissionOverrideModal.vue';

const props = defineProps({
    users: Object
});

const searchTerm = ref('');
const isOverrideModalOpen = ref(false);
const selectedUser = ref(null);

const openOverrideModal = (user) => {
    selectedUser.value = user;
    isOverrideModalOpen.value = true;
};

const getRoleBadgeClass = (role) => {
    switch (role) {
        case 'admin': return 'bg-purple-100 text-purple-700';
        case 'psadmin': return 'bg-indigo-100 text-indigo-700';
        case 'vendor': return 'bg-amber-100 text-amber-700';
        case 'associate': return 'bg-blue-100 text-blue-700';
        default: return 'bg-slate-100 text-slate-700';
    }
};
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                        <Users :size="24" class="text-indigo-600" />
                        Usuarios del Sistema
                    </h1>
                    <p class="text-slate-500 text-sm">Administra asociados, vendedores y administradores</p>
                </div>
                <div class="flex gap-2">
                     <div class="relative">
                        <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input 
                            v-model="searchTerm"
                            type="text" 
                            placeholder="Buscar usuarios..." 
                            class="pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64"
                        >
                    </div>
                    <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-indigo-700 transition">
                        <UserPlus :size="20" />
                        Nuevo Usuario
                    </button>
                </div>
            </div>

            <Card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-medium">
                            <tr>
                                <th class="px-6 py-4">Usuario</th>
                                <th class="px-6 py-4">Rol</th>
                                <th class="px-6 py-4">Balance</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-500">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ user.name }}</div>
                                            <div class="text-xs text-slate-500">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="getRoleBadgeClass(user.role)" class="px-2 py-0.5 rounded-full text-xs font-bold capitalize">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-slate-700">
                                    ${{ parseFloat(user.balance).toLocaleString() }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span v-if="user.is_active" class="text-green-600 flex items-center justify-center gap-1">
                                        <UserCheck :size="16" />
                                        Activo
                                    </span>
                                    <span v-else class="text-slate-400 flex items-center justify-center gap-1">
                                        <UserX :size="16" />
                                        Inactivo
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            @click="openOverrideModal(user)"
                                            class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                            title="Configurar Comisiones Propias"
                                        >
                                            <DollarSign :size="18" />
                                        </button>
                                        <button class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                            <Shield :size="18" />
                                        </button>
                                        <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition">
                                            <MoreHorizontal :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Placeholder -->
                <div v-if="users.links" class="p-4 border-t border-slate-100 flex justify-center">
                    <!-- Simple Pagination component or links -->
                </div>
            </Card>
        </div>

        <!-- Commission Override Modal -->
        <CommissionOverrideModal 
            :show="isOverrideModalOpen"
            :user="selectedUser"
            @close="isOverrideModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
