<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { User, DollarSign, FileCheck, Globe, Camera, Lock, Save, Landmark, FileText } from 'lucide-vue-next';
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
    w9_file: null,
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
            <!-- 2. Dashboard Hero Hub (Gradient Identity Card) -->
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-800 to-slate-900 rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-white/10 group">
                <!-- Decorative effects -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl transition-transform duration-1000 group-hover:scale-150"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl"></div>

                <div class="relative flex flex-col md:flex-row items-center md:items-end justify-between gap-8">
                    <!-- Identity Info -->
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="relative">
                            <div class="w-24 h-24 md:w-28 md:h-28 rounded-3xl bg-white/10 border-4 border-white/20 shadow-2xl overflow-hidden backdrop-blur-md transition-transform duration-500 group-hover:rotate-1">
                                <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center text-white/20">
                                    <User :size="40" />
                                </div>
                            </div>
                            <label class="absolute -bottom-2 -right-2 p-3 bg-white text-indigo-900 rounded-2xl shadow-2xl hover:bg-indigo-50 transition-all cursor-pointer border-2 border-white active:scale-95">
                                <Camera :size="20" />
                                <input type="file" @change="onLogoChange" class="hidden" accept="image/*" />
                            </label>
                        </div>
                        
                        <div class="text-center md:text-left space-y-2">
                            <span class="px-3 py-1 bg-white/10 text-indigo-200 border border-white/10 rounded-full text-[10px] font-black uppercase tracking-[0.2em] backdrop-blur-md">
                                {{ user.role }}
                            </span>
                            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">{{ user.name }}</h2>
                            <p class="text-indigo-100/60 font-medium">{{ user.email }}</p>
                        </div>
                    </div>

                    <!-- Quick Status Hub -->
                    <div v-if="!isAdmin" class="flex flex-wrap justify-center md:justify-end gap-3">
                        <div class="bg-white/5 border border-white/10 hover:border-white/20 transition-colors rounded-3xl p-5 backdrop-blur-md flex items-center gap-4">
                            <div class="p-2.5 bg-indigo-500/20 text-indigo-300 rounded-xl">
                                <Globe :size="18" />
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-widest text-indigo-300/60 leading-none mb-1">Moneda</p>
                                <p class="text-sm font-black text-white leading-none">{{ form.preferred_currency }}</p>
                            </div>
                        </div>

                        <div class="bg-white/5 border border-white/10 hover:border-white/20 transition-colors rounded-3xl p-5 backdrop-blur-md flex items-center gap-4">
                            <div :class="[
                                'p-2.5 rounded-xl',
                                user.w9_status === 'verified' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400'
                            ]">
                                <FileCheck :size="18" />
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-widest leading-none mb-1" :class="user.w9_status === 'verified' ? 'text-emerald-300/60' : 'text-amber-300/60'">W-9 STATUS</p>
                                <p class="text-sm font-black text-white leading-none capitalize">{{ user.w9_status === 'verified' ? 'Verificado' : 'Pendiente' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Consolidated Modular Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Personal Data Card (Structural Consistency with Users/Edit) -->
                <Card class="p-8 border-slate-100/60 shadow-xl shadow-slate-200/50">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                            <User :size="20" />
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight uppercase">Datos Personales</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Información de contacto y categoría</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre Completo o Razón Social</label>
                            <input v-model="form.name" type="text" class="w-full h-14 bg-slate-50 border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all" />
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Teléfono Móvil</label>
                                <input v-model="form.phone" type="text" class="w-full h-14 bg-slate-50 border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Moneda Preferida</label>
                                <select v-model="form.preferred_currency" class="w-full h-14 bg-slate-50 border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <option v-for="c in currencies" :key="c.value" :value="c.value">{{ c.label }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Professional Category Info -->
                        <div v-if="!isAdmin" class="pt-4 border-t border-slate-50">
                            <div class="p-5 bg-slate-50/50 border border-slate-100 rounded-2xl flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="p-2.5 bg-white text-indigo-600 rounded-xl shadow-sm border border-slate-100">
                                        <Landmark :size="18" />
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest mb-0.5">Categoría Profesional</p>
                                        <p class="text-sm font-black text-slate-700 leading-none capitalize">{{ user.category || 'Sin Categoría' }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-500 text-[8px] font-black uppercase rounded border border-indigo-100">SOPORTE</span>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Security Card -->
                <Card class="p-8 border-slate-100/60 shadow-xl shadow-slate-200/50">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="p-3 bg-red-50 text-red-600 rounded-2xl">
                            <Lock :size="20" />
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight uppercase">Seguridad</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Control de acceso y contraseña</p>
                        </div>
                    </div>

                    <form @submit.prevent="updatePassword" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Contraseña Actual</label>
                            <input v-model="passwordForm.current_password" type="password" class="w-full h-14 bg-slate-50 border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-red-500/10 transition-all" placeholder="••••••••" />
                            <p v-if="passwordForm.errors.current_password" class="text-xs text-red-500 mt-1">{{ passwordForm.errors.current_password }}</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nueva Contraseña</label>
                            <input v-model="passwordForm.new_password" type="password" class="w-full h-14 bg-slate-50 border-slate-200 rounded-2xl px-5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Nueva contraseña" />
                            <p v-if="passwordForm.errors.new_password" class="text-xs text-red-500 mt-1">{{ passwordForm.errors.new_password }}</p>
                        </div>

                        <button 
                            type="submit" 
                            class="w-full h-14 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg active:scale-95 disabled:opacity-50"
                            :disabled="passwordForm.processing"
                        >
                            {{ passwordForm.processing ? 'Sincronizando...' : 'Actualizar Contraseña' }}
                        </button>
                    </form>
                </Card>

                <!-- Financial HUB (Full Width at bottom) -->
                <div v-if="!isAdmin" class="lg:col-span-2">
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 p-8 md:p-12 relative overflow-hidden">
                        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-emerald-500/5 rounded-full blur-[100px]"></div>
                        
                        <div class="relative flex items-center gap-4 mb-10 pb-6 border-b border-slate-50">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                                <DollarSign :size="20" />
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-800 tracking-tight uppercase">Gestión Financiera Associate</h2>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-0.5">Configuración de cobros e impuestos</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                            <!-- Payment Details Section -->
                            <div class="md:col-span-7 space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Método de Pago y Detalles de Transferencia</label>
                                <textarea v-model="form.payment_info.details" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-6 text-sm font-bold text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500/20 transition-all resize-none" placeholder="Nombre del Banco, Zelle (email/tel), PayPal, o detalles SWIFT..."></textarea>
                            </div>

                            <!-- W-9 Column -->
                            <div class="md:col-span-5 flex flex-col justify-end gap-6 pb-2">
                                <div class="p-6 bg-slate-50/50 border border-slate-100 rounded-3xl">
                                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-800 mb-4">
                                        <FileCheck :size="18" class="text-indigo-600" />
                                        Cumplimiento Fiscal (W-9)
                                    </h3>
                                    
                                    <!-- IRS Download Link -->
                                    <div class="mb-6 p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                                        <p class="text-xs text-indigo-800 mb-2 font-medium">Requisito Obligatorio</p>
                                        <p class="text-[10px] text-indigo-600/80 mb-3 leading-relaxed">
                                            Para procesar pagos de comisiones, necesitamos que complete el formulario W-9 del IRS.
                                        </p>
                                        <a 
                                            href="https://www.irs.gov/pub/irs-pdf/fw9.pdf" 
                                            target="_blank" 
                                            class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-indigo-700 hover:text-indigo-900 transition-colors bg-white px-3 py-2 rounded-lg shadow-sm"
                                        >
                                            <FileText :size="14" />
                                            Descargar Formulario Oficial
                                        </a>
                                    </div>

                                    <!-- Manual Status Toggle -->
                                    <div class="space-y-3">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Estado de su Documentación</label>
                                        
                                        <div class="space-y-2">
                                            <!-- Pending Option -->
                                            <label class="flex items-start gap-3 p-3 rounded-xl border transition-all cursor-pointer relative overflow-hidden" 
                                                :class="form.w9_status === 'pending' ? 'bg-amber-50 border-amber-200 ring-1 ring-amber-500/20' : 'bg-white border-slate-200 hover:bg-slate-50'">
                                                <input type="radio" v-model="form.w9_status" value="pending" class="mt-1 text-amber-500 focus:ring-amber-500" />
                                                <div>
                                                    <span class="block text-xs font-bold text-slate-700">Pendiente</span>
                                                    <span class="text-[10px] text-slate-400">Aún no he enviado el formulario</span>
                                                </div>
                                            </label>

                                            <!-- Submitted Option -->
                                            <label class="flex items-start gap-3 p-3 rounded-xl border transition-all cursor-pointer"
                                                :class="form.w9_status === 'submitted' ? 'bg-emerald-50 border-emerald-200 ring-1 ring-emerald-500/20' : 'bg-white border-slate-200 hover:bg-slate-50'">
                                                <input type="radio" v-model="form.w9_status" value="submitted" class="mt-1 text-emerald-500 focus:ring-emerald-500" />
                                                <div>
                                                    <span class="block text-xs font-bold text-slate-700">Enviado / Notificado</span>
                                                    <span class="text-[10px] text-slate-400">Ya envié mi W-9 por email</span>
                                                </div>
                                            </label>
                                        </div>

                                        <p v-if="form.w9_status === 'submitted'" class="text-[10px] text-emerald-600 font-bold flex items-center gap-1 mt-2 animate-pulse">
                                            <FileCheck :size="12" />
                                            Gracias. Revisaremos su documentación.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Action Bar (Consistent with Dashboard Save approach) -->
            <div class="flex justify-end pt-8">
                <button 
                    @click="submit"
                    class="w-full md:w-auto flex items-center justify-center gap-4 bg-indigo-600 text-white px-12 h-16 rounded-[2rem] font-black text-sm uppercase tracking-[0.2em] transform transition-all hover:bg-indigo-700 hover:shadow-2xl hover:shadow-indigo-500/20 active:scale-95 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    <Save :size="20" />
                    <span>{{ form.processing ? 'Sincronizando...' : 'Guardar Cambios de Cuenta' }}</span>
                </button>
            </div>
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