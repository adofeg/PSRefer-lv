<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { User, DollarSign, FileCheck, Globe, Camera, Lock, Save, Landmark } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import Card from '@/Components/UI/Card.vue';

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
    _method: 'PUT'
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
            // Success logic handled by toast if available, or just standard state
        }
    });
};

const updatePassword = () => {
    passwordForm.post(route('settings.password'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        }
    });
};

const isAdmin = computed(() => ['admin', 'psadmin'].includes(props.user.role));

const currencies = [
    { value: 'USD', label: 'USD - Dólar' },
    { value: 'MXN', label: 'MXN - Peso Mexicano' },
    { value: 'COP', label: 'COP - Peso Colombiano' }
];
</script>

<template>
    <Head title="Configuración de Perfil" />

    <AppLayout>
        <div class="w-full space-y-8 animate-fade-in pb-20">
            <!-- 1. Compact Premium Header -->
            <Card class="bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 border-none shadow-xl relative overflow-hidden p-0 mb-8 border border-white/5">
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-600/10 rounded-full blur-3xl"></div>
                
                <div class="relative px-6 py-6 md:px-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <!-- Left: Identity -->
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-800 ring-4 ring-white/5 shadow-2xl transition-transform duration-500 group-hover:scale-105">
                                <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center text-slate-600 bg-slate-800">
                                    <User :size="32" />
                                </div>
                            </div>
                            <label class="absolute -bottom-1 -right-1 p-2 bg-indigo-600 text-white rounded-xl shadow-lg hover:bg-indigo-500 transition-all cursor-pointer ring-2 ring-slate-900">
                                <Camera :size="14" />
                                <input type="file" @change="onLogoChange" class="hidden" accept="image/*" />
                            </label>
                        </div>
                        <div class="text-center md:text-left">
                            <div class="flex items-center justify-center md:justify-start gap-3 mb-1">
                                <h1 class="text-xl font-black text-white tracking-tight">{{ user.name }}</h1>
                                <span class="px-2 py-0.5 bg-indigo-500/20 text-indigo-300 rounded-md text-[9px] font-black uppercase tracking-widest border border-indigo-500/30">
                                    {{ user.role }}
                                </span>
                            </div>
                            <p class="text-slate-400 font-bold text-xs">{{ user.email }} • ID: {{ user.id }}</p>
                        </div>
                    </div>

                    <!-- Right: Quick Status -->
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col items-end gap-1.5">
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-white/5 rounded-lg border border-white/5 backdrop-blur-md">
                                <Globe :size="12" class="text-indigo-400" />
                                <span class="text-[10px] font-black text-white/70 uppercase tracking-widest">{{ form.preferred_currency }}</span>
                            </div>
                            <div v-if="!isAdmin" :class="[
                                'flex items-center gap-2 px-3 py-1.5 rounded-lg border backdrop-blur-md uppercase text-[9px] font-black tracking-widest',
                                user.w9_status === 'verified' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'bg-amber-500/10 border-amber-500/20 text-amber-400'
                            ]">
                                <FileCheck :size="12" />
                                {{ user.w9_status === 'verified' ? 'Verificado' : 'Pendiente' }}
                            </div>
                        </div>
                    </div>
                </div>
            </Card>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- MAIN COLUMN (Left - 8/12) -->
                    <div class="lg:col-span-8 space-y-8">
                        <!-- Personal Info -->
                        <Card class="p-8 border-slate-100 shadow-sm transition-all hover:shadow-md">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100">
                                    <User :size="18" />
                                </div>
                                <div>
                                    <h3 class="text-base font-black uppercase text-slate-800 tracking-wide">Información Personal</h3>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Datos de contacto y visualización</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2 space-y-2">
                                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest ml-1">Nombre Completo o Razón Social</label>
                                    <input v-model="form.name" type="text" class="w-full border-slate-200 rounded-xl p-3.5 text-sm font-bold text-slate-700 bg-slate-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none border" />
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest ml-1">Teléfono de Contacto</label>
                                    <input v-model="form.phone" type="text" class="w-full border-slate-200 rounded-xl p-3.5 text-sm font-bold text-slate-700 bg-slate-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none border" placeholder="+1 (000) 000-0000" />
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest ml-1">Moneda Preferida</label>
                                    <select v-model="form.preferred_currency" class="w-full border-slate-200 rounded-xl p-3.5 text-sm font-bold text-slate-700 bg-slate-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none border appearance-none cursor-pointer">
                                        <option v-for="c in currencies" :key="c.value" :value="c.value">{{ c.label }}</option>
                                    </select>
                                </div>
                            </div>
                        </Card>

                        <!-- Payment & Tax -->
                        <div v-if="!isAdmin">
                            <Card class="p-8 border-slate-100 shadow-sm transition-all hover:shadow-md">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                                        <Landmark :size="18" />
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black uppercase text-slate-800 tracking-wide">Pagos & Facturación</h3>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Configura cómo recibes tus comisiones</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest ml-1">Detalles de Cobro (Zelle, PayPal, SWIFT...)</label>
                                        <textarea v-model="form.payment_info.details" rows="3" class="w-full border-slate-200 rounded-xl p-4 text-sm font-bold text-slate-700 bg-slate-50/50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none resize-none border" placeholder="Especifica tus datos de pago exactos..."></textarea>
                                    </div>

                                    <div class="p-5 bg-slate-50 border border-slate-100 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                                        <div>
                                            <label class="block text-[9px] font-black uppercase text-slate-400 tracking-widest mb-1.5 ml-0.5">Estado del Modelo W-9</label>
                                            <div :class="[
                                                'inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest border transition-all shadow-sm',
                                                user.w9_status === 'verified' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 
                                                user.w9_status === 'submitted' ? 'bg-blue-50 border-blue-200 text-blue-700' : 
                                                'bg-white border-slate-200 text-slate-500'
                                            ]">
                                                <div :class="['w-1.5 h-1.5 rounded-full', user.w9_status === 'verified' ? 'bg-emerald-500' : user.w9_status === 'submitted' ? 'bg-blue-500' : 'bg-slate-300']"></div>
                                                {{ user.w9_status === 'verified' ? 'Documento Verificado' : user.w9_status === 'submitted' ? 'En Revisión' : 'Pendiente de Envío' }}
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col items-center sm:items-end">
                                            <label for="w9-upload" class="inline-flex items-center gap-2 cursor-pointer bg-white text-slate-700 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition active:scale-95 border border-slate-200 shadow-sm group">
                                                <FileCheck :size="14" class="text-indigo-600 group-hover:scale-110 transition-transform" />
                                                {{ user.w9_file_url ? 'Actualizar W-9' : 'Cargar W-9' }}
                                                <input type="file" id="w9-upload" @change="e => form.w9_file = e.target.files[0]" class="hidden" accept=".pdf,.png,.jpg,.jpeg,.webp" />
                                            </label>
                                            <div v-if="form.w9_file" class="mt-2 text-[8px] font-black text-indigo-600 px-1 border-b border-indigo-200 pb-0.5 max-w-[150px] truncate animate-pulse">
                                                📄 {{ form.w9_file.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <p v-if="form.errors.w9_file" class="text-[9px] text-red-500 font-bold px-1 text-center sm:text-left">{{ form.errors.w9_file }}</p>
                                </div>
                            </Card>
                        </div>
                    </div>

                    <!-- SIDEBAR COLUMN (Right - 4/12) -->
                    <div class="lg:col-span-4 space-y-8">
                        <!-- Category (If Associate) -->
                        <Card v-if="!isAdmin" class="p-6 border-slate-100 shadow-sm bg-slate-50/30">
                            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-4 ml-1">Estatus Profesional</label>
                            <div class="flex items-center gap-4 p-4 bg-white border border-slate-100 rounded-xl">
                                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                                    <Globe :size="16" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-slate-700 truncate capitalize">{{ user.category || 'Sin Categoría' }}</p>
                                    <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest leading-tight mt-0.5">Categoría bloqueada</p>
                                </div>
                            </div>
                        </Card>

                        <!-- Security -->
                        <Card class="p-8 border-slate-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100">
                                    <Lock :size="18" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-black uppercase text-slate-800 tracking-widest">Seguridad</h3>
                                    <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">Protección de cuenta</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="space-y-1.5">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 tracking-widest ml-1">Contraseña Actual</label>
                                    <input v-model="passwordForm.current_password" type="password" class="w-full border-slate-200 rounded-xl p-3 text-xs font-bold text-slate-700 bg-slate-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none border" placeholder="••••••••" />
                                    <p v-if="passwordForm.errors.current_password" class="text-[9px] text-red-500 font-bold mt-1">{{ passwordForm.errors.current_password }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 tracking-widest ml-1">Nueva Contraseña</label>
                                    <input v-model="passwordForm.new_password" type="password" class="w-full border-slate-200 rounded-xl p-3 text-xs font-bold text-slate-700 bg-slate-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none border" placeholder="Mínimo 8 caracteres" />
                                    <p v-if="passwordForm.errors.new_password" class="text-[9px] text-red-500 font-bold mt-1">{{ passwordForm.errors.new_password }}</p>
                                </div>
                                <button type="button" @click="updatePassword" class="w-full h-12 bg-white text-indigo-600 border border-indigo-100 rounded-xl font-black text-[10px] uppercase tracking-[0.1em] shadow-sm hover:bg-indigo-50 transition active:scale-[0.98] disabled:opacity-50 mt-2" :disabled="passwordForm.processing">
                                    {{ passwordForm.processing ? 'ACTUALIZANDO...' : 'CAMBIAR CONTRASEÑA' }}
                                </button>
                            </div>
                        </Card>
                    </div>
                </div>

                <!-- Sticky Action Bar for Global Save -->
                <div class="sticky bottom-8 z-10 mt-12">
                    <div class="bg-indigo-900/10 backdrop-blur-xl p-4 rounded-3xl border border-white/20 shadow-2xl flex justify-between items-center gap-4 ring-1 ring-white/10">
                        <div class="hidden md:flex items-center gap-3 ml-4">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Cambios pendientes de guardar</span>
                        </div>
                        <button 
                            type="submit" 
                            class="w-full md:w-auto flex items-center justify-center gap-3 bg-slate-900 text-white px-10 h-14 rounded-2xl font-black text-sm uppercase tracking-[0.2em] transform transition-all hover:bg-slate-800 hover:shadow-xl active:scale-95 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <Save :size="18" />
                            <span>{{ form.processing ? 'GUARDANDO...' : 'GUARDAR TODO' }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

/* Custom Form Styles */
input::placeholder { color: #94a3b8; font-weight: 500; font-size: 0.8rem; text-transform: none; }
textarea::placeholder { color: #94a3b8; font-weight: 500; font-size: 0.8rem; text-transform: none; }

/* Selection fix */
::selection { background: #6366f1; color: white; }
</style>