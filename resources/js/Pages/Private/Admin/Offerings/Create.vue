<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { ref } from 'vue';
import { Plus, Trash, FileText, Info, Save } from 'lucide-vue-next';
import { normalizeResource } from '@/Utils/inertia';
import SchemaBuilder from '@/Components/Offerings/SchemaBuilder.vue';

const props = defineProps({
    offering: Object,
    categories: Array,
    commissionable_roles: Array,
    all_associates: Array,
});

import MultiSelectCombobox from '@/Components/UI/MultiSelectCombobox.vue';

const activeTab = ref('general');
const offering = normalizeResource(props.offering, null);

const form = useForm({
    name: offering?.name || '',
    type: offering?.type || 'service',
    description: offering?.description || '',
    base_commission: offering?.base_commission !== null && offering?.base_commission !== undefined ? offering.base_commission : '',
    category_id: offering?.category_id || '',
    is_active: offering ? offering.is_active : true,
    form_schema: offering?.form_schema || [],
    commission_rules: offering?.commission_rules || [],
    notification_emails: offering?.notification_emails || [],
    commission_type: offering?.commission_type || 'percentage',
});

const addCommissionRule = () => {
    // Determine default roles (if only one, pre-select it)
    const defaultRoles = props.commissionable_roles?.length === 1 
        ? [props.commissionable_roles[0]] 
        : [];

    form.commission_rules.push({
        condition: 'default',
        base_commission: form.base_commission,
        label: '',
        roles: defaultRoles, // Now an array
        user_ids: [] // Now an array of IDs
    });
};

const removeCommissionRule = (index) => {
    form.commission_rules.splice(index, 1);
};

const addEmail = () => {
    form.notification_emails.push('');
};

const removeEmail = (index) => {
    form.notification_emails.splice(index, 1);
};

const submit = () => {
    if (offering) {
        form.put(route('admin.offerings.update', offering.id));
    } else {
        form.post(route('admin.offerings.store'));
    }
};
</script>

