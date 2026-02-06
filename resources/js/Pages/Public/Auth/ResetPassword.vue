<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <AuthLayout>
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <div class="hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-indigo-600 to-purple-700 text-white">
                <div>
                    <div class="text-sm uppercase tracking-widest text-white/80">PS Refer</div>
                    <h2 class="mt-4 text-3xl font-bold">Crea una nueva contraseña</h2>
                    <p class="mt-3 text-white/90">
                        Asegura tu cuenta con una contraseña fuerte y única.
                    </p>
                </div>
                <div class="text-sm text-white/80">
                    <p class="font-semibold">Recomendaciones</p>
                    <ul class="mt-2 space-y-1 list-disc list-inside">
                        <li>Usa al menos 8 caracteres.</li>
                        <li>Combina letras, números y símbolos.</li>
                        <li>No reutilices contraseñas anteriores.</li>
                    </ul>
                </div>
            </div>

            <div class="p-8 lg:p-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Restablecer contraseña</h2>
                        <p class="text-sm text-slate-500">Confirma tu email y define la nueva clave</p>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">Restablecer</span>
                </div>

                <form @submit.prevent="submit" class="mt-6 space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" type="email" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.email" required autofocus autocomplete="username" />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Nueva contraseña</label>
                        <input id="password" type="password" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.password" required autocomplete="new-password" />
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirmar contraseña</label>
                        <input id="password_confirmation" type="password" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="form.password_confirmation" required autocomplete="new-password" />
                        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-1">{{ form.errors.password_confirmation }}</div>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Guardar nueva contraseña
                    </button>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>