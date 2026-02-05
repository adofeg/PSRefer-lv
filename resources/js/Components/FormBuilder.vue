<script setup>
import { computed } from 'vue';

const props = defineProps({
    schema: {
        type: Array,
        required: true,
        default: () => []
    },
    modelValue: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const updateField = (key, value) => {
    emit('update:modelValue', { ...props.modelValue, [key]: value });
};
</script>

<template>
    <div class="space-y-4">
        <div v-for="(field, index) in schema" :key="index">
            <label :for="field.name" class="block font-medium text-sm text-gray-700 mb-1">
                {{ field.label }} <span v-if="field.required" class="text-red-500">*</span>
            </label>

            <!-- Text Input -->
            <input
                v-if="['text', 'email', 'tel', 'number', 'date'].includes(field.type)"
                :id="field.name"
                :type="field.type"
                :value="modelValue[field.name]"
                @input="updateField(field.name, $event.target.value)"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                :required="field.required"
                :placeholder="field.placeholder"
            />

            <!-- Select -->
            <select
                v-if="field.type === 'select'"
                :id="field.name"
                :value="modelValue[field.name]"
                @change="updateField(field.name, $event.target.value)"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                :required="field.required"
            >
                <option value="">Select an option</option>
                <option v-for="opt in field.options" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>

            <!-- Textarea -->
            <textarea
                v-if="field.type === 'textarea'"
                :id="field.name"
                :value="modelValue[field.name]"
                @input="updateField(field.name, $event.target.value)"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                rows="3"
                :required="field.required"
            ></textarea>
        </div>

        <div v-if="schema.length === 0" class="text-gray-500 text-sm italic">
            No additional information required for this offering.
        </div>
    </div>
</template>
