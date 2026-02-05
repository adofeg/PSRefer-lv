<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';

const props = defineProps({
    offering: Object // If editing
});

const form = useForm({
    name: props.offering?.name || '',
    description: props.offering?.description || '',
    base_price: props.offering?.base_price || '',
    commission_rate: props.offering?.commission_rate || '',
    category: props.offering?.category || 'General',
    is_active: props.offering ? props.offering.is_active : true
});

const submit = () => {
    if (props.offering) {
        form.put(route('admin.offerings.update', props.offering.id));
    } else {
        form.post(route('admin.offerings.store'));
    }
};
</script>

<template>
    <Head :title="offering ? 'Editar Oferta' : 'Nueva Oferta'" />

    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto space-y-6">
            <h1 class="text-2xl font-bold text-slate-800">{{ offering ? 'Editar Oferta' : 'Nueva Oferta' }}</h1>

            <Card>
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nombre del Servicio/Producto</label>
                        <input v-model="form.name" type="text" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                        <textarea v-model="form.description" rows="3" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Precio Base ($)</label>
                            <input v-model="form.base_price" type="number" step="0.01" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Porcentaje Comisión (%)</label>
                            <input v-model="form.commission_rate" type="number" step="0.01" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                    </div>

                    <div>
                         <label class="flex items-center gap-2">
                             <input v-model="form.is_active" type="checkbox" class="rounded text-indigo-600 focus:ring-indigo-500" />
                             <span class="text-sm font-medium text-slate-700">Activo en Catálogo</span>
                         </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="$inertia.visit(route('admin.offerings.index'))" class="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg">Cancelar</button>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium" :disabled="form.processing">
                            {{ offering ? 'Guardar Cambios' : 'Crear Oferta' }}
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
