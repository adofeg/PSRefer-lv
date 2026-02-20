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
    Calendar, DollarSign, Briefcase, CheckCircle, LayoutDashboard, FileInput
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
const activeTab = ref('summary'); // 'summary', 'data'

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

            <!-- Tabs Navigation -->
            <div class="flex items-center gap-1 bg-slate-100/50 p-1 rounded-xl w-fit border border-slate-200">
                <button 
                    @click="activeTab = 'summary'"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200"
                    :class="activeTab === 'summary' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'"
                >
                    <LayoutDashboard :size="16" />
                    Resumen
                </button>
                <button 
                    @click="activeTab = 'data'"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200"
                    :class="activeTab === 'data' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'"
                >
                    <FileInput :size="16" />
                    Datos del Referido
                </button>
            </div>

            <!-- Tab Content -->
            <!-- Tab Content: Summary -->
            <div v-if="activeTab === 'summary'" class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in">
                
                <!-- Left Column (2/3): Service Info -->
                <div class="lg:col-span-2 space-y-8">
                    <Card class="overflow-hidden h-full">
                        <div class="p-6 flex flex-col h-full">
                             <div class="flex items-center gap-2 mb-6 text-indigo-600">
                                <Briefcase :size="20" />
                                <h3 class="font-bold text-base uppercase tracking-wide">Servicio de Interés</h3>
                            </div>
                            
                            <div v-if="referral.offering" class="flex-1">
                                <div class="text-2xl font-bold text-slate-800 mb-2 leading-tight">{{ referral.offering.name }}</div>
                                <div class="inline-block px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 rounded mb-6">
                                    {{ referral.offering.category }}
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-6 border-t border-slate-100">
                                    <div>
                                        <dt class="text-[10px] uppercase font-bold text-slate-400 mb-1.5">Tipo de Comisión</dt>
                                        <dd class="font-bold text-emerald-600 text-xl">
                                            {{ referral.offering.commission_type === 'percentage' ? `${referral.offering.base_commission}%` : formatCurrency(referral.offering.base_commission) }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-[10px] uppercase font-bold text-slate-400 mb-1.5">Fuente del Referido</dt>
                                        <dd v-if="referral.associate" class="font-medium text-slate-700 flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold ring-2 ring-white shadow-sm">
                                                {{ referral.associate.user?.name?.charAt(0) || 'A' }}
                                            </div>
                                            <div class="leading-tight">
                                                <div class="text-sm font-bold">{{ referral.associate.user?.name || 'Asociado' }}</div>
                                                <div class="text-[10px] text-slate-400">Asociado Certificado</div>
                                            </div>
                                        </dd>
                                        <dd v-else class="font-medium text-slate-700 flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-sm font-bold ring-2 ring-white shadow-sm">
                                                <LayoutDashboard :size="16" />
                                            </div>
                                            <div class="leading-tight">
                                                <div class="text-sm font-bold">Sistema / Directo</div>
                                                <div class="text-[10px] text-slate-400">Registro Interno</div>
                                            </div>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-slate-400 italic py-8 text-center">
                                No se ha asignado un servicio específico.
                            </div>
                        </div>
                    </Card>
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

            <!-- Tab Content: Data -->
            <div v-else-if="activeTab === 'data'" class="animate-fade-in">
                <Card class="p-8">
                    <MetadataDisplay 
                        :metadata="referral.metadata" 
                        :schema="referral.offering?.form_schema"
                        :files="referral.file_assets || []"
                        :exclude-keys="['_schema_version']" 
                    />
                </Card>
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

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
