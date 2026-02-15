<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Plus, Trash, FileText, Layout, GripVertical, ChevronDown, ChevronUp, Copy, CheckCircle } from 'lucide-vue-next';
import draggable from 'vuedraggable';

const props = defineProps({
    modelValue: {
        type: [Array, Object],
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue']);

// Local state: Always Grouped (v2.0)
const localGroups = ref([]); 
const isInitialized = ref(false);
const wasMigrated = ref(false);
const currentVersion = ref(1);

// Initialize from Props
const init = () => {
    // Standard system fields
    const systemFields = [
        { name: 'client_name', label: 'Nombre Completo', type: 'text', required: true, is_system: true },
        { name: 'client_email', label: 'Correo Electrónico', type: 'email', required: true, is_system: true },
        { name: 'client_phone', label: 'Teléfono', type: 'tel', required: true, is_system: true },
    ];

    if (!props.modelValue || (Array.isArray(props.modelValue) && props.modelValue.length === 0)) {
        localGroups.value = [{
            id: 'group_identity',
            title: 'Datos Personales',
            fields: systemFields
        }];
        isInitialized.value = true;
        return;
    }

    // Capture version
    if (props.modelValue.version) {
        currentVersion.value = props.modelValue.version;
    }

    // Case 1: Already v2.0 Grouped Schema
    if (!Array.isArray(props.modelValue) && props.modelValue?.groups) {
        let groups = JSON.parse(JSON.stringify(props.modelValue.groups || []));
        
        // Ensure first group starts with system fields if missing
        if (groups.length > 0) {
            const firstGroupFields = groups[0].fields || [];
            const hasSystem = firstGroupFields.some(f => f.is_system);
            if (!hasSystem) {
                groups[0].fields = [...systemFields, ...firstGroupFields];
            }
        } else {
            groups = [{ id: 'group_identity', title: 'Datos Personales', fields: systemFields }];
        }
        localGroups.value = groups;
    } 
    // Case 2: Legacy Array Schema -> Auto-Migrate
    else if (Array.isArray(props.modelValue) && props.modelValue.length > 0) {
        localGroups.value = [{
            id: 'group_legacy_migrated',
            title: 'Datos Personales',
            fields: [...systemFields, ...JSON.parse(JSON.stringify(props.modelValue))]
        }];
        wasMigrated.value = true;
    }
    
    isInitialized.value = true;
};

// Reserved System Field Names
const reservedNames = ['client_name', 'client_email', 'client_phone'];

// Sync back to parent (Always emit v2.0 structure but preserve revision for backend logic)
watch(localGroups, () => {
    if (!isInitialized.value) return;

    // Filter out redundant fields that might have been added manually or from old seeders
    const cleanedGroups = localGroups.value.map(group => ({
        ...group,
        fields: group.fields.filter(f => !reservedNames.includes(f.name))
    }));

    emit('update:modelValue', {
        version: currentVersion.value,
        groups: cleanedGroups
    });
}, { deep: true });

onMounted(() => {
    init();
});

// Watch for external changes (only if not initialized or significantly different)
watch(() => props.modelValue, (newVal) => {
    if (!isInitialized.value && newVal) {
        init();
    }
}, { deep: true });

// Component Logic
const createField = (groupIndex, fieldIndex) => {
    // Generate sequential ID: field_GG_FF (Group Index 1-based, Field Index 1-based)
    const gId = (groupIndex + 1).toString().padStart(2, '0');
    const fId = (fieldIndex + 1).toString().padStart(2, '0');
    return {
        name: `field_${gId}_${fId}`, 
        label: '', 
        type: 'text', 
        required: false,
        options: '',
        placeholder: ''
    };
};

// Group Actions
const addGroup = () => {
    const index = localGroups.value.length + 1;
    const gId = index.toString().padStart(2, '0');
    localGroups.value.push({
        id: `group_${gId}`,
        title: '',
        fields: []
    });
};
const removeGroup = (index) => {
    if (confirm('¿Eliminar esta sección y todos sus campos?')) {
        localGroups.value.splice(index, 1);
    }
};

// Field in Group Actions
const addFieldToGroup = (groupIndex) => {
    if (!localGroups.value[groupIndex].fields) localGroups.value[groupIndex].fields = [];
    const fieldIndex = localGroups.value[groupIndex].fields.length;
    localGroups.value[groupIndex].fields.push(createField(groupIndex, fieldIndex));
};
const removeFieldFromGroup = (groupIndex, fieldIndex) => {
    localGroups.value[groupIndex].fields.splice(fieldIndex, 1);
};
const duplicateField = (groupIndex, fieldIndex) => {
    const field = localGroups.value[groupIndex].fields[fieldIndex];
    const newField = JSON.parse(JSON.stringify(field));
    // Update ID to ensure uniqueness
    const newIndices = localGroups.value[groupIndex].fields.length;
    const gId = (groupIndex + 1).toString().padStart(2, '0');
    const fId = (newIndices + 1).toString().padStart(2, '0');
    newField.name = `field_${gId}_${fId}`;
    
    localGroups.value[groupIndex].fields.splice(fieldIndex + 1, 0, newField);
};

</script>

<template>
    <div class="space-y-8 animate-fade-in">
        <!-- Version Indicator (Only if migrated) -->
        <div v-if="wasMigrated" class="bg-amber-50/50 border border-amber-200/50 text-amber-800 px-6 py-4 rounded-2xl flex items-center gap-4 text-sm shadow-sm">
            <div class="bg-amber-100 p-2 rounded-xl shrink-0">
                <FileText :size="18" class="text-amber-600" />
            </div>
            <div>
                <span class="font-black uppercase tracking-tight block text-xs mb-1">Formulario Migrado</span>
                <span class="text-xs opacity-80 leading-relaxed">Este formulario proviene de una versión anterior (v1.0). Se ha convertido automáticamente al nuevo formato de secciones.</span>
            </div>
        </div>


        <!-- Empty State (Only if no custom sections) -->
         <div v-if="localGroups.length === 0" class="text-center py-20 bg-slate-50/50 rounded-3xl border-dashed border-2 border-slate-200 text-slate-400 text-sm flex flex-col items-center gap-4 shadow-inner">
            <Layout :size="48" class="text-slate-300 opacity-50" />
            <div class="space-y-1">
                <p class="font-bold text-slate-500 text-lg">Campos Dinámicos</p>
                <p class="text-xs">No hay secciones personalizadas aún. Crea una para pedir detalles específicos del servicio.</p>
            </div>
            <button @click="addGroup" type="button" class="mt-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 flex items-center gap-2">
                <Plus :size="16" /> Agregar Sección Personalizada
            </button>
        </div>

        <!-- Groups List (Sections) -->
        <draggable 
            v-model="localGroups" 
            item-key="id" 
            handle=".section-handle"
            ghost-class="opacity-50"
            class="space-y-8"
        >
            <template #item="{ element: group, index: gIndex }">
                <div class="border border-slate-200/60 rounded-3xl overflow-hidden bg-white shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
                    <!-- Group Header Editor -->
                    <div class="bg-slate-50/80 backdrop-blur-sm p-5 border-b border-slate-100 flex flex-col md:flex-row gap-4 items-center justify-between group-header">
                         <div class="flex items-center gap-4 flex-1 w-full">
                             <!-- Section Reorder Handle -->
                             <div class="section-handle p-2.5 cursor-grab active:cursor-grabbing text-slate-300 hover:text-indigo-500 transition-all bg-white rounded-xl border border-slate-100 shadow-sm">
                                 <GripVertical :size="20" />
                             </div>
                             <div class="flex-1">
                                 <input v-model="group.title" type="text" class="w-full text-xl font-black bg-transparent border-0 border-b-2 border-transparent focus:border-indigo-500 focus:ring-0 px-0 placeholder-slate-300 transition-all tracking-tight text-slate-800" placeholder="Nombre de la Sección (Ej: Información del Vehículo)" />
                             </div>
                         </div>
                          <div class="flex items-center gap-2 pt-4 md:pt-0 shrink-0">
                             <!-- Show icon/label for Step 1 (Identity) -->
                             <span v-if="gIndex === 0" class="px-3 py-1 bg-indigo-50 text-indigo-500 rounded-lg text-[10px] font-black uppercase tracking-tight border border-indigo-100 flex items-center gap-2 mr-2">
                                <CheckCircle :size="12" /> Identidad Fija
                             </span>
                             <button 
                                v-if="localGroups.length > 1"
                                @click="removeGroup(gIndex)" 
                                type="button" 
                                class="p-2.5 text-slate-300 hover:text-red-500 rounded-xl hover:bg-red-50 transition-all" 
                                title="Eliminar Sección"
                             >
                                <Trash :size="20" />
                             </button>
                          </div>
                     </div>

                     <!-- Group Fields -->
                     <div class="p-6 bg-white space-y-6">
                        <draggable 
                            v-model="group.fields" 
                            item-key="name" 
                            handle=".field-handle"
                            ghost-class="opacity-50"
                            class="space-y-4"
                        >
                            <template #item="{ element: field, index: fIndex }">
                                <div class="bg-white p-5 rounded-2xl border border-slate-100 hover:border-indigo-200 transition-all shadow-sm hover:shadow-md relative group/field"
                                     :class="{ 'border-indigo-50 bg-indigo-50/5 opacity-80': field.is_system }">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                                        <!-- Field Move Handle (Hide for system fields to keep them at top) -->
                                        <div class="md:col-span-1 flex items-center justify-center pt-8">
                                            <div v-if="!field.is_system" class="field-handle p-2.5 cursor-grab active:cursor-grabbing text-slate-200 hover:text-indigo-400 transition-colors bg-slate-50 rounded-lg border border-slate-100">
                                                <GripVertical :size="18" />
                                            </div>
                                            <div v-else class="p-2.5 text-indigo-300 bg-indigo-50/50 rounded-lg border border-indigo-100 opacity-50 cursor-not-allowed">
                                                <CheckCircle :size="18" />
                                            </div>
                                        </div>

                                        <!-- Field Editor -->
                                        <div class="md:col-span-5">
                                            <label class="block text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2">Pregunta / Etiqueta</label>
                                            <input 
                                                v-model="field.label" 
                                                :disabled="field.is_system"
                                                type="text" 
                                                class="w-full text-sm border-slate-200 rounded-xl p-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white transition-all font-medium disabled:bg-slate-50 disabled:text-slate-400" 
                                                placeholder="Escribe la pregunta..." 
                                            />
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2">Tipo de Entrada</label>
                                            <div v-if="field.is_system" class="w-full text-xs font-bold text-indigo-400 px-1 pt-3 italic">Campo del Sistema</div>
                                            <select v-else v-model="field.type" class="w-full text-sm border-slate-200 rounded-xl p-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white cursor-pointer font-medium text-slate-700">
                                                <option value="text">Texto Corto</option>
                                                <option value="textarea">Párrafo de Texto</option>
                                                <option value="email">Correo Electrónico</option>
                                                <option value="tel">Teléfono</option>
                                                <option value="number">Número</option>
                                                <option value="date">Fecha</option>
                                                <option value="select">Lista Desplegable</option>
                                                <option value="checkbox">Casilla de Verificación</option>
                                                <option value="file">Subida de Archivo</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-3 flex items-center justify-end gap-3 pt-8">
                                            <div v-if="!field.is_system" class="flex items-center gap-3 bg-slate-50/50 px-3 py-2 rounded-xl border border-slate-100 mr-2">
                                               <label class="flex items-center gap-2 cursor-pointer select-none">
                                                   <div class="relative">
                                                       <input v-model="field.required" type="checkbox" class="peer sr-only" />
                                                       <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                                   </div>
                                                   <span class="text-xs font-black uppercase tracking-tighter text-slate-400 peer-checked:text-indigo-600 transition-colors">Requerido</span>
                                               </label>
                                            </div>
                                            
                                            <div v-if="!field.is_system" class="flex items-center gap-1.5">
                                                <button @click="duplicateField(gIndex, fIndex)" type="button" class="p-2.5 text-slate-300 hover:text-indigo-600 rounded-xl hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100" title="Duplicar">
                                                    <Copy :size="18" />
                                                </button>
                                                <button @click="removeFieldFromGroup(gIndex, fIndex)" type="button" class="p-2.5 text-slate-300 hover:text-red-600 rounded-xl hover:bg-red-50 transition-all border border-transparent hover:border-red-100" title="Eliminar">
                                                    <Trash :size="18" />
                                                </button>
                                            </div>
                                            <div v-else class="mr-4">
                                                <span class="text-[9px] font-black uppercase tracking-widest text-indigo-300 border border-indigo-100 px-2 py-1 rounded bg-indigo-50/50">Bloqueado</span>
                                            </div>
                                        </div>
                                     </div>

                                     <!-- Conditional Options -->
                                     <div v-if="['select', 'radio', 'checkbox'].includes(field.type)" class="mt-6 pl-14">
                                         <div class="bg-indigo-50/30 p-4 rounded-2xl border border-indigo-100/50 space-y-3">
                                            <label class="block text-[10px] uppercase font-black tracking-widest text-indigo-400">Opciones de la Lista (separadas por coma)</label>
                                            <input v-model="field.options" type="text" class="w-full text-sm border-slate-200 rounded-xl p-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white" placeholder="Opción 1, Opción 2, Opción 3" />
                                         </div>
                                     </div>
                                </div>
                            </template>
                        </draggable>

                        <!-- Add Field Button -->
                        <div class="pt-4">
                            <button @click="addFieldToGroup(gIndex)" type="button" class="w-full py-4 bg-slate-50/50 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all duration-300 font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3 group/btn">
                                <div class="bg-white p-2 rounded-xl shadow-sm text-slate-300 group-hover/btn:text-indigo-600 transition-all group-hover/btn:scale-110">
                                    <Plus :size="18" />
                                </div>
                                Agregar Pregunta a esta sección
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </draggable>

        <!-- Add Section Button -->
        <div class="pt-4">
            <button @click="addGroup" type="button" class="w-full py-6 bg-indigo-50/20 border-2 border-dashed border-indigo-200 rounded-3xl text-indigo-600 font-black text-xs uppercase tracking-widest hover:bg-indigo-50/50 transition-all duration-300 shadow-sm flex flex-col items-center justify-center gap-3 group">
                <div class="bg-white p-3 rounded-2xl shadow-md text-indigo-600 group-hover:scale-110 transition-transform border border-indigo-100">
                    <Plus :size="24" />
                </div>
                Crear Nueva Sección del Formulario
            </button>
        </div>
    </div>
</template>
