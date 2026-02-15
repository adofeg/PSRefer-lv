<script setup>
import { ref, watch, computed } from 'vue';
import FormField from './FormField.vue';
import { 
    User, Briefcase, MapPin, FileText, CheckCircle, 
    Info, Hash, Phone, Mail, ChevronLeft, ChevronRight 
} from 'lucide-vue-next';

const props = defineProps({
    schema: {
        type: [Array, Object], // Array for legacy, Object for v2.0
        default: () => []
    },
    modelValue: {
        type: Object,
        default: () => ({})
    },
    errors: {
        type: Object,
        default: () => ({})
    },
    // Core Identity Props (v3.5)
    isReferralMode: { type: Boolean, default: false },
    clientName: String,
    clientEmail: String,
    clientPhone: String
});

const emit = defineEmits([
    'update:modelValue', 
    'update:is-final-step', 
    'update:is-valid',
    'update:clientName',
    'update:clientEmail',
    'update:clientPhone'
]);

const formData = ref({ ...props.modelValue });
const currentStep = ref(0);

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
    formData.value = { ...newValue };
}, { deep: true });

// Reset step if schema change
watch(() => props.schema, () => {
    currentStep.value = 0;
});

// Sync System Fields into formData for validation
watch(() => [props.clientName, props.clientEmail, props.clientPhone], ([n, e, p]) => {
    formData.value.client_name = n;
    formData.value.client_email = e;
    formData.value.client_phone = p;
}, { immediate: true });

const updateField = (fieldName, value) => {
    if (fieldName === 'client_name') emit('update:clientName', value);
    else if (fieldName === 'client_email') emit('update:clientEmail', value);
    else if (fieldName === 'client_phone') emit('update:clientPhone', value);
    else {
        formData.value[fieldName] = value;
        emit('update:modelValue', formData.value);
    }
};

// Version & Wizard Detection
const isGroupedSchema = computed(() => {
    if (props.isReferralMode) return true;
    return !Array.isArray(props.schema) && Array.isArray(props.schema?.groups);
});

const effectiveSchema = computed(() => {
    return props.schema;
});

const isWizard = computed(() => {
    return isGroupedSchema.value && (effectiveSchema.value?.groups?.length > 1);
});

const currentGroup = computed(() => {
    if (!isWizard.value) return null;
    return effectiveSchema.value.groups[currentStep.value];
});

const isFinalStep = computed(() => {
    if (!isWizard.value) return true;
    return currentStep.value === effectiveSchema.value.groups.length - 1;
});

watch(isFinalStep, (val) => {
    emit('update:is-final-step', val);
}, { immediate: true });

// Total Form Validation (All steps)
const isTotalValid = computed(() => {
    if (!isGroupedSchema.value) return true;
    
    return effectiveSchema.value.groups.every((group) => {
        return group.fields.every(field => {
            if (!field.required) return true;
            const value = formData.value[field.name];
            if (value === null || value === undefined || value === '') return false;
            if (Array.isArray(value)) return value.length > 0;
            return true;
        });
    });
});

watch(isTotalValid, (val) => {
    emit('update:is-valid', val);
}, { immediate: true });

// Step Validation
const canGoNext = computed(() => {
    if (!currentGroup.value) return true;
    
    // Check if every required field in the current group has a value
    return currentGroup.value.fields.every(field => {
        if (!field.required) return true;
        
        const value = formData.value[field.name];
        
        // Basic validation: not null, not undefined, not empty string
        if (value === null || value === undefined || value === '') return false;
        
        // Additional check for arrays (checkboxes/multi-select)
        if (Array.isArray(value)) return value.length > 0;
        
        return true;
    });
});

