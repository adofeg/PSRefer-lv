<script setup>
import { computed } from 'vue';
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

// Helper to check if schema is grouped
const isGrouped = computed(() => {
    return props.schema && !Array.isArray(props.schema) && props.schema.version === '2.0' && Array.isArray(props.schema.groups);
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
                .filter(field => !props.excludeKeys.includes(field.name) && hasValue(props.metadata[field.name]))
                .map(field => {
                    definedKeys.add(field.name);
                    return {
                        key: field.name,
                        label: field.label || formatLabel(field.name),
                        value: formatValue(props.metadata[field.name]),
                        rawValue: props.metadata[field.name]
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
                // Should optimally check against schema definition if available, 
                // but for flat schema generic rendering simplifies things.
                // We'll mark all as defined if we're in flat mode without explicit schema array map
                return !props.excludeKeys.includes(key);
            })
            .map(([key, value]) => {
                definedKeys.add(key);
                return {
                    key,
                    label: formatLabel(key),
                    value: formatValue(value),
                    rawValue: value
                };
            });
        
        if (flatFields.length) {
            groups.push({ id: 'default', fields: flatFields });
        }
    }

    // 2. Identify Orphan Fields (Data present but not in Schema)
    // Only applies if we have a robust schema to check against (Grouped Mode)
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
                rawValue: value
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

const hasValue = (value) => {
    return value !== null && value !== undefined && value !== '';
};

// Format key to human-readable label
const formatLabel = (key) => {
    return key
        .replace(/_/g, ' ')
        .replace(/([A-Z])/g, ' $1')
        .trim()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
};

// Format value based on type
const formatValue = (value) => {
    if (value === null || value === undefined || value === '') {
        return 'No especificado';
    }
    
    if (typeof value === 'boolean') {
        return value ? 'Sí' : 'No';
    }
    
    if (Array.isArray(value)) {
        return value.join(', ');
    }
    
    if (typeof value === 'object') {
        return JSON.stringify(value, null, 2);
    }
    
    // Check if it's a date-like string
    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)) {
        try {
            const date = new Date(value);
            return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
        } catch {
            return value;
        }
    }
    
    return value.toString();
};
</script>

<template>
    <div v-if="displayGroups.length" class="space-y-6">
        <div v-for="group in displayGroups" :key="group.id">
            <!-- Group Header (Only if grouped schema is used) -->
            <div v-if="group.title" class="flex items-center gap-2 mb-3 pb-2 border-b border-indigo-100">
                <component :is="getIcon(group.icon)" class="w-4 h-4 text-indigo-500" />
                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-900">{{ group.title }}</h4>
            </div>

            <!-- Fields Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div 
                    v-for="{ key, label, value } in group.fields" 
                    :key="key"
                    class="bg-gradient-to-br from-slate-50 to-white p-4 rounded-lg border border-slate-200 hover:border-indigo-300 hover:shadow-sm transition-all duration-200"
                >
                    <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full" :class="group.isOrphan ? 'bg-amber-400' : 'bg-indigo-400'"></span>
                        {{ label }}
                    </dt>
                    <dd class="text-sm font-semibold text-slate-800 leading-relaxed break-words">
                        {{ value }}
                    </dd>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="text-center py-8 text-slate-400 text-sm italic">
        No hay información adicional específica del servicio.
    </div>
    
    <!-- Historical Version Badge -->
    <div v-if="metadata._schema_version" class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
         <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500" title="Versión del formulario al momento de la captura">
            <FileText :size="10" />
            Versión {{ metadata._schema_version }}
        </span>
    </div>
</template>
