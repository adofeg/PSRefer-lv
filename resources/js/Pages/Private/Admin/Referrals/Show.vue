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
    Calendar, DollarSign, Briefcase, CheckCircle, LayoutDashboard, FileInput, MapPin, Info, Hash
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { useFormatters } from '@/Composables/useFormatters';
import { normalizeResource } from '@/Utils/inertia';

const props = defineProps({
    referral: Object,
    auth: Object
});

const { formatCurrency, formatDate } = useFormatters();
const referral = normalizeResource(props.referral, {});

const showStatusModal = ref(false);
const activeTab = ref('summary');

const iconMap = {
    user: User,
    briefcase: Briefcase,
    'map-pin': MapPin,
    file: FileText,
    check: CheckCircle,
    info: Info,
    hash: Hash,
    phone: Phone,
    mail: Mail
};

const getGroupIcon = (name) => iconMap[name] || FileText;

const excludeKeys = ['_schema_version', 'client_name', 'client_email', 'client_phone', 'client_contact', 'client_state', 'origen', 'sector_id'];

const hasValue = (value) => {
    return value !== null && value !== undefined && value !== '';
};

const filteredSchemaGroups = computed(() => {
    if (!referral.offering?.form_schema?.groups) return [];
    
    return referral.offering.form_schema.groups.map((group, index) => {
        const hasVisibleFields = group.fields.some(field => {
            return !excludeKeys.includes(field.name) && hasValue(referral.metadata?.[field.name]);
        });
        return { ...group, originalIndex: index, hasVisibleFields };
    }).filter(group => group.hasVisibleFields);
});

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
            <div class="flex flex-wrap items-center gap-1 bg-slate-100/50 p-1 rounded-xl w-fit border border-slate-200">
                <button 
                    @click="activeTab = 'summary'"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200"
                    :class="activeTab === 'summary' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'"
                >
                    <LayoutDashboard :size="16" />
                    Resumen
                </button>
                
                <!-- Dynamic Tabs from Schema Groups -->
                <template v-if="filteredSchemaGroups.length > 0">
                    <button 
                        v-for="group in filteredSchemaGroups" 
                        :key="group.originalIndex"
                        @click="activeTab = `group_${group.originalIndex}`"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200"
                        :class="activeTab === `group_${group.originalIndex}` ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'"
                    >
                        <component :is="getGroupIcon(group.icon)" :size="16" />
                        {{ group.name }}
                    </button>
                </template>
                
                <!-- Fallback Tab for Flat Schema Data -->
                <template v-else-if="Object.keys(referral.metadata || {}).filter(k => !excludeKeys.includes(k)).length > 0">
                    <button 
                        @click="activeTab = 'data'"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200"
                        :class="activeTab === 'data' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'"
                    >
                        <FileInput :size="16" />
                        Datos del Referido
                    </button>
                </template>
            </div>

            <!-- Unified Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column (2/3): Core Information -->
                <div class="lg:col-span-2 space-y-8 animate-fade-in">
                    
                    <template v-if="activeTab === 'summary'">
                        <!-- Basic Contact Info (Previously excluded) -->
                        <Card class="overflow-hidden relative ring-1 ring-slate-100 shadow-sm pt-2">
                            <div class="absolute top-0 right-0 p-6 opacity-[0.03] pointer-events-none">
                                <User :size="120" />
                            </div>
                            <div class="p-6 md:p-8">
                                <div class="flex items-center gap-2 mb-6 text-indigo-600 border-b border-slate-100 pb-4">
                                    <User :size="20" />
                                    <h3 class="font-bold text-base uppercase tracking-wide">Información de Contacto</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                                    <div>
                                        <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-2">
                                            <Mail class="w-3.5 h-3.5" /> Correo Electrónico
                                        </dt>
                                        <dd class="text-sm font-bold text-slate-800">
                                            <a v-if="referral.client_email" :href="`mailto:${referral.client_email}`" class="hover:text-indigo-600 transition">{{ referral.client_email }}</a>
                                            <span v-else class="text-slate-400 italic font-medium">No especificado</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-2">
                                            <Phone class="w-3.5 h-3.5" /> Teléfono
                                        </dt>
                                        <dd class="text-sm font-bold text-slate-800">
                                            <a v-if="referral.client_phone" :href="`tel:${referral.client_phone}`" class="hover:text-indigo-600 transition">{{ referral.client_phone }}</a>
                                            <span v-else class="text-slate-400 italic font-medium">No especificado</span>
                                        </dd>
                                    </div>
                                    <div v-if="referral.sector">
                                        <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-2">
                                            <MapPin class="w-3.5 h-3.5" /> Sector de Servicio
                                        </dt>
                                        <dd class="text-sm font-bold text-slate-800">
                                            {{ referral.sector.name }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-2">
                                            <MapPin class="w-3.5 h-3.5" /> Estado / Locación
                                        </dt>
                                        <dd class="text-sm font-bold text-slate-800">
                                            {{ referral.metadata?.client_state || 'No especificado' }}
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </Card>

                        <!-- Service Info -->
                        <Card class="overflow-hidden relative ring-1 ring-slate-100 shadow-sm pt-2">
                            <div class="absolute top-0 right-0 p-6 opacity-[0.03] pointer-events-none">
                                <Briefcase :size="120" />
                            </div>
                            <div class="p-6 md:p-8">
                                <div class="flex items-center gap-2 mb-6 text-indigo-600 border-b border-slate-100 pb-4">
                                    <Briefcase :size="20" />
                                    <h3 class="font-bold text-base uppercase tracking-wide">Servicio Solicitado & Origen</h3>
                                </div>
                                <div v-if="referral.offering" class="relative z-10">
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
                                <div v-else class="text-slate-400 italic py-8 text-center relative z-10">
                                    No se ha asignado un servicio específico.
                                </div>
                            </div>
                        </Card>
                    </template>

                    <template v-else-if="activeTab.startsWith('group_') && filteredSchemaGroups.length > 0">
                        <!-- Dynamic Group Content -->
                        <div v-for="group in filteredSchemaGroups" :key="'content_'+group.originalIndex">
                            <Card v-if="activeTab === `group_${group.originalIndex}`" class="overflow-hidden relative ring-1 ring-slate-100 shadow-sm pt-2">
                                <div class="absolute top-0 right-0 p-6 opacity-[0.03] pointer-events-none">
                                    <component :is="getGroupIcon(group.icon)" :size="120" />
                                </div>
                                <div class="p-6 md:p-8">
                                    <div class="flex items-center gap-2 mb-6 text-indigo-600 border-b border-slate-100 pb-4">
                                        <component :is="getGroupIcon(group.icon)" :size="20" />
                                        <h3 class="font-bold text-base uppercase tracking-wide">
                                            {{ group.name }}
                                        </h3>
                                    </div>
                                    <div class="relative z-10">
                                        <!-- Hide internal group visual from MetadataDisplay by wrapping schema in matching format -->
                                        <MetadataDisplay 
                                            :metadata="referral.metadata" 
                                            :schema="{ groups: [group] }"
                                            :files="referral.file_assets || []"
                                            :exclude-keys="excludeKeys" 
                                        />
                                    </div>
                                </div>
                            </Card>
                        </div>
                    </template>

                    <template v-else-if="activeTab === 'data'">
                        <!-- Fallback Dynamic Form Data (Metadata) -->
                        <Card class="overflow-hidden relative ring-1 ring-slate-100 shadow-sm pt-2">
                            <div class="absolute top-0 right-0 p-6 opacity-[0.03] pointer-events-none">
                                <FileInput :size="120" />
                            </div>
                            <div class="p-6 md:p-8">
                                <div class="flex items-center gap-2 mb-6 text-indigo-600 border-b border-slate-100 pb-4">
                                    <FileInput :size="20" />
                                    <h3 class="font-bold text-base uppercase tracking-wide">Información Adicional (Catálogo)</h3>
                                </div>
                                <div class="relative z-10">
                                    <MetadataDisplay 
                                        :metadata="referral.metadata" 
                                        :schema="referral.offering?.form_schema"
                                        :files="referral.file_assets || []"
                                        :exclude-keys="excludeKeys" 
                                    />
                                </div>
                            </div>
                        </Card>
                    </template>
                </div>

                <!-- Right Column (1/3): Timeline -->
                <div class="lg:col-span-1">
                    <Card class="flex flex-col ring-1 ring-slate-100 shadow-sm relative overflow-hidden h-fit">
                        <div class="absolute top-0 right-0 p-6 opacity-[0.03] pointer-events-none z-0">
                            <History :size="120" />
                        </div>
                        <div class="flex items-center gap-2 mb-4 p-6 md:p-8 pb-0 border-b border-transparent z-10">
                            <div class="p-1.5 bg-indigo-50 rounded text-indigo-600 inline-block">
                                <History :size="18" />
                            </div>
                            <h3 class="font-bold text-slate-800">Historial</h3>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full ml-auto">{{ referral.history?.length || 0 }}</span>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto px-6 md:px-8 pb-6 custom-scrollbar max-h-[600px] z-10">
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

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
