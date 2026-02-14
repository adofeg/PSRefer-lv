<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

</script>

<template>
    <Head title="PS Refer" />
    <PublicLayout>
        <div class="bg-gradient-to-b from-indigo-50 via-white to-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                            Catálogo profesional de servicios
                        </span>
                        <h1 class="mt-4 text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight">
                            PS Refer
                            <span class="block text-indigo-600">Referencias con reglas claras</span>
                        </h1>
                        <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                            Plataforma robusta para administrar servicios, referencias y comisiones con control por roles.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center justify-center px-5 py-3 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition"
                            >
                                Iniciar sesión
                            </Link>
                            <Link
                                :href="route('register')"
                                class="inline-flex items-center justify-center px-5 py-3 rounded-lg border border-indigo-200 text-indigo-700 font-semibold hover:bg-indigo-50 transition"
                            >
                                Crear cuenta
                            </Link>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-4 text-sm text-slate-500">
                            <div class="bg-white border border-slate-200 rounded-lg p-4">
                                <p class="font-semibold text-slate-800">Admins/PSAdmin</p>
                                <p>Control total del catálogo y estados.</p>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-lg p-4">
                                <p class="font-semibold text-slate-800">Associates</p>
                                <p>Acceso a servicios activos y referidos.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-6">
                        <h2 class="text-lg font-bold text-slate-900">Servicios destacados</h2>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="border border-slate-200 rounded-lg p-4">
                                <p class="font-semibold text-slate-800">Seguros de Salud</p>
                                <p class="text-slate-500">Cobertura integral para individuos y familias.</p>
                                <p class="mt-2 text-xs text-indigo-600 font-semibold">Comisión mensual 30%</p>
                            </div>
                            <div class="border border-slate-200 rounded-lg p-4">
                                <p class="font-semibold text-slate-800">Taxes Corporativos</p>
                                <p class="text-slate-500">Servicios fiscales para negocios.</p>
                                <p class="mt-2 text-xs text-indigo-600 font-semibold">$50 comisión única</p>
                            </div>
                            <div class="border border-slate-200 rounded-lg p-4">
                                <p class="font-semibold text-slate-800">Group Insurance</p>
                                <p class="text-slate-500">Seguros colectivos 5+ empleados.</p>
                                <p class="mt-2 text-xs text-indigo-600 font-semibold">$50 comisión única</p>
                            </div>
                            <div class="border border-slate-200 rounded-lg p-4">
                                <p class="font-semibold text-slate-800">Business Liability</p>
                                <p class="text-slate-500">Protección para negocios.</p>
                                <p class="mt-2 text-xs text-indigo-600 font-semibold">10% comisión única</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 pb-16">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white border border-slate-200 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-slate-900">Reglas por Rol</h3>
                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                            <li>Admins/PSAdmin pueden crear, editar y desactivar servicios.</li>
                            <li>Associates ven solo servicios activos.</li>
                            <li>Admins actualizan estados y valores de ventas.</li>
                        </ul>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-slate-900">Conflicto de Interés</h3>
                        <p class="mt-3 text-sm text-slate-600">
                            Un asociado no puede referir servicios de su misma categoría profesional.
                        </p>
                        <p class="mt-2 text-xs text-slate-500">Ej: Agente de Seguros no verá “Seguros”.</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-slate-900">Creación de Referencias</h3>
                        <p class="mt-3 text-sm text-slate-600">
                            Requiere servicio válido y nombre del cliente. Estados solo por Admins.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.1)'/%3E%3C/svg%3E");
    }
}
</style>
