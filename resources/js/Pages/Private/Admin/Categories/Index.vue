<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Edit2, Trash2, X, Check, Search, Tag } from 'lucide-vue-next';
import Card from '@/Components/UI/Card.vue';
import Modal from '@/Components/UI/Modal.vue';

const props = defineProps({
    categories: Array
});

const isModalOpen = ref(false);
const editingCategory = ref(null);
const searchTerm = ref('');

const form = useForm({
    name: '',
    description: '',
    is_active: true
});

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

const deleteCategory = (category) => {
    if (confirm('¿Estás seguro de eliminar esta categoría?')) {
        form.delete(route('admin.categories.destroy', category.id));
    }
};

const filteredCategories = computed(() => {
    if (!searchTerm.value) return props.categories;
    const term = searchTerm.value.toLowerCase();
    return props.categories.filter(c => 
        c.name.toLowerCase().includes(term) || 
        c.description?.toLowerCase().includes(term)
    );
});
</script>

<script>
import { computed } from 'vue';
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
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-indigo-700 transition"
                >
                    <Plus :size="20" />
                    Nueva Categoría
                </button>
            </div>

            <!-- List -->
            <Card>
                <div class="p-4 border-b border-slate-100">
                    <div class="relative max-w-sm">
                        <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input 
                            v-model="searchTerm"
                            type="text" 
                            placeholder="Buscar categorías..." 
                            class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                </div>

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
                            <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ category.name }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ category.description || 'Sin descripción' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span 
                                        :class="category.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'"
                                        class="px-2 py-1 rounded text-xs font-bold"
                                    >
                                        {{ category.is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
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
                                            @click="deleteCategory(category)"
                                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Eliminar"
                                        >
                                            <Trash2 :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredCategories.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                                    No se encontraron categorías
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                        >
                            <Check v-if="!form.processing" :size="20" />
                            <span v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            {{ editingCategory ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>