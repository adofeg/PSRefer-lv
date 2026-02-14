<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <AuthLayout>
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <div class="hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-indigo-600 to-purple-700 text-white">
                <div>
                    <div class="text-sm uppercase tracking-widest text-white/80">PS Refer</div>
                    <h2 class="mt-4 text-3xl font-bold">Recupera tu acceso en minutos</h2>
                    <p class="mt-3 text-white/90">
                        Te enviaremos un enlace seguro para restablecer tu contraseña.
                    </p>
                </div>
                <div class="text-sm text-white/80">
                    <p class="font-semibold">Buenas prácticas</p>
                    <ul class="mt-2 space-y-1 list-disc list-inside">
                        <li>Usa un email de trabajo válido.</li>
                        <li>No compartas tu enlace de restablecimiento.</li>
                        <li>Configura una contraseña fuerte.</li>
                    </ul>
                </div>
            </div>

            <div class="p-8 lg:p-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">¿Olvidaste tu contraseña?</h2>
                        <p class="text-sm text-slate-500">Te enviamos un enlace seguro al email registrado</p>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">Recuperar</span>
                </div>

                <div v-if="status" class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="mt-6 space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" type="email" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.email" required autofocus autocomplete="username" />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Enviar enlace de recuperación
                    </button>

                    <div class="text-center text-sm text-slate-500">
                        ¿Recordaste tu contraseña?
                        <Link :href="route('login')" class="text-indigo-600 hover:text-indigo-700 font-medium">Volver a iniciar sesión</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>