// Navigation logic
const goToStep = (index) => {
    // Only allow going back OR going to next step if current is valid
    if (index <= currentStep.value || canGoNext.value) {
        currentStep.value = index;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const nextStep = () => {
    if (canGoNext.value && currentStep.value < effectiveSchema.value.groups.length - 1) {
        currentStep.value++;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
};

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
</script>

<template>
    <div class="space-y-8">
        <!-- Wizard Progress Indicator (Stepping) -->
        <nav v-if="isWizard" class="flex items-center justify-between bg-slate-50/50 p-2 rounded-2xl border border-slate-100 shadow-inner mb-8">
            <div 
                v-for="(group, index) in effectiveSchema.groups" 
                :key="group.id" 
                class="flex-1 px-2"
            >
                <button 
                    @click="goToStep(index)"
                    type="button"
                    :disabled="index > currentStep && !canGoNext"
                    class="w-full group focus:outline-none disabled:cursor-not-allowed"
                >
                    <div class="flex flex-col items-center gap-1.5 py-2">
                        <div class="flex items-center justify-center w-8 h-8 rounded-xl text-xs font-black transition-all"
                             :class="[
                                currentStep === index ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100 scale-110' : 
                                currentStep > index ? 'bg-indigo-100 text-indigo-600' : 'bg-white text-slate-300 border border-slate-100'
                             ]">
                            <CheckCircle v-if="currentStep > index" :size="16" />
                            <span v-else>{{ index + 1 }}</span>
                        </div>
                        <span class="hidden md:block text-[9px] font-black uppercase tracking-tighter transition-colors"
                              :class="currentStep === index ? 'text-indigo-600' : 'text-slate-400'">
                            {{ group.title.split(' ')[0] }}...
                        </span>
                    </div>
                </button>
            </div>
        </nav>

        <!-- Dynamic Content: Wizard (Single Group) or Full List -->
        <div v-if="isGroupedSchema" class="grid grid-cols-1 gap-10">
            <!-- If Wizard, show only currentGroup. Else show all from effectiveSchema. -->
            <div 
                v-for="(group, index) in (isWizard ? [currentGroup] : effectiveSchema.groups)" 
                :key="group.id" 
                class="bg-white/50 rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden transition-all hover:border-slate-300 animate-fade-in"
            >
                <!-- Group Header -->
                <div class="bg-slate-50/50 px-8 py-5 border-b border-slate-100/60 flex items-center justify-between">
                    <div>
                        <h3 class="flex items-center gap-3 text-xs font-black text-slate-900 uppercase tracking-widest">
                            <component :is="getIcon(group.icon)" class="w-4 h-4 text-indigo-500" />
                            {{ group.title }}
                        </h3>
                        <p v-if="group.description" class="text-xs text-slate-500 mt-1 pl-6.5">
                            {{ group.description }}
                        </p>
                    </div>
                    <div v-if="isWizard" class="text-[10px] font-black uppercase text-slate-300 tracking-tight">
                        Paso {{ currentStep + 1 }} de {{ effectiveSchema.groups.length }}
                    </div>
                </div>

                <!-- Group Fields -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <FormField 
                            v-for="field in group.fields" 
                            :key="field.name" 
                            :field="field"
                            :modelValue="formData[field.name]"
                            :error="errors[field.name]"
                            @update:modelValue="updateField(field.name, $event)"
                            class="animate-slide-up"
                        />
                    </div>
                </div>
            </div>

            <!-- Wizard Navigation Buttons -->
            <div v-if="isWizard" class="flex items-center justify-between pt-4 pb-2">
                <button 
                    v-if="currentStep > 0"
                    @click="prevStep" 
                    type="button" 
                    class="flex items-center gap-2 px-6 py-3 rounded-xl border border-slate-200 text-slate-500 font-black text-xs uppercase tracking-widest hover:bg-slate-50 transition"
                >
                    <ChevronLeft :size="16" /> Anterior
                </button>
                <div v-else></div>

                <button 
                    v-if="currentStep < effectiveSchema.groups.length - 1"
                    @click="nextStep" 
                    type="button" 
                    :disabled="!canGoNext"
                    class="flex items-center gap-2 px-8 py-3.5 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 disabled:opacity-30 disabled:cursor-not-allowed disabled:grayscale"
                >
                    Siguiente <ChevronRight :size="16" />
                </button>
            </div>
        </div>

        <!-- Legacy Flat Layout -->
        <div v-else class="space-y-4">
            <div v-for="field in schema" :key="field.name">
                <FormField 
                    :field="field"
                    :modelValue="formData[field.name]"
                    :error="errors[field.name]"
                    @update:modelValue="updateField(field.name, $event)"
                />
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="(!schema || (Array.isArray(schema) && schema.length === 0)) && !isGroupedSchema" class="text-center py-8 text-slate-500">
            <p class="text-sm">No hay campos personalizados configurados para esta oferta.</p>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}

.animate-slide-up {
    animation: slideUp 0.5s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(10px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
