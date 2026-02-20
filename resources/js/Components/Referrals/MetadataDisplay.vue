<script setup>
import { computed, ref, watch } from 'vue';
import { User, Briefcase, MapPin, FileText, CheckCircle, Info, Hash, Phone, Mail } from 'lucide-vue-next';

const props = defineProps({
    metadata: {
        type: Object,
        default: () => ({})
    },
    schema: {
        type: [Array, Object], // Optional schema for grouping
        default: null
    },
    files: {
        type: Array, // Array of FileAsset objects
        default: () => []
    },
    excludeKeys: {
        type: Array,
        default: () => ['client_name', 'client_email', 'client_phone', 'client_contact', 'client_state', 'origen']
    }
});

// Icon Mapping
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

const getIcon = (name) => iconMap[name] || FileText;

// ─── Helpers (must be before displayGroups) ───────────────────────────────────

const hasValue = (value) => {
    return value !== null && value !== undefined && value !== '';
};

const formatLabel = (key) => {
    return key
        .replace(/_/g, ' ')
        .replace(/([A-Z])/g, ' $1')
        .trim()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
};

const formatValue = (value, type = 'text') => {
    if (value === null || value === undefined || value === '') {
        return 'No especificado';
    }
    if (type === 'file') {
        const file = props.files.find(f => f.uuid === value);
        if (file) {
            return {
                is_file: true,
                name: file.original_name,
                url: `/storage/${file.path}`,
                mime: file.mime_type,
                size: file.size
            };
        }
        return 'Archivo no encontrado';
    }
    if (typeof value === 'boolean') return value ? 'Sí' : 'No';
    if (Array.isArray(value)) return value.join(', ');
    if (typeof value === 'object') return JSON.stringify(value, null, 2);
    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)) {
        try {
            return new Date(value).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
        } catch { return value; }
    }
    return value.toString();
};

// ─── Schema helpers ────────────────────────────────────────────────────────────

// Helper to check if schema is grouped
const isGrouped = computed(() => {
    return props.schema && !Array.isArray(props.schema) && Array.isArray(props.schema.groups);
});

// Compute groups for display
const displayGroups = computed(() => {
    if (!props.metadata) return [];

    let groups = [];
    const definedKeys = new Set();

    // 1. Process Defined Schema
    if (isGrouped.value) {
        groups = props.schema.groups.map(group => {
            const groupFields = group.fields
                .filter(field => !props.excludeKeys.includes(field.name))
                .map(field => {
                    definedKeys.add(field.name);
                    // Use schema label if available, fallback to formatting key
                    const displayLabel = field.label || formatLabel(field.name);
                    
                    return {
                        key: field.name,
                        label: displayLabel,
                        value: formatValue(props.metadata[field.name], field.type),
                        rawValue: props.metadata[field.name],
                        type: field.type,
                        is_file: field.type === 'file'
                    };
                });

            return {
                ...group,
                fields: groupFields
            };
        }).filter(group => group.fields.length > 0);
    } else {
        // Flat Schema (Legacy)
        const flatFields = Object.entries(props.metadata)
            .filter(([key]) => {
                return !props.excludeKeys.includes(key);
            })
            .map(([key, value]) => {
                definedKeys.add(key);
                return {
                    key,
                    label: formatLabel(key),
                    value: formatValue(value),
                    rawValue: value,
                    type: 'text', // Default
                    is_file: false
                };
            });
        
        if (flatFields.length) {
            groups.push({ id: 'default', fields: flatFields });
        }
    }

    // 2. Identify Orphan Fields (Data present but not in Schema)
    if (isGrouped.value) {
        const orphanFields = Object.entries(props.metadata)
            .filter(([key, value]) => {
                return !props.excludeKeys.includes(key) && 
                       !definedKeys.has(key) && 
                       hasValue(value);
            })
            .map(([key, value]) => ({
                key,
                label: formatLabel(key),
                value: formatValue(value),
                rawValue: value,
                type: 'text',
                is_file: false
            }));

        if (orphanFields.length > 0) {
            groups.push({
                id: 'orphans',
                title: 'Datos Adicionales (Histórico)',
                icon: 'info',
                isOrphan: true,
                fields: orphanFields
            });
        }
    }

    return groups;
});

// Active tab state: default to first group
const activeGroupId = ref(null);
watch(displayGroups, (groups) => {
    if (groups.length > 0 && !activeGroupId.value) {
        activeGroupId.value = groups[0].id;
    }
}, { immediate: true });

