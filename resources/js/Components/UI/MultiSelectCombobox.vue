<script setup>
import { ref, computed } from 'vue';
import {
    Combobox,
    ComboboxInput,
    ComboboxButton,
    ComboboxOptions,
    ComboboxOption,
    TransitionRoot,
} from '@headlessui/vue';
import { Check, ChevronsUpDown, X, ChevronDown, Search } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: [Array, String, Number, Object],
        default: null,
    },
    options: {
        type: Array,
        required: true,
        // Array of { id: any, name: string, sublabel?: string }
    },
    placeholder: {
        type: String,
        default: 'Seleccionar...',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    multiple: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['update:modelValue']);

const query = ref('');

const filteredOptions = computed(() =>
    query.value === ''
        ? props.options
        : props.options.filter((option) => {
            const searchStr = `${option.name} ${option.email || ''} ${option.label || ''}`.toLowerCase();
            return searchStr.includes(query.value.toLowerCase());
        })
);

const selectedItems = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const removeItem = (id) => {
    if (props.multiple && Array.isArray(selectedItems.value)) {
        selectedItems.value = selectedItems.value.filter((itemId) => itemId !== id);
    } else {
        selectedItems.value = null;
    }
};

const getLabel = (id) => {
    if (id === null || id === undefined) {
        return '';
    }
    const option = props.options.find((opt) => String(opt.id) === String(id) || String(opt.value) === String(id));
    return option ? (option.name || option.label) : (id === 'all' ? '' : id);
};
</script>

<template>
    <div class="w-full">
        <Combobox v-model="selectedItems" :multiple="multiple" :disabled="disabled" nullable v-slot="{ open }">
            <div class="relative">
                <div
                    class="relative w-full cursor-default overflow-hidden rounded-xl bg-white text-left border transition-all duration-300 sm:text-sm hover:border-slate-300"
                    :class="[
                        (!multiple && selectedItems) || (multiple && Array.isArray(selectedItems) && selectedItems.length > 0) 
                            ? 'border-indigo-200 bg-indigo-50/20' 
                            : 'border-slate-200 shadow-sm'
                    ]"
                >
                    <div class="flex items-center min-h-[36px] group">
                        <!-- Multi-select Chips -->
                        <div v-if="multiple && Array.isArray(selectedItems) && selectedItems.length > 0" class="flex flex-wrap gap-1 p-1 border-r border-slate-100 mr-2 bg-white/50 backdrop-blur-sm">
                            <span
                                v-for="id in selectedItems"
                                :key="id"
                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50/50 text-indigo-600 rounded-md text-[10px] font-bold uppercase tracking-tight border border-indigo-100/50"
                            >
                                {{ getLabel(id) }}
                                <button
                                    v-if="!disabled"
                                    type="button"
                                    @click.stop="removeItem(id)"
                                    class="hover:text-indigo-800 transition-colors"
                                >
                                    <X :size="10" />
                                </button>
                            </span>
                        </div>
                        
                        <ComboboxButton as="div" class="flex-1 flex items-center">
                            <ComboboxInput
                                class="flex-1 border-none py-2 px-4 text-xs leading-5 text-slate-600 placeholder:text-slate-400 focus:ring-0 bg-transparent w-full cursor-default transition-all truncate"
                                :displayValue="(val) => (multiple ? query : getLabel(val))"
                                @change="query = $event.target.value"
                                @focus="$event.target.select()"
                                :placeholder="(multiple ? (!selectedItems || selectedItems.length === 0) : !selectedItems) ? placeholder : ''"
                                autocomplete="off"
                                data-lpignore="true"
                                spellcheck="false"
                            />

                            <!-- Clear Button (Single Select) -->
                            <Transition
                                enter-active-class="transition-all duration-200"
                                enter-from-class="opacity-0 scale-95"
                                enter-to-class="opacity-100 scale-100"
                                leave-active-class="transition-all duration-150"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95"
                            >
                                <button
                                    v-if="!multiple && selectedItems && !disabled"
                                    type="button"
                                    @click.stop="selectedItems = null"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-slate-300 hover:text-red-400 hover:bg-red-50 rounded-lg transition-all z-20"
                                    title="Limpiar selección"
                                >
                                    <X :size="14" stroke-width="2.5" />
                                </button>
                            </Transition>
                        </ComboboxButton>
                    </div>
                </div>
                
                <TransitionRoot
                    enter="transition duration-200 ease-out"
                    enterFrom="opacity-0 translate-y-1"
                    enterTo="opacity-100 translate-y-0"
                    leave="transition duration-150 ease-in"
                    leaveFrom="opacity-100 translate-y-0"
                    leaveTo="opacity-0 translate-y-1"
                    @after-leave="query = ''"
                >
                    <ComboboxOptions
                        class="absolute mt-1.5 max-h-60 w-full overflow-auto rounded-2xl bg-white py-2 text-base shadow-2xl ring-1 ring-slate-900/5 focus:outline-none sm:text-sm z-50 border border-slate-100 scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent animate-in fade-in zoom-in duration-200"
                    >
                        <!-- Phantom null option to prevent auto-select on blur -->
                        <ComboboxOption v-if="!multiple" :value="null" class="hidden" />
                        
                        <div
                            v-if="filteredOptions.length === 0 && query !== ''"
                            class="relative cursor-default select-none py-6 px-4 text-center"
                        >
                            <div class="mb-2 flex justify-center text-slate-200">
                                <Search :size="24" />
                            </div>
                            <span class="text-xs text-slate-400 font-medium italic">No se hallaron coincidencias.</span>
                        </div>

                        <ComboboxOption
                            v-for="option in filteredOptions"
                            as="template"
                            :key="option.id"
                            :value="option.id"
                            v-slot="{ active, selected }"
                        >
                            <li
                                class="relative cursor-default select-none mx-2 py-2.5 pl-10 pr-4 rounded-xl transition-all duration-200 group"
                                :class="{
                                    'bg-indigo-50/70 text-indigo-700': active,
                                    'text-slate-600': !active,
                                    'bg-indigo-50/20': selected && !active,
                                }"
                            >
                                <div class="flex flex-col">
                                    <span
                                        class="block truncate text-xs font-semibold"
                                        :class="{ 'text-indigo-700': selected || active, 'text-slate-700': !selected && !active }"
                                    >
                                        {{ option.name || option.label }}
                                    </span>
                                    <span v-if="option.sublabel || option.email" class="block truncate text-[10px] text-slate-400 mt-0.5 opacity-80 group-hover:opacity-100">
                                        {{ option.sublabel || option.email }}
                                    </span>
                                </div>

                                <span
                                    v-if="selected"
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 text-indigo-500"
                                >
                                    <Check :size="14" stroke-width="3" />
                                </span>
                            </li>
                        </ComboboxOption>
                    </ComboboxOptions>
                </TransitionRoot>
            </div>
        </Combobox>
    </div>
</template>
