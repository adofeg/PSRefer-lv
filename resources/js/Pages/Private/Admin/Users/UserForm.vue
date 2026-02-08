<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    user: Object,
    roles: Array,
});

const isEdit = !!props.user;

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    phone: props.user?.phone || '',
    role: props.user?.role || 'associate', // Default to associate
    category: props.user?.category || '',
    is_active: props.user?.is_active ?? true,
});

const submit = () => {
    if (isEdit) {
        form.put(route('admin.users.update', props.user.id));
    } else {
        form.post(route('admin.users.store'));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nombre Completo</label>
                <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required />
                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Correo Electrónico</label>
                <input v-model="form.email" type="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required />
                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Rol</label>
                <select v-model="form.role" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none capitalize">
                    <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                </select>
                <div v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    {{ isEdit ? 'Nueva Contraseña (Opcional)' : 'Contraseña' }}
                </label>
                <input v-model="form.password" type="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" :required="!isEdit" />
                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono</label>
                <input v-model="form.phone" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" />
                 <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
            </div>
            
            <!-- Category (Associate Only) -->
            <div v-if="form.role === 'associate'">
                <label class="block text-sm font-medium text-slate-700 mb-1">Categoría Profesional</label>
                <input v-model="form.category" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Ej: Realtor, Abogado" />
                 <div v-if="form.errors.category" class="text-red-500 text-xs mt-1">{{ form.errors.category }}</div>
            </div>

             <!-- Status -->
             <div class="flex items-center gap-2">
                <input v-model="form.is_active" type="checkbox" id="is_active" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500" />
                <label for="is_active" class="text-sm font-medium text-slate-700">Usuario Activo</label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="button" @click="$emit('cancel')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">
                Cancelar
            </button>
            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                {{ isEdit ? 'Guardar Cambios' : 'Crear Usuario' }}
            </button>
        </div>
    </form>
</template>
