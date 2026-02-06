<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    category: '',
    offering_id: new URLSearchParams(window.location.search).get('offering') || '',
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <AuthLayout>
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <div class="hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-indigo-600 to-purple-700 text-white">
                <div>
                    <div class="text-sm uppercase tracking-widest text-white/80">PS Refer</div>
                    <h2 class="mt-4 text-3xl font-bold">Registro verificado y transparente</h2>
                    <p class="mt-3 text-white/90">
                        Completa tu perfil para acceder a un catálogo curado y reglas claras de referidos.
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
                        <h2 class="text-2xl font-bold text-slate-900">Crear cuenta</h2>
                        <p class="text-sm text-slate-500">Datos esenciales para validar tu acceso</p>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">Registro</span>
                </div>

                <form @submit.prevent="submit" class="mt-6 space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">Nombre completo</label>
                        <input id="name" type="text" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.name" required autofocus autocomplete="name" />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" type="email" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.email" required autocomplete="username" />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700">Teléfono</label>
                        <input id="phone" type="text" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.phone" autocomplete="tel" />
                        <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">{{ form.errors.phone }}</div>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700">Categoría profesional (opcional)</label>
                        <input id="category" type="text" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.category" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Contraseña</label>
                        <input id="password" type="password" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.password" required autocomplete="new-password" />
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirmar contraseña</label>
                        <input id="password_confirmation" type="password" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.password_confirmation" required autocomplete="new-password" />
                        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-1">{{ form.errors.password_confirmation }}</div>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Crear cuenta
                    </button>

                    <div class="text-center text-sm text-slate-500">
                        ¿Ya tienes cuenta?
                        <Link href="/login" class="text-indigo-600 hover:text-indigo-700 font-medium">Inicia sesión</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>