<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Edit2, Trash2, X, Check, Search, Tag, Filter } from 'lucide-vue-next';
import Card from '@/Components/UI/Card.vue';
import Modal from '@/Components/UI/Modal.vue';
import ConfirmModal from '@/Components/UI/ConfirmModal.vue';
import { Switch } from '@headlessui/vue';

const props = defineProps({
    categories: Object,
    filters: Object
});

const isModalOpen = ref(false);
const editingCategory = ref(null);
const searchTerm = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');

// Confirm Modals State
const confirmDeleteModal = ref(false);
const confirmToggleModal = ref(false);
const itemToDelete = ref(null);
const itemToToggle = ref(null);

const form = useForm({
    name: '',
    description: '',
    is_active: true
});

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const applyFilters = debounce(() => {
    router.get(
        route('admin.categories.index'),
        { 
            search: searchTerm.value,
            status: statusFilter.value
        },
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true 
        }
    );
}, 300);

watch([searchTerm, statusFilter], () => {
    applyFilters();
});

const clearFilters = () => {
    searchTerm.value = '';
    statusFilter.value = 'all';
};

const openCreateModal = () => {
    editingCategory.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.description = category.description;
    form.is_active = !!category.is_active;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingCategory.value) {
        form.put(route('admin.categories.update', editingCategory.value.id), {
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => closeModal()
        });
    }
};

const promptDelete = (category) => {
    itemToDelete.value = category;
    confirmDeleteModal.value = true;
};

const executeDelete = () => {
    if (!itemToDelete.value) return;

    router.delete(route('admin.categories.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            confirmDeleteModal.value = false;
            itemToDelete.value = null;
        }
    });
};

const promptToggle = (category) => {
    itemToToggle.value = category;
    confirmToggleModal.value = true;
};

const executeToggle = () => {
    if (!itemToToggle.value) return;
    
    const newState = !itemToToggle.value.is_active;

    router.post(route('admin.categories.toggle-status', itemToToggle.value.id), { is_active: newState }, {
        preserveScroll: true,
        onSuccess: () => {
            confirmToggleModal.value = false;
            itemToToggle.value = null;
        }
    });
};
</script>

<template>
    <Head title="Gestión de Categorías" />

    <AppLayout>
        <div class="w-full space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                        <Tag :size="24" class="text-indigo-600" />
                        Categorías de Ofertas
                    </h1>
                    <p class="text-slate-500 text-sm">Organiza tus servicios y productos por industria o tipo</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-indigo-700 transition shadow-sm"
                >
                    <Plus :size="20" />
                    Nueva Categoría
                </button>
            </div>

            <!-- Professional Filter Bar -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search -->
                <div class="relative w-full md:w-96">
                    <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input 
                        v-model="searchTerm"
                        type="text" 
                        placeholder="Buscar categorías..." 
                        class="pl-10 pr-4 py-2.5 w-full bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                    >
                </div>

                <!-- Filters Group -->
                <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0 justify-end">
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
                        v-if="searchTerm || statusFilter !== 'all'"
                        type="button"
                        @click="clearFilters"
                        class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition shrink-0 cursor-pointer"
                        title="Limpiar Filtros"
                    >
                        <X :size="18" />
                    </button>
                </div>
            </div>

            <!-- List -->
            <Card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-medium">
                            <tr>
                                <th class="px-6 py-4">Nombre</th>
                                <th class="px-6 py-4">Descripción</th>
                                <th class="px-6 py-4 text-center">Estatus</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="category in categories.data" :key="category.id" class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ category.name }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ category.description || 'Sin descripción' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <Switch
                                        :model-value="category.is_active"
                                        @click="promptToggle(category)"
                                        :class="category.is_active ? 'bg-green-500' : 'bg-slate-200'"
                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer"
                                    >
                                        <span class="sr-only">Cambiar estado</span>
                                        <span
                                            :class="category.is_active ? 'translate-x-5' : 'translate-x-1'"
                                            class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"
                                        />
                                    </Switch>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            @click="openEditModal(category)"
                                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                            title="Editar"
                                        >
                                            <Edit2 :size="18" />
                                        </button>
                                        <button 
                                            @click="promptDelete(category)"
                                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Eliminar"
                                        >
                                            <Trash2 :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="categories.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                                    No se encontraron categorías
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="categories.links.length > 3" class="px-6 py-4 border-t border-slate-100 flex justify-center">
                    <div class="flex gap-1">
                         <Link
                            v-for="(link, i) in categories.links"
                            :key="i"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1 rounded border text-xs font-medium transition"
                            :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                         />
                    </div>
                </div>
            </Card>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ editingCategory ? 'Editar Categoría' : 'Nueva Categoría' }}
                    </h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <X :size="24" />
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                        <input 
                            v-model="form.name"
                            type="text" 
                            class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="Ej: Marketing, Salud, Consultoría..."
                            required
                        >
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                        <textarea 
                            v-model="form.description"
                            class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            rows="3"
                            placeholder="Breve descripción de la categoría..."
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input 
                            v-model="form.is_active"
                            id="category_is_active"
                            type="checkbox" 
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <label for="category_is_active" class="text-sm font-medium text-slate-700">Categoría Activa</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8">
                        <button 
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 text-slate-700 font-medium hover:bg-slate-100 rounded-lg transition"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition flex items-center gap-2"
                            :class="{ 'opacity-70 cursor-not-allowed': form.processing }"
                        >
                            <Check v-if="!form.processing" :size="20" />
                            <span v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            {{ editingCategory ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Confirm Modals -->
        <ConfirmModal 
            :show="confirmDeleteModal"
            title="¿Eliminar Categoría?"
            :message="`Estás a punto de eliminar '${itemToDelete?.name}'. Esto podría afectar a las ofertas asociadas.`"
            confirmText="Sí, Eliminar"
            type="danger"
            @close="confirmDeleteModal = false"
            @confirm="executeDelete"
        />

        <ConfirmModal 
            :show="confirmToggleModal"
            :title="itemToToggle?.is_active ? '¿Desactivar Categoría?' : '¿Activar Categoría?'"
            :message="itemToToggle?.is_active 
                ? `Estás a punto de desactivar '${itemToToggle?.name}'. Las ofertas bajo esta categoría podrían dejar de ser visibles para los asociados.` 
                : `Estás a punto de reactivar '${itemToToggle?.name}'.`"
            :confirmText="itemToToggle?.is_active ? 'Sí, Desactivar' : 'Sí, Activar'"
            type="warning"
            @close="confirmToggleModal = false"
            @confirm="executeToggle"
        />
    </AppLayout>
</template>