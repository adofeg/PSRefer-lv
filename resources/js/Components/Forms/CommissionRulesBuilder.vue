<script setup>
import { ref, computed, watch } from 'vue';
import { Trash2, Plus, MoveUp, MoveDown, Eye, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    baseRate: {
        type: Number,
        default: 0
    }
});

const emit = defineEmits(['update:modelValue']);

const rules = ref([...props.modelValue]);
const previewValue = ref(10000);

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
    rules.value = [...newValue];
}, { deep: true });

const fieldOptions = [
    { value: 'deal_value', label: 'Valor del Deal' },
    { value: 'revenue_generated', label: 'Revenue Generado' }
];

const operatorOptions = [
    { value: '>=', label: 'Mayor o igual (≥)' },
    { value: '<=', label: 'Menor o igual (≤)' },
    { value: '>', label: 'Mayor que (>)' },
    { value: '<', label: 'Menor que (<)' },
    { value: '==', label: 'Igual a (=)' },
    { value: '!=', label: 'Diferente de (≠)' }
];

const addRule = () => {
    rules.value.push({
        label: 'Nueva Regla',
        field: 'deal_value',
        operator: '>=',
        value: 0,
        base_commission: props.baseRate || 0,
        condition: 'deal_value >= 0'
    });
    emitUpdate();
};

const addDefaultRule = () => {
    rules.value.push({
        label: 'Regla por Defecto',
        condition: 'default',
        base_commission: props.baseRate || 0
    });
    emitUpdate();
};

const removeRule = (index) => {
    rules.value.splice(index, 1);
    emitUpdate();
};

const moveUp = (index) => {
    if (index > 0) {
        const temp = rules.value[index];
        rules.value[index] = rules.value[index - 1];
        rules.value[index - 1] = temp;
        emitUpdate();
    }
};

const moveDown = (index) => {
    if (index < rules.value.length - 1) {
        const temp = rules.value[index];
        rules.value[index] = rules.value[index + 1];
        rules.value[index + 1] = temp;
        emitUpdate();
    }
};

const updateCondition = (index) => {
    const rule = rules.value[index];
    if (rule.condition === 'default') {
        return;
    }
    
    // Build condition string
    rule.condition = `${rule.field} ${rule.operator} ${rule.value}`;
    emitUpdate();
};

const emitUpdate = () => {
    emit('update:modelValue', rules.value);
};

