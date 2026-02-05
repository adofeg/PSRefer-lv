<script setup>
import { ref, computed } from 'vue';
import { Trash2, Plus, MoveUp, MoveDown, GripVertical } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue']);

const schema = ref([...props.modelValue]);

const fieldTypes = [
    { value: 'text', label: 'Texto' },
    { value: 'email', label: 'Email' },
    { value: 'tel', label: 'Teléfono' },
    { value: 'number', label: 'Número' },
    { value: 'url', label: 'URL' },
    { value: 'select', label: 'Selector' },
    { value: 'textarea', label: 'Área de texto' },
    { value: 'date', label: 'Fecha' },
    { value: 'checkbox', label: 'Casilla de verificación' }
];

const addField = () => {
    schema.value.push({
        name: `field_${schema.value.length + 1}`,
        label: 'Nuevo Campo',
        type: 'text',
        required: false,
        placeholder: '',
        helpText: ''
    });
    emitUpdate();
};

const removeField = (index) => {
    schema.value.splice(index, 1);
    emitUpdate();
};

const moveUp = (index) => {
    if (index > 0) {
        const temp = schema.value[index];
        schema.value[index] = schema.value[index - 1];
        schema.value[index - 1] = temp;
        emitUpdate();
    }
};

const moveDown = (index) => {
    if (index < schema.value.length - 1) {
        const temp = schema.value[index];
        schema.value[index] = schema.value[index + 1];
        schema.value[index + 1] = temp;
        emitUpdate();
    }
};

const emitUpdate = () => {
    emit('update:modelValue', schema.value);
};

const parseOptions = (optionsString) => {
    if (!optionsString) return [];
    return optionsString.split('\n').filter(o => o.trim());
};

const formatOptions = (options) => {
    if (!options || !Array.isArray(options)) return '';
    return options.join('\n');
};

const updateFieldOptions = (index, value) => {
    schema.value[index].options = parseOptions(value);
    emitUpdate();
};

const schemaJson = computed(() => {
    return JSON.stringify(schema.value, null, 2);
});
</script>

<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-800">Constructor de Formulario</h3>
            <button 
                @click="addField"
                type="button"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
            >
                <Plus :size="20" />
                Agregar Campo
            </button>
        </div>

        <!-- Fields List -->
        <div class="space-y-4">
            <div 
                v-for="(field, index) in schema" 
                :key="index"
                class="bg-slate-50 border-2 border-slate-200 rounded-lg p-4 hover:border-indigo-300 transition"
            >
                <div class="flex items-start gap-4">
                    <!-- Drag Handle -->
                    <div class="pt-6 text-slate-400 cursor-move">
                        <GripVertical :size="20" />
                    </div>

                    <!-- Field Configuration -->
                    <div class="flex-1 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Field Name -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Nombre Interno *
                                </label>
                                <input 
                                    v-model="field.name"
                                    @input="emitUpdate"
                                    type="text"
                                    placeholder="ej: company_name"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                <p class="text-xs text-slate-500 mt-1">Sin espacios, solo letras, números y guión bajo</p>
                            </div>

                            <!-- Field Label -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Etiqueta Visible *
                                </label>
                                <input 
                                    v-model="field.label"
                                    @input="emitUpdate"
                                    type="text"
                                    placeholder="ej: Nombre de la Empresa"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <!-- Field Type -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Tipo de Campo</label>
                                <select 
                                    v-model="field.type"
                                    @change="emitUpdate"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option v-for="type in fieldTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Placeholder -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Placeholder</label>
                                <input 
                                    v-model="field.placeholder"
                                    @input="emitUpdate"
                                    type="text"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"
                                >
                            </div>

                            <!-- Required Checkbox -->
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input 
                                        v-model="field.required"
                                        @change="emitUpdate"
                                        type="checkbox"
                                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
                                    >
                                    <span class="text-sm font-medium text-slate-700">Requerido</span>
                                </label>
                            </div>
                        </div>

                        <!-- Options for Select Type -->
                        <div v-if="field.type === 'select'" class="mt-3">
                            <label class="block text-xs font-medium text-slate-700 mb-1">
                                Opciones (una por línea)
                            </label>
                            <textarea 
                                :value="formatOptions(field.options)"
                                @input="updateFieldOptions(index, $event.target.value)"
                                rows="4"
                                placeholder="Opción 1&#10;Opción 2&#10;Opción 3"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg font-mono"
                            ></textarea>
                        </div>

                        <!-- Number-specific options -->
                        <div v-if="field.type === 'number'" class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Mínimo</label>
                                <input 
                                    v-model.number="field.min"
                                    @input="emitUpdate"
                                    type="number"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Máximo</label>
                                <input 
                                    v-model.number="field.max"
                                    @input="emitUpdate"
                                    type="number"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Paso</label>
                                <input 
                                    v-model="field.step"
                                    @input="emitUpdate"
                                    type="text"
                                    placeholder="any"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"
                                >
                            </div>
                        </div>

                        <!-- Help Text -->
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Texto de Ayuda</label>
                            <input 
                                v-model="field.helpText"
                                @input="emitUpdate"
                                type="text"
                                placeholder="Texto informativo que aparece debajo del campo"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"
                            >
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2 pt-6">
                        <button 
                            @click="moveUp(index)"
                            :disabled="index === 0"
                            type="button"
                            class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded disabled:opacity-30 disabled:cursor-not-allowed transition"
                            title="Mover arriba"
                        >
                            <MoveUp :size="18" />
                        </button>
                        <button 
                            @click="moveDown(index)"
                            :disabled="index === schema.length - 1"
                            type="button"
                            class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded disabled:opacity-30 disabled:cursor-not-allowed transition"
                            title="Mover abajo"
                        >
                            <MoveDown :size="18" />
                        </button>
                        <button 
                            @click="removeField(index)"
                            type="button"
                            class="p-2 text-red-600 hover:bg-red-50 rounded transition"
                            title="Eliminar campo"
                        >
                            <Trash2 :size="18" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="schema.length === 0" class="text-center py-12 bg-slate-50 rounded-lg border-2 border-dashed border-slate-300">
                <p class="text-slate-600 mb-4">No hay campos configurados</p>
                <button 
                    @click="addField"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                >
                    <Plus :size="20" />
                    Agregar Primer Campo
                </button>
            </div>
        </div>

        <!-- JSON Preview (optional, can be toggled) -->
        <details class="bg-slate-100 rounded-lg p-4">
            <summary class="cursor-pointer font-medium text-slate-700">Ver JSON del Schema</summary>
            <pre class="mt-3 p-3 bg-slate-800 text-green-400 rounded text-xs overflow-x-auto">{{ schemaJson }}</pre>
        </details>
    </div>
</template>
