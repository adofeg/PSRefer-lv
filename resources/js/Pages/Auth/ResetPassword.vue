<script setup>
import { useForm, Head } from '@inertiajs/vue3';

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

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-6">Set New Password</h2>

            <form @submit.prevent="submit">
                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                    <input id="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" v-model="form.email" required autofocus autocomplete="username" />
                    <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="mt-4">
                    <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                    <input id="password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" v-model="form.password" required autocomplete="new-password" />
                    <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
                </div>

                <div class="mt-4">
                    <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" v-model="form.password_confirmation" required autocomplete="new-password" />
                    <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-1">{{ form.errors.password_confirmation }}</div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
