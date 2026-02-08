<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import UserForm from './UserForm.vue';
import { ArrowLeft } from 'lucide-vue-next';

defineProps({
    roles: Array
});
</script>

<template>
    <Head title="Nuevo Usuario" />

    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.users.index')" class="p-2 hover:bg-slate-100 rounded-full transition">
                    <ArrowLeft :size="24" class="text-slate-500" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Nuevo Usuario</h1>
                    <p class="text-slate-500">Registrar un nuevo usuario en el sistema</p>
                </div>
            </div>

            <Card>
                <UserForm :roles="roles" @cancel="$inertia.visit(route('admin.users.index'))" />
            </Card>
        </div>
    </AppLayout>
</template>
