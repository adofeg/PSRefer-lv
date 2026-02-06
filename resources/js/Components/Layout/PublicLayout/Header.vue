<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <header class="bg-white/90 backdrop-blur border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center justify-between">
                <Link :href="route('home')" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                        PS
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-extrabold text-slate-900">PS Refer</div>
                        <div class="text-[11px] text-slate-500">Catálogo & Referencias</div>
                    </div>
                </Link>

                <nav class="flex items-center gap-3 text-sm">
                    <Link
                        v-if="user"
                        :href="route('dashboard')"
                        class="px-4 py-2 rounded-lg text-slate-700 border border-slate-200 hover:bg-slate-50"
                    >
                        Ir al Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900">
                            Iniciar sesión
                        </Link>
                        <Link :href="route('register')" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                            Crear cuenta
                        </Link>
                    </template>
                </nav>
            </div>
        </div>
    </header>
</template>
