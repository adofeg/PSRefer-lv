<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ShieldCheck } from 'lucide-vue-next';

const props = defineProps({
    twoFactorEnabled: Boolean,
    requiresConfirmation: Boolean,
});
</script>

<template>
    <Head title="Configuración - Doble Factor" />

    <AppLayout>
        <div class="max-w-2xl mx-auto space-y-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <ShieldCheck :size="18" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Autenticación de dos factores</h1>
                    <p class="text-sm text-slate-500">Refuerza la seguridad de tu cuenta.</p>
                </div>
            </div>

            <div class="border border-slate-200 rounded-2xl p-6 bg-white shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Estado actual</h2>
                        <p class="text-sm text-slate-500">
                            {{ props.twoFactorEnabled ? 'Activo' : 'Inactivo' }}
                        </p>
                    </div>
                    <span
                        :class="[
                            'px-3 py-1 rounded-full text-xs font-black uppercase',
                            props.twoFactorEnabled ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'
                        ]"
                    >
                        {{ props.twoFactorEnabled ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div v-if="props.requiresConfirmation" class="text-xs text-amber-600 font-bold">
                    Tu configuración requiere confirmación al habilitar el doble factor.
                </div>
            </div>
        </div>
    </AppLayout>
</template>