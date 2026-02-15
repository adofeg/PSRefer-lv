<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';
import StatusChangeModal from '@/Components/Referrals/StatusChangeModal.vue';
import AuditTimeline from '@/Components/Referrals/AuditTimeline.vue';
import MetadataDisplay from '@/Components/Referrals/MetadataDisplay.vue';
import { 
    ArrowLeft, History, User, Phone, Mail, FileText, 
    Calendar, DollarSign, Briefcase, CheckCircle 
} from 'lucide-vue-next';
import { ref } from 'vue';
import { useFormatters } from '@/Composables/useFormatters';
import { normalizeResource } from '@/Utils/inertia';

const props = defineProps({
    referral: Object,
    auth: Object
});

const { formatCurrency, formatDate } = useFormatters();
const referral = normalizeResource(props.referral, {});

const showStatusModal = ref(false);

const handleStatusUpdated = () => {
    showStatusModal.value = false;
};
</script>

<template>
    <Head :title="`Referido: ${referral.client_name || 'Sin Nombre'}`" />

    <AppLayout>
        <div class="w-full space-y-6">
            <!-- Premium Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 pb-6">
                <div class="flex items-start gap-4">
                    <Link :href="route('admin.referrals.index')" 
                        class="p-2.5 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 rounded-xl transition shadow-sm group"
                        title="Volver a la lista"
                    >
                        <ArrowLeft :size="20" class="text-slate-400 group-hover:text-slate-600 transition" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ referral.client_name || 'Sin Nombre' }}</h1>
                            <Badge :status="referral.status" class="px-3 py-1 text-sm shadow-sm" />
                        </div>
                        <div class="flex items-center gap-4 text-sm text-slate-500">
                            <span class="flex items-center gap-1.5">
                                <Calendar :size="14" />
                                Creado el {{ formatDate(referral.created_at) }}
                            </span>
                            <span v-if="referral.code" class="flex items-center gap-1.5 font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-600">
                                <FileText :size="12" />
                                {{ referral.code }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-6">
                    <!-- Global System Details (Header) -->
                    <div class="hidden md:flex items-center gap-6 text-sm text-slate-500 border-r border-slate-200 pr-6">
                        <div class="text-right">
                             <div class="text-xs uppercase font-bold tracking-wider text-slate-400 mb-0.5">ID Referencia</div>
                             <div class="font-mono font-bold text-slate-700 text-base">#{{ referral.id }}</div>
                        </div>
                        <div class="text-right">
                             <div class="text-xs uppercase font-bold tracking-wider text-slate-400 mb-0.5">Actualizado</div>
                             <div class="font-medium text-slate-700">{{ formatDate(referral.updated_at) }}</div>
                        </div>
                    </div>

                    <button 
                        v-if="['admin', 'psadmin'].includes($page.props.auth.user.role)"
                        @click="showStatusModal = true" 
                        class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 transition font-medium shadow-md shadow-indigo-100 flex items-center gap-2"
                    >
                        <CheckCircle :size="18" />
                        Gestionar Estado
                    </button>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column (2/3): Info + Financials -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Consolidated Info Card -->
                    <Card class="overflow-hidden">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-0">
                            <!-- Left: Client Info (2/3) -->
                            <div class="md:col-span-2 p-0 md:pr-8 md:border-r border-slate-100">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                        <User :size="20" />
                                    </div>
                                    <h3 class="font-bold text-lg text-slate-800">Información del Cliente</h3>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                                    <div class="space-y-1">
                                        <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nombre Completo</dt>
                                        <dd class="text-base font-medium text-slate-900">{{ referral.client_name }}</dd>
                                    </div>

                                    <div class="space-y-1">
                                        <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Contacto Principal</dt>
                                        <dd class="text-base font-medium text-slate-900 flex items-center gap-2">
                                            <Phone v-if="referral.client_phone" :size="16" class="text-slate-400" />
                                            {{ referral.client_contact || 'No registrado' }}
                                        </dd>
                                    </div>

                                    <div class="space-y-1">
                                        <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Correo Electrónico</dt>
                                        <dd class="text-base font-medium text-slate-900 flex items-center gap-2">
                                            <Mail v-if="referral.client_email" :size="16" class="text-slate-400" />
                                            {{ referral.client_email || 'No registrado' }}
                                        </dd>
                                    </div>

                                    <div v-if="referral.metadata?.client_state" class="space-y-1">
                                        <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Estado / Ubicación</dt>
                                        <dd class="text-base font-medium text-slate-900">{{ referral.metadata.client_state }}</dd>
                                    </div>

                                    <div class="space-y-1">
                                        <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Notas / Comentarios</dt>
                                        <dd class="text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 text-sm leading-relaxed">
                                            {{ referral.notes || 'Sin notas adicionales.' }}
                                        </dd>
                                    </div>

                                    <!-- Dynamic Extra Fields -->
                                    <div v-if="referral.metadata && Object.keys(referral.metadata).filter(k => !['client_name', 'client_email', 'client_phone', 'client_state', 'origen', 'client_contact'].includes(k)).length" class="sm:col-span-2 mt-6">
                                         <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                            <FileText :size="14" class="text-indigo-500" />
                                            Información Específica del Servicio
                                         </dt>
                                         <MetadataDisplay 
                                            :metadata="referral.metadata" 
                                            :schema="referral.offering?.form_schema"
                                         />
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Service Info (1/3) -->
                            <div class="md:col-span-1 p-0 md:pl-8 pt-8 md:pt-0 border-t md:border-t-0 border-slate-100 flex flex-col justify-center">
                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
                                     <div class="flex items-center gap-2 mb-4 text-indigo-600">
                                        <Briefcase :size="18" />
                                        <h3 class="font-bold text-sm uppercase tracking-wide">Servicio de Interés</h3>
                                    </div>
                                    
                                    <div v-if="referral.offering">
                                        <div class="text-xl font-bold text-slate-800 mb-1 leading-tight">{{ referral.offering.name }}</div>
                                        <div class="inline-block px-2 text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 rounded mb-4">
                                            {{ referral.offering.category }}
                                        </div>

                                        <div class="space-y-2 pt-4 border-t border-slate-200">
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-slate-500">Comisión Est.</span>
                                                <span class="font-bold text-emerald-600 px-1 py-0.5 bg-emerald-50 rounded">
                                                    {{ referral.offering.commission_type === 'percentage' ? `${referral.offering.base_commission}%` : formatCurrency(referral.offering.base_commission) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                     <div v-else class="text-slate-400 italic text-sm text-center py-4">
                                        Oferta no asignada.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Card>
                    
                    <!-- Financial Stats -->
                    <div v-if="referral.status === 'Cerrado' || referral.status === 'Venta' || referral.contract_id" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Revenue Card -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex flex-col justify-between relative overflow-hidden group">
                           <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                                <DollarSign :size="48" class="text-emerald-500" />
                           </div>
                           <dt class="text-sm font-medium text-slate-500 mb-1">Venta Total</dt>
                           <dd class="text-2xl font-bold text-emerald-600">{{ formatCurrency(referral.revenue_generated) }}</dd>
                           <div class="h-1 w-full bg-emerald-100 mt-3 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 w-full"></div>
                           </div>
                        </div>

                         <!-- Initial Payment Card -->
                         <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex flex-col justify-between">
                           <dt class="text-sm font-medium text-slate-500 mb-1">Pago Inicial</dt>
                           <dd class="text-2xl font-bold text-slate-800">{{ formatCurrency(referral.down_payment) }}</dd>
                           <div class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                                <CheckCircle :size="12" class="text-green-500" /> Confirmado
                           </div>
                        </div>

                         <!-- Contract ID Card -->
                         <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex flex-col justify-between">
                           <dt class="text-sm font-medium text-slate-500 mb-1">Contrato</dt>
                           <dd class="text-xl font-bold text-indigo-600 font-mono">{{ referral.contract_id || '---' }}</dd>
                           <div class="text-xs text-slate-400 mt-2">Método: {{ referral.payment_method || 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (1/3): Timeline -->
                <div class="lg:col-span-1">
                    <Card class="flex flex-col h-full ring-4 ring-slate-50/50">
                        <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-3">
                            <div class="p-1.5 bg-slate-100 rounded text-slate-600">
                                <History :size="18" />
                            </div>
                            <h3 class="font-bold text-slate-800">Historial</h3>
                            <span class="text-xs text-slate-400 ml-auto bg-slate-50 px-2 py-0.5 rounded-full">{{ referral.history?.length || 0 }}</span>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar max-h-[600px]">
                           <AuditTimeline :history="referral.history || []" />
                        </div>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Status Change Modal -->
        <StatusChangeModal 
            :show="showStatusModal"
            :referral-id="referral.id"
            :current-status="referral.status"
            @close="showStatusModal = false"
            @updated="handleStatusUpdated"
        />
    </AppLayout>
</template>
