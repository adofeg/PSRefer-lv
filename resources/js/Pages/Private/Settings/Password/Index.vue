<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Lock } from 'lucide-vue-next';

const form = useForm({
    current_password: '',
    new_password: '',
});

const submit = () => {
    form.post(route('settings.password'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Seguridad - Contraseña" />

    <AppLayout>
        <div class="max-w-xl mx-auto space-y-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <Lock :size="18" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Actualizar contraseña</h1>
                    <p class="text-sm text-slate-500">Protege tu cuenta con una nueva clave.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Contraseña actual</label>
                    <input v-model="form.current_password" type="password" class="w-full border-slate-200 rounded-xl p-3 text-sm font-semibold focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Nueva contraseña</label>
                    <input v-model="form.new_password" type="password" class="w-full border-slate-200 rounded-xl p-3 text-sm font-semibold focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : 'Actualizar' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>