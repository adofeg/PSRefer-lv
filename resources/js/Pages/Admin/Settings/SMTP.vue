<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Mail, Save, AlertCircle, CheckCircle } from 'lucide-vue-next';
import Card from '@/Components/Card.vue';

const props = defineProps({
    config: Object
});

const form = useForm({
    host: props.config.host || '',
    port: props.config.port || '587',
    username: props.config.username || '',
    password: props.config.password || '',
    encryption: props.config.encryption || 'tls',
    from_address: props.config.from_address || '',
    from_name: props.config.from_name || 'PS Refer'
});

const submit = () => {
    form.post(route('admin.settings.smtp.update'));
};
</script>

<template>
    <Head title="Configuración SMTP" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <Mail :size="24" class="text-indigo-600" />
                    Configuración de Correo (SMTP)
                </h1>
                <p class="text-slate-500 text-sm">Configura los parámetros para que el sistema pueda enviar notificaciones por email</p>
            </div>

            <div v-if="form.wasSuccessful" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                <CheckCircle :size="20" />
                Configuración guardada correctamente
            </div>

            <Card>
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Host & Port -->
                        <div class="space-y-4">
                            <h3 class="font-bold text-slate-700 border-b pb-2">Servidor</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Host SMTP</label>
                                <input v-model="form.host" type="text" placeholder="smtp.mailtrap.io" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500" required />
                                <p v-if="form.errors.host" class="mt-1 text-xs text-red-600">{{ form.errors.host }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Puerto</label>
                                    <input v-model="form.port" type="text" placeholder="587" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Encriptación</label>
                                    <select v-model="form.encryption" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500">
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">Ninguna</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Auth -->
                        <div class="space-y-4">
                            <h3 class="font-bold text-slate-700 border-b pb-2">Autenticación</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Usuario / Email</label>
                                <input v-model="form.username" type="text" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500" required />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
                                <input v-model="form.password" type="password" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500" required />
                            </div>
                        </div>

                        <!-- Sender Info -->
                        <div class="space-y-4 md:col-span-2">
                            <h3 class="font-bold text-slate-700 border-b pb-2">Remitente</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">De (Email)</label>
                                    <input v-model="form.from_address" type="email" placeholder="noreply@psrefer.com" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">De (Nombre)</label>
                                    <input v-model="form.from_name" type="text" placeholder="PS Refer System" class="w-full border-slate-300 rounded-lg focus:ring-indigo-500" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                        <div class="flex items-center gap-2 text-amber-600 text-sm">
                            <AlertCircle :size="18" />
                            <span>Asegúrate de que las credenciales sean correctas para evitar errores de envío.</span>
                        </div>
                        
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-bold flex items-center gap-2 shadow-lg shadow-indigo-100"
                        >
                            <Save v-if="!form.processing" :size="20" />
                            <span v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
