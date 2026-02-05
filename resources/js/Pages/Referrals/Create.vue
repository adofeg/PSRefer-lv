<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';

const props = defineProps({
    offering: Object, // Optional pre-selected offering
    offerings: Array // List for dropdown if needed
});

const form = useForm({
    offering_id: props.offering?.id || '',
    client_name: '',
    client_contact: '',
    notes: ''
});

const submit = () => {
    form.post(route('admin.referrals.store'));
};
</script>

<template>
    <Head title="Nuevo Referido" />

    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto space-y-6">
            <h1 class="text-2xl font-bold text-slate-800">Registrar Referido</h1>

            <Card>
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nombre del Cliente</label>
                        <input v-model="form.client_name" type="text" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required />
                        <div v-if="form.errors.client_name" class="text-red-500 text-xs mt-1">{{ form.errors.client_name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Contacto (Tel/Email)</label>
                        <input v-model="form.client_contact" type="text" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>

                    <div v-if="offering">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Servicio a Referir</label>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 font-medium">
                            {{ offering.name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notas Adicionales</label>
                        <textarea v-model="form.notes" rows="3" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="$inertia.visit(route('admin.offerings.index'))" class="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg">Cancelar</button>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium" :disabled="form.processing">
                            Registrar Referido
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
