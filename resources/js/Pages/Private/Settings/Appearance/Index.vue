<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Palette } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, setAppearance, options } = useAppearance();
</script>

<template>
    <Head title="Configuración - Apariencia" />

    <AppLayout>
        <div class="max-w-2xl mx-auto space-y-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <Palette :size="18" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Apariencia</h1>
                    <p class="text-sm text-slate-500">Personaliza el tema visual de tu panel.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button
                    v-for="option in options"
                    :key="option.value"
                    type="button"
                    @click="setAppearance(option.value)"
                    :class="[
                        'border rounded-2xl p-5 text-left transition shadow-sm',
                        appearance === option.value
                            ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                            : 'border-slate-200 bg-white text-slate-600 hover:border-indigo-300'
                    ]"
                >
                    <div class="text-sm font-black uppercase tracking-widest">{{ option.label }}</div>
                    <p class="text-xs mt-2 text-slate-400">{{ option.value === 'light' ? 'Fondo claro y limpio.' : 'Tema oscuro para baja luz.' }}</p>
                </button>
            </div>
        </div>
    </AppLayout>
</template>