<template>
    <Head :title="offering ? 'Editar Oferta' : 'Nueva Oferta'" />

    <AppLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex flex-col gap-1">
                    <h1 class="text-3xl font-black text-slate-800 tracking-tight">{{ offering ? 'Editar Oferta' : 'Nueva Oferta' }}</h1>
                    <p class="text-sm font-medium text-slate-500">
                        {{ offering ? 'Actualiza la información y el formulario de captación para este servicio.' : 'Configura una nueva oferta y su formulario de captación para el catálogo.' }}
                    </p>
                </div>
            </div>

            <!-- Modern Tabs -->
            <div class="bg-white p-1.5 rounded-2xl shadow-sm border border-slate-200/60 inline-flex gap-1">
                <button 
                    @click="activeTab = 'general'"
                    :class="['px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2', activeTab === 'general' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-500 hover:bg-slate-50']"
                >
                    <Info :size="18" />
                    Información General
                </button>
                <button 
                    @click="activeTab = 'form'"
                    :class="['px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2', activeTab === 'form' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-500 hover:bg-slate-50']"
                >
                    <FileText :size="18" />
                    Formulario Dinámico
                </button>
                <button 
                    @click="activeTab = 'rules'"
                    :class="['px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2', activeTab === 'rules' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-500 hover:bg-slate-50']"
                >
                    <Plus :size="18" />
                    Reglas de Comisión
                </button>
            </div>

            <Card clazz="p-8 shadow-xl border-slate-200/50 bg-white/50 backdrop-blur-sm">
                <form @submit.prevent="submit" class="space-y-10">
                    
                    <!-- TAB: General -->
                    <div v-show="activeTab === 'general'" class="space-y-8 animate-fade-in">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="md:col-span-3">
                                <label class="block text-xs font-black uppercase text-slate-400 tracking-widest mb-2">Nombre del Servicio/Producto</label>
                                <input v-model="form.name" type="text" class="w-full border-slate-200 rounded-2xl p-4 text-lg font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all bg-white" placeholder="Ej: Seguro Comercial Premium" required />
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-black uppercase text-slate-400 tracking-widest mb-2">Tipo de Oferta</label>
                                <select v-model="form.type" class="w-full border-slate-200 rounded-2xl p-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white cursor-pointer">
                                    <option value="service">Servicio</option>
                                    <option value="product">Producto</option>
                                    <option value="professional">Profesional</option>
                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-black uppercase text-slate-400 tracking-widest mb-2">Categoría</label>
                                <select v-model="form.category_id" class="w-full border-slate-200 rounded-2xl p-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white cursor-pointer">
                                    <option value="">Seleccionar categoría...</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-black uppercase text-slate-400 tracking-widest mb-2">Estado Inicial</label>
                                <div class="bg-slate-50/50 p-2.5 rounded-2xl border border-slate-200/50">
                                    <label class="flex items-center gap-3 cursor-pointer select-none px-2 py-1.5">
                                        <div class="relative">
                                            <input v-model="form.is_active" type="checkbox" class="peer sr-only" />
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </div>
                                        <span class="text-sm font-bold text-slate-700">{{ form.is_active ? 'Activa' : 'Inactiva' }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-black uppercase text-slate-400 tracking-widest mb-2">Descripción</label>
                                <textarea v-model="form.description" rows="3" class="w-full border-slate-200 rounded-2xl p-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white transition-all" placeholder="Describe brevemente la oferta..."></textarea>
                            </div>

                            <div class="md:col-span-3">
                                <div class="flex justify-between items-center mb-4">
                                    <label class="block text-xs font-black uppercase text-slate-400 tracking-widest">Notificaciones (Emails)</label>
                                    <button @click="addEmail" type="button" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-black uppercase tracking-tight hover:bg-indigo-100 transition flex items-center gap-1.5">
                                        <Plus :size="14" /> Agregar Email
                                    </button>
                                </div>
                                
                                <div v-if="form.notification_emails.length === 0" class="p-6 text-center border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 text-sm italic">
                                    No hay correos configurados para recibir alertas.
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div v-for="(email, index) in form.notification_emails" :key="index" class="flex gap-3 items-center group">
                                        <div class="relative flex-1">
                                            <input v-model="form.notification_emails[index]" type="email" placeholder="ejemplo@ps.com" class="w-full border-slate-200 rounded-xl p-3 pl-10 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm" />
                                            <FileText :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-400 transition-colors" />
                                        </div>
                                        <button @click="removeEmail(index)" type="button" class="p-3 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition">
                                            <Trash :size="18" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- TAB: Form Builder -->
                    <div v-show="activeTab === 'form'" class="space-y-6 animate-fade-in">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Campos Dinámicos</h3>
                                <p class="text-xs text-slate-500 font-medium">Define qué datos debe capturar el asociado al enviar un lead</p>
                            </div>
                        </div>

                        <SchemaBuilder v-model="form.form_schema" />
                    </div>

                <!-- TAB: Rules Builder -->
                <div v-show="activeTab === 'rules'" class="space-y-8 animate-fade-in">
                    
                    <!-- Base Commission Config -->
                    <div class="bg-indigo-50/30 p-8 rounded-3xl border border-indigo-100/50 shadow-sm mb-8">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-8">
                            <div class="lg:w-1/3">
                                <h4 class="text-sm font-black uppercase text-indigo-600 tracking-widest mb-1">Comisión Base</h4>
                                <p class="text-xs text-slate-500 font-medium">Define la tasa por defecto si no se cumplen reglas específicas.</p>
                            </div>
                            
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-tighter">Tipo de Comisión</label>
                                    <select v-model="form.commission_type" class="w-full border-slate-200 rounded-2xl p-4 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white transition-all font-bold">
                                        <option value="percentage">Porcentaje (%)</option>
                                        <option value="fixed">Monto Fijo ($)</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-tighter">
                                        {{ form.commission_type === 'percentage' ? 'Comisión Base (%)' : 'Comisión Base ($)' }}
                                    </label>
                                    <div class="relative">
                                        <input v-model="form.base_commission" type="number" step="0.01" class="w-full border-slate-200 rounded-2xl p-4 text-sm font-black text-indigo-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white transition-all pl-10" />
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 font-black">
                                            {{ form.commission_type === 'percentage' ? '%' : '$' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <div class="space-y-1">
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Reglas Basadas en Monto o Rol</h3>
                            <p class="text-sm text-slate-500 font-medium">Define condiciones especiales para el cálculo de comisiones</p>
                        </div>
                        <button @click="addCommissionRule" type="button" class="flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                            <Plus :size="18" /> AGREGAR REGLA
                        </button>
                    </div>

                    <div v-if="form.commission_rules.length === 0" class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100/50 flex gap-4 items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                            <Info :size="20" class="text-blue-600" />
                        </div>
                        <p class="text-sm text-blue-700 font-medium">Se aplicará la comisión base de <strong class="text-blue-900">{{ form.base_commission }}{{ form.commission_type === 'percentage' ? '%' : '$' }}</strong> para todos los referidos si no hay reglas adicionales.</p>
                    </div>

                    <div v-else class="space-y-6">
                        <div v-for="(rule, index) in form.commission_rules" :key="index" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl transition-all group">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                                <!-- Basic Info -->
                                <div class="lg:col-span-11 grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-tighter">Nombre de la Regla (Interno)</label>
                                        <input v-model="rule.label" type="text" class="w-full text-sm border-slate-200 rounded-2xl p-4 bg-slate-50/50 focus:bg-white transition-all focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-bold" placeholder="Bono High Ticket..." />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-tighter">Condición de Monto</label>
                                        <input v-model="rule.condition" type="text" class="w-full text-sm border-slate-200 rounded-2xl p-4 bg-slate-50/50 focus:bg-white transition-all focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500" placeholder="deal_value >= 10000" />
                                        <p class="text-[9px] text-slate-400 font-medium italic">Ej: deal_value >= 5000</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-tighter">
                                            Valor Comisión ({{ form.commission_type === 'percentage' ? '%' : '$' }})
                                        </label>
                                        <input v-model="rule.base_commission" type="number" step="0.01" class="w-full text-sm border-slate-200 rounded-2xl p-4 bg-slate-50/50 focus:bg-white transition-all focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-black" />
                                    </div>
                                </div>

                                <div class="lg:col-span-1 flex justify-center items-start">
                                    <button @click="removeCommissionRule(index)" type="button" class="p-4 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all group-hover:bg-slate-50">
                                        <Trash :size="20" />
                                    </button>
                                </div>

                                <!-- Filters -->
                                <div class="lg:col-span-12 grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-slate-50">
                                    <div class="space-y-3">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-tighter">Filtrar por Roles (Asociados)</label>
                                        <MultiSelectCombobox 
                                            v-model="rule.roles" 
                                            :options="commissionable_roles.map(r => ({ id: r, name: r }))"
                                            placeholder="Seleccionar roles..."
                                            :disabled="commissionable_roles.length === 1"
                                        />
                                        <p v-if="commissionable_roles.length === 1" class="text-[9px] text-slate-400 font-medium italic">
                                            Rol '{{ commissionable_roles[0] }}' seleccionado automáticamente (único rol comisionable).
                                        </p>
                                    </div>
                                    <div class="space-y-3">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-tighter">Usuarios Específicos (Opcional)</label>
                                        <MultiSelectCombobox 
                                            v-model="rule.user_ids" 
                                            :options="all_associates"
                                            placeholder="Buscar por nombre o email..."
                                        />
                                        <p class="text-[9px] text-slate-400 font-medium">Si seleccionas usuarios, la regla solo aplicará a ellos.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                    <!-- Actions -->
                    <div class="flex justify-end items-center gap-4 pt-10 border-t border-slate-100">
                        <button type="button" @click="$inertia.visit(route('admin.offerings.index'))" class="px-8 py-4 text-sm font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition">
                            CANCELAR
                        </button>
                        <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 flex items-center gap-3">
                            <save :size="20" class="opacity-50" />
                            {{ offering ? 'Actualizar Oferta' : 'Crear Oferta' }}
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