// Only use tabs when there are multiple groups
const useTabs = computed(() => isGrouped.value && displayGroups.value.length > 1);

</script>

<template>
    <div v-if="displayGroups.length">

        <!-- TAB NAVIGATION: only when multiple groups exist -->
        <div v-if="useTabs" class="flex items-center gap-1 bg-slate-100/60 p-1 rounded-xl w-full border border-slate-200 mb-8 flex-wrap">
            <button
                v-for="group in displayGroups"
                :key="group.id"
                @click="activeGroupId = group.id"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-black transition-all duration-200 flex-1 min-w-max justify-center"
                :class="activeGroupId === group.id
                    ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5'
                    : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'"
            >
                <component :is="getIcon(group.icon)" :size="14" />
                <span class="truncate uppercase tracking-wider">{{ group.title || 'General' }}</span>
            </button>
        </div>

        <!-- GROUP CONTENT -->
        <div class="animate-fade-in" :key="activeGroupId">
            <template v-for="group in displayGroups" :key="group.id">
                <div v-if="!useTabs || activeGroupId === group.id">

                    <!-- Single-group header (no tabs mode) -->
                    <div v-if="!useTabs" class="flex items-center gap-3 mb-8">
                        <div class="p-2.5 bg-indigo-600 rounded-xl text-white shadow-sm">
                            <component :is="getIcon(group.icon)" :size="18" />
                        </div>
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-widest text-slate-800">{{ group.title || 'Información General' }}</h4>
                            <p v-if="group.description" class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-0.5">{{ group.description }}</p>
                        </div>
                    </div>

                    <!-- Fields Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-12 gap-y-8">
                        <div
                            v-for="{ key, label, value, type, is_file } in group.fields"
                            :key="key"
                            class="space-y-2"
                            :class="{ 'md:col-span-2 xl:col-span-3': is_file || type === 'textarea' }"
                        >
                            <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                                {{ label }}
                                <span v-if="group.isOrphan" class="px-2 py-0.5 rounded-md bg-amber-50 text-[8px] text-amber-600 font-black border border-amber-100">EXTERNO</span>
                            </dt>

                            <dd class="text-sm font-bold text-slate-700 leading-relaxed">
                                <!-- File -->
                                <div v-if="is_file && value.url" class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-200/60 hover:border-indigo-300 hover:bg-indigo-50/30 transition-all group cursor-pointer">
                                    <div class="p-2.5 bg-white rounded-xl border border-slate-200 text-indigo-600 group-hover:rotate-3 transition-transform flex-shrink-0">
                                        <FileText :size="20" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <a :href="value.url" target="_blank" class="block text-sm font-black text-slate-900 truncate group-hover:text-indigo-700 transition-colors">
                                            {{ value.name }}
                                        </a>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-[9px] font-black uppercase tracking-tight text-slate-400">{{ (value.size / 1024).toFixed(1) }} KB</span>
                                            <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                            <span class="text-[9px] font-black uppercase tracking-tight text-indigo-400">{{ value.mime.split('/')[1] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Textarea -->
                                <div v-else-if="type === 'textarea'" class="bg-slate-50/50 p-5 rounded-2xl border border-slate-100 text-slate-600 whitespace-pre-wrap font-medium text-xs leading-relaxed shadow-inner">
                                    {{ value }}
                                </div>

                                <!-- Standard -->
                                <div v-else class="flex items-center min-h-[2.5rem] px-4 bg-slate-50/30 rounded-xl border border-slate-100/50">
                                    <span v-if="value === 'No especificado'" class="text-slate-300 font-bold text-xs italic">{{ value }}</span>
                                    <span v-else class="text-slate-900 tracking-tight">{{ value }}</span>
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Version Badge -->
        <div v-if="metadata._schema_version" class="pt-8 flex justify-end">
            <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-slate-200 text-slate-400 shadow-sm">
                <Hash :size="12" class="opacity-40" />
                <span class="text-[10px] font-black uppercase tracking-widest">Estructura v{{ metadata._schema_version }}</span>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div v-else class="py-20 text-center">
        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-slate-100 shadow-inner">
            <Info :size="32" class="text-slate-200" />
        </div>
        <h4 class="text-xl font-black text-slate-800 mb-2">Sin datos adicionales</h4>
        <p class="text-sm font-medium text-slate-500 max-w-xs mx-auto">Este referido fue registrado con la información básica del sistema.</p>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.35s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
