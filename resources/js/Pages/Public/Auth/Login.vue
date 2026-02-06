<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthLayout>
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <div class="hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-indigo-600 to-purple-700 text-white">
                <div>
                    <div class="text-sm uppercase tracking-widest text-white/80">PS Refer</div>
                    <h2 class="mt-4 text-3xl font-bold">Acceso seguro para gestionar referencias</h2>
                    <p class="mt-3 text-white/90">
                        Control total para Admins y acceso de lectura para Associates con reglas claras de negocio.
                    </p>
                </div>
                <div class="text-sm text-white/80">
                    <p class="font-semibold">Reglas clave</p>
                    <ul class="mt-2 space-y-1 list-disc list-inside">
                        <li>Admins/PSAdmin gestionan catálogo y estados.</li>
                        <li>Associates solo ven servicios activos.</li>
                        <li>Conflicto de interés por categoría profesional.</li>
                    </ul>
                </div>
            </div>

            <div class="p-8 lg:p-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Bienvenido</h2>
                        <p class="text-sm text-slate-500">Inicia sesión para continuar</p>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">Login</span>
                </div>

                <div v-if="status" class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="mt-6 space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <input
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                        <input
                            id="password"
                            type="password"
                            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-sm text-slate-600">
                            <input type="checkbox" name="remember" v-model="form.remember" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                            <span class="ms-2">Recordarme</span>
                        </label>
                        <Link :href="route('password.request')" class="text-sm text-indigo-600 hover:text-indigo-700">
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Iniciar sesión
                    </button>

                    <div class="text-center text-sm text-slate-500">
                        ¿No tienes cuenta?
                        <Link href="/register" class="text-indigo-600 hover:text-indigo-700 font-medium">Regístrate</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>