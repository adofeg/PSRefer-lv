<script setup>
import { computed } from 'vue';

const props = defineProps({
    field: {
        type: Object,
        required: true
    },
    modelValue: {
        type: [String, Number, Boolean, File, Array, Object],
        default: ''
    },
    error: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);

// Use a computed property for v-model to handle binding clean
const inputValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const inputClass = computed(() => {
    const baseClass = 'mt-2 w-full px-4 py-3.5 border rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition bg-white text-slate-700 font-medium';
    const errorClass = props.error ? 'border-red-500' : 'border-slate-200';
    return `${baseClass} ${errorClass}`;
});

const parsedOptions = computed(() => {
    if (!props.field.options) return [];
    if (Array.isArray(props.field.options)) return props.field.options;
    
    // If it's a string, split by comma and trim
    if (typeof props.field.options === 'string') {
        return props.field.options.split(',').map(opt => opt.trim()).filter(opt => opt !== '');
    }
    
    return [];
});
</script>

<template>
    <div class="form-field">
        <label :for="field.name" class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500 ml-0.5">*</span>
        </label>

        <!-- Text Input -->
        <input 
            v-if="field.type === 'text'"
            :id="field.name"
            type="text"
            v-model="inputValue"
            :placeholder="field.placeholder || ''"
            :required="field.required"
            :maxlength="field.maxlength"
            :class="inputClass"
        >

        <!-- Email Input -->
        <input 
            v-if="field.type === 'email'"
            :id="field.name"
            type="email"
            v-model="inputValue"
            :placeholder="field.placeholder || 'ejemplo@email.com'"
            :required="field.required"
            :class="inputClass"
        >

        <!-- Phone/Tel Input -->
        <input 
            v-if="field.type === 'tel' || field.type === 'phone'"
            :id="field.name"
            type="tel"
            v-model="inputValue"
            :placeholder="field.placeholder || '+1 (555) 123-4567'"
            :required="field.required"
            :class="inputClass"
        >

        <!-- URL Input -->
        <input 
            v-if="field.type === 'url'"
            :id="field.name"
            type="url"
            v-model="inputValue"
            :placeholder="field.placeholder || 'https://ejemplo.com'"
            :required="field.required"
            :class="inputClass"
        >

        <!-- Number Input -->
        <input 
            v-if="field.type === 'number'"
            :id="field.name"
            type="number"
            v-model="inputValue"
            :placeholder="field.placeholder || '0'"
            :required="field.required"
            :min="field.min"
            :max="field.max"
            :step="field.step || 'any'"
            :class="inputClass"
        >

        <select 
            v-if="field.type === 'select'"
            :id="field.name"
            v-model="inputValue"
            :required="field.required"
            :class="inputClass"
        >
            <option value="">{{ field.placeholder || 'Seleccionar...' }}</option>
            <option 
                v-for="option in parsedOptions" 
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
            v-model="inputValue"
            :placeholder="field.placeholder || ''"
            :required="field.required"
            :rows="field.rows || 4"
            :maxlength="field.maxlength"
            :class="inputClass"
        ></textarea>

        <!-- Date Input -->
        <input 
            v-if="field.type === 'date'"
            :id="field.name"
            type="date"
            v-model="inputValue"
            :required="field.required"
            :min="field.min"
            :max="field.max"
            :class="inputClass"
        >

        <!-- Checkbox -->
        <div v-if="field.type === 'checkbox'" class="flex items-center mt-2">
            <input 
                :id="field.name"
                type="checkbox"
                v-model="inputValue"
                class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
            >
            <label :for="field.name" class="ml-2 text-sm text-slate-700">
                {{ field.checkboxLabel || field.label }}
            </label>
        </div>

        <!-- File Input -->
        <div v-if="field.type === 'file'">
            <input 
                :id="field.name"
                type="file"
                @change="(e) => emit('update:modelValue', e.target.files[0])"
                :accept="field.accept || '*/*'"
                :required="field.required"
                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition"
            >
        </div>

        <!-- Help Text -->
        <p v-if="field.helpText" class="mt-1 text-xs text-slate-500">
            {{ field.helpText }}
        </p>

        <!-- Error Message -->
        <p v-if="error" class="mt-1 text-sm text-red-600">
            {{ error }}
        </p>
    </div>
</template>
