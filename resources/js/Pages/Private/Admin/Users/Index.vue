<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { Users, UserPlus, Search, DollarSign, Pencil, UserX, UserCheck, Filter, X } from 'lucide-vue-next';
import { Switch } from '@headlessui/vue';
import Card from '@/Components/UI/Card.vue';
import CommissionOverrideModal from '@/Components/Admin/CommissionOverrideModal.vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue';
import { normalizePaginated } from '@/Utils/inertia';

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Array
});

const usersResource = computed(() => normalizePaginated(props.users));

const searchTerm = ref(props.filters?.search || '');
const roleFilter = ref(props.filters?.role || '');
const statusFilter = ref(props.filters?.status || 'all');

const isOverrideModalOpen = ref(false);
const confirmDeleteModal = ref(false);
const confirmToggleModal = ref(false);
const selectedUser = ref(null);
const userToDelete = ref(null);
const userToToggle = ref(null);

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const applyFilters = debounce(() => {
    router.get(
        route('admin.users.index'),
        { 
            search: searchTerm.value,
            role: roleFilter.value,
            status: statusFilter.value
        },
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true 
        }
    );
}, 300);

watch([searchTerm, roleFilter, statusFilter], () => {
    applyFilters();
});

const clearFilters = () => {
    searchTerm.value = '';
    roleFilter.value = '';
    statusFilter.value = 'all';
};

const openOverrideModal = (user) => {
    selectedUser.value = user;
    isOverrideModalOpen.value = true;
};

const promptDelete = (user) => {
    userToDelete.value = user;
    confirmDeleteModal.value = true;
};

const executeDelete = () => {
    if (!userToDelete.value) return;

    router.delete(route('admin.users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            confirmDeleteModal.value = false;
            userToDelete.value = null;
        }
    });
};

const promptToggle = (user) => {
    userToToggle.value = user;
    confirmToggleModal.value = true;
};

const executeToggle = () => {
    if (!userToToggle.value) return;
    
    // We want to toggle to the OPPOSITE of current state
    const newState = !userToToggle.value.is_active;

    router.post(route('admin.users.toggle-status', userToToggle.value.id), { is_active: newState }, {
        preserveScroll: true,
        onSuccess: () => {
            confirmToggleModal.value = false;
            userToToggle.value = null;
        },
        onFinish: () => {
            confirmToggleModal.value = false;
        }
    });
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

    <AppLayout>
        <div class="w-full space-y-6">
            <!-- Header & Actions -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                        <Users :size="24" class="text-indigo-600" />
                        Usuarios del Sistema
                    </h1>
                    <p class="text-slate-500 text-sm">Administra asociados, vendedores y administradores</p>
                </div>
                <Link 
                    v-if="$page.props.auth.user.role !== 'psadmin'"
                    :href="route('admin.users.create')" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-indigo-700 transition shadow-sm hover:shadow-md"
                >
                    <UserPlus :size="20" />
                    Nuevo Usuario
                </Link>
            </div>

            <!-- Professional Filter Bar -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search -->
                <div class="relative w-full md:w-96">
                    <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input 
                        v-model="searchTerm"
                        type="text" 
                        placeholder="Buscar por nombre o email..." 
                        class="pl-10 pr-4 py-2.5 w-full bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                    >
                </div>

                <!-- Filters Group -->
                <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
                    <!-- Role Filter -->
                    <div class="relative min-w-[140px]">
                        <select 
                            v-model="roleFilter" 
                            class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 pr-8 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm cursor-pointer"
                        >
                            <option value="">Todos los Roles</option>
                            <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                        </select>
                         <Filter :size="14" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    </div>

                    <!-- Status Filter -->
                    <div class="relative min-w-[140px]">
                        <select 
                            v-model="statusFilter" 
                            class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 pr-8 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm cursor-pointer"
                        >
                            <option value="all">Todos los Estados</option>
                            <option value="true">Activos</option>
                            <option value="false">Inactivos</option>
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                            <span v-if="statusFilter === 'true'" class="w-2 h-2 rounded-full bg-green-500 block"></span>
                            <span v-else-if="statusFilter === 'false'" class="w-2 h-2 rounded-full bg-slate-300 block"></span>
                            <Filter v-else :size="14" class="text-slate-400" />
                        </div>
                    </div>

                    <!-- Reset -->
                     <button 
                        v-if="searchTerm || roleFilter || statusFilter !== 'all'"
                        @click="clearFilters"
                        class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
                        title="Limpiar Filtros"
                    >
                        <X :size="18" />
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
                            <tr v-for="user in usersResource.data" :key="user.id" class="hover:bg-slate-50 transition">
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
                                    <span v-if="['admin', 'psadmin'].includes(user.role)" class="text-slate-400 text-xs font-normal">N/A</span>
                                    <span v-else>${{ parseFloat(user.balance || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <Switch
                                        :model-value="user.is_active"
                                        @click="promptToggle(user)"
                                        :class="user.is_active ? 'bg-green-500' : 'bg-slate-200'"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer"
                                    >
                                        <span class="sr-only">Cambiar estado</span>
                                        <span
                                            :class="user.is_active ? 'translate-x-6' : 'translate-x-1'"
                                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                        />
                                    </Switch>
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
                                        <Link 
                                            v-if="$page.props.auth.user.role !== 'psadmin'"
                                            :href="route('admin.users.edit', user.id)"
                                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                            title="Editar Usuario"
                                        >
                                            <Pencil :size="18" />
                                        </Link>
                                        <button
                                            @click="promptDelete(user)"
                                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Eliminar Usuario"
                                        >
                                            <UserX :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Placeholder -->
                <div v-if="usersResource.links.length > 0" class="p-4 border-t border-slate-100 flex justify-center">
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

        <!-- Confirm Delete Modal -->
         <ConfirmModal 
            :show="confirmDeleteModal"
            title="¿Eliminar Usuario?"
            :message="`Estás a punto de eliminar a ${userToDelete?.name}. Esta acción no se puede deshacer.`"
            confirmText="Sí, Eliminar"
            type="danger"
            @close="confirmDeleteModal = false"
            @confirm="executeDelete"
        />

        <!-- Confirm Status Toggle Modal -->
        <ConfirmModal 
            :show="confirmToggleModal"
            :title="userToToggle?.is_active ? '¿Desactivar Usuario?' : '¿Activar Usuario?'"
            :message="userToToggle?.is_active 
                ? `Estás a punto de desactivar el acceso al sistema para ${userToToggle?.name}.` 
                : `Estás a punto de reactivar el acceso para ${userToToggle?.name}.`"
            :confirmText="userToToggle?.is_active ? 'Sí, Desactivar' : 'Sí, Activar'"
            type="warning"
            @close="confirmToggleModal = false"
            @confirm="executeToggle"
        />
    </AppLayout>
</template>
