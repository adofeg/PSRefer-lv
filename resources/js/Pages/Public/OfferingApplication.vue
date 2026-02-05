<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import DynamicForm from '@/Components/DynamicForm.vue';
import { User, Phone, Mail, DollarSign, Building2, CheckCircle2, ArrowLeft } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    offering: Object,
    referrer: Object,
    success: String
});

const form = useForm({
    client_name: '',
    client_contact: '',
    form_data: {},
    notes: '',
    referrer_id: props.referrer?.id || null
});

const submit = () => {
    form.post(route('public.apply.submit', props.offering.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Form will be reset on success via success message
        }
    });
};

const formatPrice = (price) => {
    if (!price) return null;
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0
    }).format(price);
};
</script>

<template>
    <Head>
        <title>{{ offering.name }} - Solicitud</title>
        <meta name="description" :content="offering.description">
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <!-- Header with Back Link -->
        <div class="bg-white border-b border-slate-200 shadow-sm">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <a 
                    :href="route('home')"
                    class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-indigo-600 transition"
                >
                    <ArrowLeft :size="16" />
                    Volver al inicio
                </a>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 py-8 sm:py-12">
            <!-- Success Message -->
            <div 
                v-if="success"
                class="mb-8 bg-green-50 border-2 border-green-500 rounded-2xl p-6 flex items-start gap-4 animate-fade-in"
            >
                <CheckCircle2 :size="28" class="text-green-600 flex-shrink-0 mt-1" />
                <div>
                    <h3 class="text-lg font-semibold text-green-900 mb-1">¡Solicitud Enviada!</h3>
                    <p class="text-green-800">{{ success }}</p>
                    <p class="text-sm text-green-700 mt-2">
                        Un representante se pondrá en contacto contigo pronto.
                    </p>
                </div>
            </div>

            <!-- Offering Header -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-10">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-white mb-3">
                                <Building2 :size="14" />
                                {{ offering.type === 'service' ? 'Servicio' : 'Producto' }}
                                <span v-if="offering.category" class="ml-1">• {{ offering.category }}</span>
                            </div>
                            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">
                                {{ offering.name }}
                            </h1>
                            <p class="text-lg text-indigo-100 leading-relaxed max-w-2xl">
                                {{ offering.description }}
                            </p>
                        </div>
                        <div v-if="offering.base_price" class="ml-4 flex-shrink-0">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl px-6 py-4 border border-white/20">
                                <p class="text-xs text-indigo-200 uppercase tracking-wide mb-1">Desde</p>
                                <div class="flex items-center gap-2">
                                    <DollarSign :size="24" class="text-white" />
                                    <span class="text-3xl font-bold text-white">
                                        {{ formatPrice(offering.base_price) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referrer Card (if exists) -->
            <div v-if="referrer" class="bg-white rounded-2xl shadow-lg p-6 mb-6 border-2 border-indigo-100">
                <p class="text-sm text-indigo-700 font-medium mb-3 uppercase tracking-wide">
                    👋 Referido por:
                </p>
                <div class="flex items-center gap-4">
                    <div 
                        v-if="referrer.logo_url" 
                        class="w-20 h-20 rounded-full overflow-hidden ring-4 ring-indigo-100 flex-shrink-0"
                    >
                        <img 
                            :src="referrer.logo_url" 
                            :alt="referrer.name" 
                            class="w-full h-full object-cover"
                        >
                    </div>
                    <div 
                        v-else 
                        class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center ring-4 ring-indigo-100 flex-shrink-0"
                    >
                        <User :size="40" class="text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-xl text-slate-900">{{ referrer.name }}</p>
                        <div class="mt-2 space-y-1">
                            <p v-if="referrer.phone" class="text-sm text-slate-600 flex items-center gap-2">
                                <Phone :size="16" class="text-indigo-600" />
                                <a :href="`tel:${referrer.phone}`" class="hover:text-indigo-600 transition">
                                    {{ referrer.phone }}
                                </a>
                            </p>
                            <p class="text-sm text-slate-600 flex items-center gap-2">
                                <Mail :size="16" class="text-indigo-600" />
                                <a :href="`mailto:${referrer.email}`" class="hover:text-indigo-600 transition">
                                    {{ referrer.email }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Form -->
            <form 
                v-if="!success"
                @submit.prevent="submit" 
                class="bg-white rounded-2xl shadow-xl p-6 sm:p-8"
            >
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">Solicitar Información</h2>
                    <p class="text-slate-600 mt-1">
                        Completa el formulario y nos pondremos en contacto contigo
                    </p>
                </div>

                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Tu Nombre Completo <span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="form.client_name"
                                type="text"
                                required
                                placeholder="Ej: Juan Pérez"
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                :class="{ 'border-red-500': form.errors.client_name }"
                            >
                            <p v-if="form.errors.client_name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.client_name }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Email o Teléfono de Contacto <span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="form.client_contact"
                                type="text"
                                required
                                placeholder="ejemplo@email.com o (555) 123-4567"
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                :class="{ 'border-red-500': form.errors.client_contact }"
                            >
                            <p v-if="form.errors.client_contact" class="mt-1 text-sm text-red-600">
                                {{ form.errors.client_contact }}
                            </p>
                        </div>
                    </div>

                    <!-- Dynamic Form Fields -->
                    <div v-if="offering.form_schema?.length" class="pt-6 border-t-2 border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">
                            Información Adicional
                        </h3>
                        <DynamicForm 
                            :schema="offering.form_schema"
                            v-model="form.form_data"
                            :errors="form.errors"
                        />
                    </div>

                    <!-- Notes -->
                    <div class="pt-6 border-t-2 border-slate-100">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Comentarios o Preguntas (Opcional)
                        </label>
                        <textarea 
                            v-model="form.notes"
                            rows="4"
                            placeholder="Cuéntanos más sobre lo que necesitas..."
                            class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"
                        ></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 pt-6 border-t-2 border-slate-100">
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 px-8 rounded-xl font-semibold text-lg hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        <span v-if="form.processing">Enviando...</span>
                        <span v-else>Enviar Solicitud →</span>
                    </button>
                    
                    <p class="mt-4 text-xs text-center text-slate-500">
                        Al enviar, aceptas ser contactado por 
                        <span class="font-medium">{{ offering.owner?.name || 'nuestro equipo' }}</span>
                        respecto a esta solicitud.
                    </p>
                </div>
            </form>

            <!-- After Success - CTA -->
            <div v-if="success" class="text-center py-8">
                <a 
                    :href="route('home')"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg"
                >
                    <ArrowLeft :size="20" />
                    Volver al Inicio
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-900 text-white py-8 mt-16">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <p class="text-slate-400 text-sm">
                    © {{ new Date().getFullYear() }} PSRefer. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}
</style>
