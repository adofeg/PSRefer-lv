<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import { ref } from 'vue';
import { Plus, Trash, Settings, FileText, Briefcase, Share2, Info } from 'lucide-vue-next';

const props = defineProps({
    offering: Object,
    categories: Array
});

const activeTab = ref('general');

const form = useForm({
    name: props.offering?.name || '',
    type: props.offering?.type || 'service',
    description: props.offering?.description || '',
    base_price: props.offering?.base_price || '',
    commission_rate: props.offering?.commission_rate || '',
    category_id: props.offering?.category_id || '',
    is_active: props.offering ? props.offering.is_active : true,
    is_active: props.offering ? props.offering.is_active : true,
    form_schema: props.offering?.form_schema || [],
    commission_rules: props.offering?.commission_rules || [],
    notification_emails: props.offering?.notification_emails || []
});

const addFormField = () => {
    form.form_schema.push({ 
        name: '', 
        label: '', 
        type: 'text', 
        required: false,
        options: ''
    });
};

const removeFormField = (index) => {
    form.form_schema.splice(index, 1);
};

const addCommissionRule = () => {
    form.commission_rules.push({
        condition: 'default',
        commission_rate: form.commission_rate,
        label: '',
        roles: '' // Comma separated Roles (e.g. associate)
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
    if (props.offering) {
        form.put(route('admin.offerings.update', props.offering.id));
    } else {
        form.post(route('admin.offerings.store'));
    }
};
</script>

<template>
    <Head :title="offering ? 'Editar Oferta' : 'Nueva Oferta'" />

    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-slate-800">{{ offering ? 'Editar Oferta' : 'Nueva Oferta' }}</h1>
                <button type="button" @click="$inertia.visit(route('admin.offerings.index'))" class="text-sm text-slate-500 hover:text-indigo-600 transition">Volver al catálogo</button>
            </div>

            <!-- Tabs -->
            <div class="flex gap-4 border-b border-slate-200">
                <button 
                    @click="activeTab = 'general'"
                    :class="['pb-2 px-4 text-sm font-bold transition-all', activeTab === 'general' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-400 hover:text-slate-600']"
                >
                    Información General
                </button>
                <button 
                    @click="activeTab = 'form'"
                    :class="['pb-2 px-4 text-sm font-bold transition-all', activeTab === 'form' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-400 hover:text-slate-600']"
                >
                    Formulario Dinámico
                </button>
                <button 
                    @click="activeTab = 'rules'"
                    :class="['pb-2 px-4 text-sm font-bold transition-all', activeTab === 'rules' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-400 hover:text-slate-600']"
                >
                    Reglas de Comisión
                </button>

            </div>

            <Card>
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- TAB: General -->
                    <div v-show="activeTab === 'general'" class="space-y-6 animate-fade-in">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nombre del Servicio/Producto</label>
                                <input v-model="form.name" type="text" class="w-full border-slate-300 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: Seguro Comercial Premium" required />
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Tipo de Oferta</label>
                                <select v-model="form.type" class="w-full border-slate-300 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="service">Servicio</option>
                                    <option value="product">Producto</option>
                                    <option value="professional">Profesional</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Categoría</label>
                                <select v-model="form.category_id" class="w-full border-slate-300 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccionar categoría...</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Descripción</label>
                                <textarea v-model="form.description" rows="4" class="w-full border-slate-300 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Describe brevemente la oferta..."></textarea>
                            </div>

                            <div class="col-span-2">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-bold text-slate-700">Notificaciones (Emails)</label>
                                    <button @click="addEmail" type="button" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                        <Plus :size="14" /> Agregar
                                    </button>
                                </div>
                                
                                <div v-if="form.notification_emails.length === 0" class="text-xs text-slate-400 italic mb-2">
                                    Sin correos configurados.
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div v-for="(email, index) in form.notification_emails" :key="index" class="flex gap-2 items-center">
                                        <input v-model="form.notification_emails[index]" type="email" placeholder="ejemplo@ps.com" class="w-full text-sm border-slate-300 rounded-lg p-2" />
                                        <button @click="removeEmail(index)" type="button" class="text-red-400 hover:text-red-600">
                                            <Trash :size="16" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Precio Base ($)</label>
                                <input v-model="form.base_price" type="number" step="0.01" class="w-full border-slate-300 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Comisión Base (%)</label>
                                <input v-model="form.commission_rate" type="number" step="0.01" class="w-full border-slate-300 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input v-model="form.is_active" type="checkbox" class="w-5 h-5 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500" />
                                <div>
                                    <span class="text-sm font-bold text-slate-700 block">Oferta Activa</span>
                                    <p class="text-xs text-slate-500">Los asociados podrán ver y referir esta oferta si está activa.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- TAB: Form Builder -->
                    <div v-show="activeTab === 'form'" class="space-y-6 animate-fade-in">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Campos Dinámicos</h3>
                                <p class="text-xs text-slate-500 font-medium">Define qué datos debe capturar el asociado al enviar un lead</p>
                            </div>
                            <button @click="addFormField" type="button" class="flex items-center gap-2 bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-indigo-100 transition">
                                <Plus :size="16" /> AGREGAR CAMPO
                            </button>
                        </div>

                        <div v-if="form.form_schema.length === 0" class="text-center py-12 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <FileText :size="32" class="mx-auto text-slate-300 mb-2" />
                            <p class="text-sm text-slate-400">No hay campos definidos. Se capturarán los básicos: Nombre, Teléfono, Email.</p>
                        </div>

                        <div v-else class="space-y-3">
                            <div v-for="(field, index) in form.form_schema" :key="index" class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-start md:items-center">
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3 w-full">
                                    <input v-model="field.label" type="text" placeholder="Etiqueta (Ej: Numero de Póliza)" class="text-sm border-slate-300 rounded-lg p-2 w-full" />
                                    <select v-model="field.type" class="text-sm border-slate-300 rounded-lg p-2 w-full">
                                        <option value="text">Texto Corto</option>
                                        <option value="number">Número</option>
                                        <option value="date">Fecha</option>
                                        <option value="email">Email</option>
                                        <option value="checkbox">Checkboox</option>
                                        <option value="select">Lista de Selección</option>
                                    </select>
                                    <input v-if="field.type === 'select'" v-model="field.options" type="text" placeholder="Opciones (Ej: Auto, Moto, Camion)" class="text-sm border-slate-300 rounded-lg p-2 w-full" />
                                    <div v-else class="flex items-center gap-2 px-2">
                                        <input v-model="field.required" type="checkbox" class="rounded text-indigo-600" />
                                        <span class="text-xs font-bold text-slate-500 uppercase">Requerido</span>
                                    </div>
                                </div>
                                <button @click="removeFormField(index)" type="button" class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition">
                                    <Trash :size="18" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: Rules Builder -->
                    <div v-show="activeTab === 'rules'" class="space-y-6 animate-fade-in">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Reglas Basadas en Monto o Rol</h3>
                                <p class="text-xs text-slate-500 font-medium">Define condiciones especiales para el cálculo de comisiones</p>
                            </div>
                            <button @click="addCommissionRule" type="button" class="flex items-center gap-2 bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-indigo-100 transition">
                                <Plus :size="16" /> AGREGAR REGLA
                            </button>
                        </div>

                        <div v-if="form.commission_rules.length === 0" class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex gap-3">
                            <Info :size="20" class="text-blue-500 shrink-0" />
                            <p class="text-sm text-blue-700">Se aplicará el <strong>{{ form.commission_rate }}% base</strong> para todos los referidos si no hay reglas adicionales.</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="(rule, index) in form.commission_rules" :key="index" class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                    <div class="md:col-span-4">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Condición (Ej: deal_value >= 5000)</label>
                                        <input v-model="rule.condition" type="text" class="w-full text-sm border-slate-300 rounded-lg p-2" placeholder="deal_value >= 10000" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Comisión (%)</label>
                                        <input v-model="rule.commission_rate" type="number" step="0.01" class="w-full text-sm border-slate-300 rounded-lg p-2" />
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Filtro por Rol (Opcional)</label>
                                        <input v-model="rule.roles" type="text" class="w-full text-sm border-slate-300 rounded-lg p-2" placeholder="associate" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Nombre Regla</label>
                                        <input v-model="rule.label" type="text" class="w-full text-sm border-slate-300 rounded-lg p-2" placeholder="Bono High Ticket" />
                                    </div>
                                    <div class="md:col-span-1 flex justify-center">
                                        <button @click="removeCommissionRule(index)" type="button" class="p-2 text-red-300 hover:text-red-500 transition">
                                            <Trash :size="20" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                        <button type="button" @click="$inertia.visit(route('admin.offerings.index'))" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 rounded-xl transition">CANCELAR</button>
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition">
                            {{ offering ? 'Actualizar Oferta' : 'Crear Oferta' }}
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>