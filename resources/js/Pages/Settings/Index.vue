<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { User, DollarSign, FileCheck, Globe, Camera, Lock, Save } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    categories: Array
});

const form = useForm({
    name: props.user.name || '',
    w9_status: props.user.w9_status || 'pending',
    payment_info: props.user.payment_info || { details: '' },
    preferred_currency: props.user.preferred_currency || 'USD',
    category: props.user.category || '',
    phone: props.user.phone || '',
    logo_file: null,
    _method: 'PUT' // For file upload with PUT
});

const passwordForm = useForm({
    current_password: '',
    new_password: '',
});

const logoPreview = ref(props.user.logo_url || null);

const onLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo_file = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            alert('Perfil actualizado con éxito');
        }
    });
};

const updatePassword = () => {
    passwordForm.post(route('settings.password'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            alert('Contraseña actualizada con éxito');
        }
    });
};

const currencies = [
    { value: 'USD', label: 'USD - Dólar' },
    { value: 'MXN', label: 'MXN - Peso Mexicano' },
    { value: 'COP', label: 'COP - Peso Colombiano' }
];
</script>

<template>
    <Head title="Configuración" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto space-y-8 animate-fade-in">
            <div class="mb-2">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Configuración</h2>
                <p class="text-slate-500 font-medium">Administra tu perfil, logos y seguridad</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile & Logo Sidebar -->
                <div class="space-y-6">
                    <Card class="text-center overflow-hidden pt-10 pb-6 group">
                        <div class="relative w-32 h-32 mx-auto mb-4 rounded-2xl overflow-hidden bg-slate-100 ring-4 ring-white shadow-lg">
                            <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                                <User :size="48" />
                            </div>
                            <label class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                <Camera class="text-white" :size="24" />
                                <input type="file" @change="onLogoChange" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" />
                            </label>
                        </div>
                        <h3 class="font-black text-slate-800 uppercase tracking-wide px-4">{{ user.name }}</h3>
                        <p class="text-xs text-slate-400 font-bold px-4 truncate">{{ user.email }}</p>
                        <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase">
                            {{ user.role }}
                        </div>
                    </Card>

                    <Card class="p-6 bg-slate-900 text-white border-none shadow-xl">
                        <div class="flex items-center gap-3 mb-4">
                            <Lock :size="20" class="text-indigo-400" />
                            <h3 class="font-black uppercase text-sm tracking-widest">Seguridad</h3>
                        </div>
                        <form @submit.prevent="updatePassword" class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Contraseña Actual</label>
                                <input v-model="passwordForm.current_password" type="password" class="w-full bg-white/5 border-white/10 rounded-xl p-2.5 text-sm focus:border-indigo-500 transition" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Nueva Contraseña</label>
                                <input v-model="passwordForm.new_password" type="password" class="w-full bg-white/5 border-white/10 rounded-xl p-2.5 text-sm focus:border-indigo-500 transition" />
                            </div>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition shadow-lg shadow-indigo-900/50" :disabled="passwordForm.processing">
                                {{ passwordForm.processing ? 'ACTUALIZANDO...' : 'CAMBIAR CLAVE' }}
                            </button>
                        </form>
                    </Card>
                </div>

                <!-- Main Settings Form -->
                <div class="lg:col-span-2 space-y-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <Card class="p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                                    <User :size="20" />
                                </div>
                                <h3 class="text-lg font-black uppercase tracking-wide text-slate-700">Perfil del Asociado</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-xs font-black uppercase text-slate-400 mb-1">Nombre Completo / Empresa</label>
                                    <input v-model="form.name" type="text" class="w-full border-slate-200 rounded-xl p-3 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase text-slate-400 mb-1">Teléfono Público</label>
                                    <input v-model="form.phone" type="text" class="w-full border-slate-200 rounded-xl p-3 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500" placeholder="+1 (000) 000-0000" />
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase text-slate-400 mb-1">Categoría Profesional</label>
                                    <select v-model="form.category" class="w-full border-slate-200 rounded-xl p-3 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Seleccionar...</option>
                                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                                    </select>
                                </div>
                            </div>
                        </Card>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <Card class="p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <Globe :size="20" class="text-blue-500" />
                                    <h3 class="font-black uppercase text-xs tracking-wider text-slate-700">Preferencia</h3>
                                </div>
                                <select v-model="form.preferred_currency" class="w-full border-slate-200 rounded-xl p-3 text-sm font-bold">
                                    <option v-for="c in currencies" :key="c.value" :value="c.value">{{ c.label }}</option>
                                </select>
                            </Card>

                            <Card class="p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <FileCheck :size="20" class="text-green-500" />
                                    <h3 class="font-black uppercase text-xs tracking-wider text-slate-700">W-9 Status</h3>
                                </div>
                                <select v-model="form.w9_status" class="w-full border-slate-200 rounded-xl p-3 text-sm font-bold">
                                    <option value="pending">Pendiente</option>
                                    <option value="submitted">Presentado</option>
                                    <option value="verified">Verificado ✅</option>
                                </select>
                                <div class="mt-3">
                                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Subir Formulario W-9 (PDF/IMG)</label>
                                    <input @input="form.w9_file = $event.target.files[0]" type="file" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                    <p v-if="user.w9_file_url" class="mt-2 text-xs text-green-600 font-bold flex items-center gap-1">
                                        <FileCheck :size="12" /> Archivo subido previamente
                                    </p>
                                </div>
                            </Card>
                        </div>

                        <Card class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <DollarSign :size="20" class="text-yellow-500" />
                                <h3 class="font-black uppercase text-xs tracking-wider text-slate-700">Detalles de Pago (Zelle/Bank)</h3>
                            </div>
                            <textarea v-model="form.payment_info.details" rows="3" class="w-full border-slate-200 rounded-xl p-3 text-sm font-bold" placeholder="Escribe aquí tu método de cobro..."></textarea>
                        </Card>

                        <div class="flex justify-end">
                            <button type="submit" class="flex items-center gap-2 bg-indigo-600 text-white px-12 py-3.5 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 active:scale-95" :disabled="form.processing">
                                <Save :size="18" /> GUARDAR TODO
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