// Preview which rule would apply for a given value
const previewRule = computed(() => {
    if (rules.value.length === 0) {
        return {
            rate: props.baseRate,
            label: 'Base Rate',
            commission: (previewValue.value * props.baseRate) / 100
        };
    }
    
    for (const rule of rules.value) {
        if (rule.condition === 'default') {
            return {
                rate: rule.base_commission,
                label: rule.label,
                commission: (previewValue.value * rule.base_commission) / 100
            };
        }
        
        // Parse and evaluate condition
        const match = rule.condition.match(/(\w+)\s*(>=|<=|>|<|==|!=)\s*(\d+(?:\.\d+)?)/);
        if (match) {
            const [, field, operator, value] = match;
            const testValue = previewValue.value;
            const conditionValue = parseFloat(value);
            
            let passes = false;
            switch (operator) {
                case '>=': passes = testValue >= conditionValue; break;
                case '<=': passes = testValue <= conditionValue; break;
                case '>': passes = testValue > conditionValue; break;
                case '<': passes = testValue < conditionValue; break;
                case '==': passes = testValue == conditionValue; break;
                case '!=': passes = testValue != conditionValue; break;
            }
            
            if (passes) {
                return {
                    rate: rule.base_commission,
                    label: rule.label,
                    commission: (previewValue.value * rule.base_commission) / 100
                };
            }
        }
    }
    
    return {
        rate: props.baseRate,
        label: 'Base Rate (Fallback)',
        commission: (previewValue.value * props.baseRate) / 100
    };
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Reglas de Comisión</h3>
                <p class="text-sm text-slate-600 mt-1">
                    Define comisiones variables basadas en el valor del deal
                </p>
            </div>
            <div class="flex gap-2">
                <button 
                    @click="addRule"
                    type="button"
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm"
                >
                    <Plus :size="18" />
                    Agregar Regla
                </button>
                <button 
                    @click="addDefaultRule"
                    type="button"
                    class="flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition text-sm"
                >
                    <Plus :size="18" />
                    Regla por Defecto
                </button>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-3">
            <AlertCircle :size="20" class="text-blue-600 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">¿Cómo funcionan las reglas?</p>
                <p>Las reglas se evalúan en orden. La primera regla que coincida determina la comisión. Si ninguna coincide, se usa la tasa base ({{ baseRate }}%).</p>
            </div>
        </div>

        <!-- Rules List -->
        <div class="space-y-3">
            <div 
                v-for="(rule, index) in rules" 
                :key="index"
                class="bg-white border-2 border-slate-200 rounded-lg p-4 hover:border-indigo-300 transition"
            >
                <div class="flex gap-4">
                    <!-- Rule Configuration -->
                    <div class="flex-1 space-y-3">
                        <!-- Label -->
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">
                                Nombre de la Regla
                            </label>
                            <input 
                                v-model="rule.label"
                                @input="emitUpdate"
                                type="text"
                                placeholder="ej: Premium Tier"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                        </div>

                        <!-- Condition Builder (if not default) -->
                        <div v-if="rule.condition !== 'default'" class="grid grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Campo</label>
                                <select 
                                    v-model="rule.field"
                                    @change="updateCondition(index)"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option v-for="field in fieldOptions" :key="field.value" :value="field.value">
                                        {{ field.label }}
                                    </option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Operador</label>
                                <select 
                                    v-model="rule.operator"
                                    @change="updateCondition(index)"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option v-for="op in operatorOptions" :key="op.value" :value="op.value">
                                        {{ op.label }}
                                    </option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Valor</label>
                                <input 
                                    v-model.number="rule.value"
                                    @input="updateCondition(index)"
                                    type="number"
                                    step="0.01"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1 font-bold tracking-tight uppercase">Base Comisión</label>
                                <input 
                                    v-model.number="rule.base_commission"
                                    @input="emitUpdate"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>
                        </div>

                        <!-- Default Rule Display -->
                        <div v-else class="grid grid-cols-2 gap-3">
                            <div class="col-span-2 bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800">
                                <p class="font-medium">Regla por Defecto</p>
                                <p class="text-xs mt-1">Se aplica cuando ninguna otra regla coincide</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">% Comisión</label>
                                <input 
                                    v-model.number="rule.base_commission"
                                    @input="emitUpdate"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>
                        </div>

                        <!-- Condition Display -->
                        <div class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded font-mono">
                            Condición: <span class="font-semibold">{{ rule.condition }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2">
                        <button 
                            @click="moveUp(index)"
                            :disabled="index === 0"
                            type="button"
                            class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded disabled:opacity-30 disabled:cursor-not-allowed transition"
                            title="Mover arriba (mayor prioridad)"
                        >
                            <MoveUp :size="18" />
                        </button>
                        <button 
                            @click="moveDown(index)"
                            :disabled="index === rules.length - 1"
                            type="button"
                            class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded disabled:opacity-30 disabled:cursor-not-allowed transition"
                            title="Mover abajo (menor prioridad)"
                        >
                            <MoveDown :size="18" />
                        </button>
                        <button 
                            @click="removeRule(index)"
                            type="button"
                            class="p-2 text-red-600 hover:bg-red-50 rounded transition"
                            title="Eliminar regla"
                        >
                            <Trash2 :size="18" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="rules.length === 0" class="text-center py-12 bg-slate-50 rounded-lg border-2 border-dashed border-slate-300">
                <p class="text-slate-600 mb-2">No hay reglas configuradas</p>
                <p class="text-sm text-slate-500 mb-4">Se usará la tasa base de {{ baseRate }}% para todos los deals</p>
                <button 
                    @click="addRule"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                >
                    <Plus :size="20" />
                    Agregar Primera Regla
                </button>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg p-6 border-2 border-indigo-200">
            <div class="flex items-center gap-2 mb-4">
                <Eye :size="20" class="text-indigo-600" />
                <h4 class="font-semibold text-indigo-900">Vista Previa</h4>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-indigo-900 mb-2">
                        Simular Valor del Deal
                    </label>
                    <input 
                        v-model.number="previewValue"
                        type="number"
                        step="1000"
                        class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>
                
                <div class="bg-white rounded-lg p-4 border-2 border-indigo-300">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-xs text-slate-600 uppercase tracking-wide mb-1">Regla Aplicada</p>
                            <p class="text-lg font-bold text-indigo-900">{{ previewRule.label }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-600 uppercase tracking-wide mb-1">Tasa de Comisión</p>
                            <p class="text-lg font-bold text-indigo-600">{{ previewRule.rate }}%</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-600 uppercase tracking-wide mb-1">Comisión Total</p>
                            <p class="text-lg font-bold text-green-600">{{ formatCurrency(previewRule.commission) }}</p>
                        </div>
                    </div>
                </div>

                <!-- All Rules Preview Table -->
                <div v-if="rules.length > 0" class="mt-4">
                    <p class="text-sm font-medium text-indigo-900 mb-2">Resumen de Reglas:</p>
                    <div class="bg-white rounded-lg border border-indigo-200 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-indigo-100">
                                <tr>
                                    <th class="px-3 py-2 text-left text-indigo-900">#</th>
                                    <th class="px-3 py-2 text-left text-indigo-900">Regla</th>
                                    <th class="px-3 py-2 text-left text-indigo-900">Condición</th>
                                    <th class="px-3 py-2 text-right text-indigo-900">Tasa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(rule, index) in rules" :key="index" class="border-t border-indigo-100">
                                    <td class="px-3 py-2 text-slate-600">{{ index + 1 }}</td>
                                    <td class="px-3 py-2 font-medium">{{ rule.label }}</td>
                                    <td class="px-3 py-2 font-mono text-xs text-slate-600">{{ rule.condition }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-indigo-600">{{ rule.base_commission }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
