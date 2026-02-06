<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <Head title="Verify Email" />

    <AuthLayout>
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <div class="hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-indigo-600 to-purple-700 text-white">
                <div>
                    <div class="text-sm uppercase tracking-widest text-white/80">PS Refer</div>
                    <h2 class="mt-4 text-3xl font-bold">Verifica tu correo para continuar</h2>
                    <p class="mt-3 text-white/90">
                        Solo toma unos segundos y protege tu cuenta.
                    </p>
                </div>
                <div class="text-sm text-white/80">
                    <p class="font-semibold">¿No encuentras el email?</p>
                    <ul class="mt-2 space-y-1 list-disc list-inside">
                        <li>Revisa spam o promociones.</li>
                        <li>Confirma que el email sea correcto.</li>
                        <li>Puedes reenviar el enlace.</li>
                    </ul>
                </div>
            </div>

            <div class="p-8 lg:p-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Verificación de email</h2>
                        <p class="text-sm text-slate-500">Te enviamos un enlace para activar tu cuenta</p>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">Verificación</span>
                </div>

                <p class="mt-5 text-sm text-slate-600">
                    Revisa tu bandeja de entrada y haz clic en el enlace de verificación. Si no lo encuentras, puedes solicitar uno nuevo.
                </p>

                <div v-if="status === 'verification-link-sent'" class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    Enlace de verificación enviado.
                </div>

                <form @submit.prevent="submit" class="mt-6 space-y-4">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Reenviar enlace
                    </button>
                    <Link href="/logout" method="post" as="button" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Cerrar sesión
                    </Link>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>