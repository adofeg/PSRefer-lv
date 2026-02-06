<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    schema: {
        type: Array,
        default: () => []
    },
    modelValue: {
        type: Object,
        default: () => ({})
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update:modelValue']);

const formData = ref({ ...props.modelValue });

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
    formData.value = { ...newValue };
}, { deep: true });

const updateField = (fieldName, value) => {
    formData.value[fieldName] = value;
    emit('update:modelValue', formData.value);
};

const getInputClass = (fieldName) => {
    const baseClass = 'mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition';
    const errorClass = props.errors[fieldName] ? 'border-red-500' : 'border-slate-300';
    return `${baseClass} ${errorClass}`;
};
</script>

<template>
    <div class="space-y-4">
        <div v-for="field in schema" :key="field.name" class="form-field">
            <label :for="field.name" class="block text-sm font-medium text-slate-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500 ml-1">*</span>
            </label>

            <!-- Text Input -->
            <input 
                v-if="field.type === 'text'"
                :id="field.name"
                type="text"
                :value="formData[field.name]"
                @input="updateField(field.name, $event.target.value)"
                :placeholder="field.placeholder || ''"
                :required="field.required"
                :maxlength="field.maxlength"
                :class="getInputClass(field.name)"
            >

            <!-- Email Input -->
            <input 
                v-if="field.type === 'email'"
                :id="field.name"
                type="email"
                :value="formData[field.name]"
                @input="updateField(field.name, $event.target.value)"
                :placeholder="field.placeholder || 'ejemplo@email.com'"
                :required="field.required"
                :class="getInputClass(field.name)"
            >

            <!-- Phone/Tel Input -->
            <input 
                v-if="field.type === 'tel' || field.type === 'phone'"
                :id="field.name"
                type="tel"
                :value="formData[field.name]"
                @input="updateField(field.name, $event.target.value)"
                :placeholder="field.placeholder || '+1 (555) 123-4567'"
                :required="field.required"
                :class="getInputClass(field.name)"
            >

            <!-- URL Input -->
            <input 
                v-if="field.type === 'url'"
                :id="field.name"
                type="url"
                :value="formData[field.name]"
                @input="updateField(field.name, $event.target.value)"
                :placeholder="field.placeholder || 'https://ejemplo.com'"
                :required="field.required"
                :class="getInputClass(field.name)"
            >

            <!-- Number Input -->
            <input 
                v-if="field.type === 'number'"
                :id="field.name"
                type="number"
                :value="formData[field.name]"
                @input="updateField(field.name, parseFloat($event.target.value))"
                :placeholder="field.placeholder || '0'"
                :required="field.required"
                :min="field.min"
                :max="field.max"
                :step="field.step || 'any'"
                :class="getInputClass(field.name)"
            >

            <!-- Select Dropdown -->
            <select 
                v-if="field.type === 'select'"
                :id="field.name"
                :value="formData[field.name]"
                @change="updateField(field.name, $event.target.value)"
                :required="field.required"
                :class="getInputClass(field.name)"
            >
                <option value="">{{ field.placeholder || 'Seleccionar...' }}</option>
                <option 
                    v-for="option in field.options" 
                    :key="option" 
                    :value="option"
                >
                    {{ option }}
                </option>
            </select>

            <!-- Textarea -->
            <textarea 
                v-if="field.type === 'textarea'"
                :id="field.name"
                :value="formData[field.name]"
                @input="updateField(field.name, $event.target.value)"
                :placeholder="field.placeholder || ''"
                :required="field.required"
                :rows="field.rows || 4"
                :maxlength="field.maxlength"
                :class="getInputClass(field.name)"
            ></textarea>

            <!-- Date Input -->
            <input 
                v-if="field.type === 'date'"
                :id="field.name"
                type="date"
                :value="formData[field.name]"
                @input="updateField(field.name, $event.target.value)"
                :required="field.required"
                :min="field.min"
                :max="field.max"
                :class="getInputClass(field.name)"
            >

            <!-- Checkbox -->
            <div v-if="field.type === 'checkbox'" class="flex items-center mt-2">
                <input 
                    :id="field.name"
                    type="checkbox"
                    :checked="formData[field.name]"
                    @change="updateField(field.name, $event.target.checked)"
                    class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
                >
                <label :for="field.name" class="ml-2 text-sm text-slate-700">
                    {{ field.checkboxLabel || field.label }}
                </label>
            </div>

            <!-- Help Text -->
            <p v-if="field.helpText" class="mt-1 text-xs text-slate-500">
                {{ field.helpText }}
            </p>

            <!-- Error Message -->
            <p v-if="errors[field.name]" class="mt-1 text-sm text-red-600">
                {{ errors[field.name] }}
            </p>
        </div>

        <!-- Empty State -->
        <div v-if="!schema || schema.length === 0" class="text-center py-8 text-slate-500">
            <p class="text-sm">No hay campos personalizados configurados para esta oferta.</p>
        </div>
    </div>
</template>

<style scoped>
/* Estilos adicionales si es necesario */
</style>
