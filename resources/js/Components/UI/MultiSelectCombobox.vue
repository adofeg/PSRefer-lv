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
import { Check, ChevronsUpDown, X } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
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
        default: true,
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
    selectedItems.value = selectedItems.value.filter((itemId) => itemId !== id);
};

const getLabel = (id) => {
    const option = props.options.find((opt) => opt.id === id || opt.value === id);
    return option ? (option.name || option.label) : id;
};
</script>

<template>
    <div class="w-full">
        <Combobox v-model="selectedItems" :multiple="multiple" :disabled="disabled">
            <div class="relative mt-1">
                <div
                    class="relative w-full cursor-default overflow-hidden rounded-2xl bg-white text-left border border-slate-200 focus-within:ring-4 focus-within:ring-indigo-500/10 focus-within:border-indigo-500 transition-all sm:text-sm"
                >
                    <div class="flex flex-wrap gap-1.5 p-2 min-h-[52px] items-center">
                        <span
                            v-for="id in (multiple ? selectedItems : (selectedItems ? [selectedItems] : []))"
                            :key="id"
                            class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-black uppercase tracking-tight border border-indigo-100"
                        >
                            {{ getLabel(id) }}
                            <button
                                v-if="!disabled"
                                type="button"
                                @click.stop="removeItem(id)"
                                class="hover:text-indigo-900 transition-colors"
                            >
                                <X :size="14" />
                            </button>
                        </span>
                        
                        <ComboboxInput
                            class="flex-1 border-none py-2 pl-3 pr-10 text-sm leading-5 text-slate-900 focus:ring-0 bg-transparent min-w-[120px]"
                            :displayValue="(val) => query"
                            @change="query = $event.target.value"
                            :placeholder="selectedItems.length === 0 ? placeholder : ''"
                        />
                    </div>
                    
                    <ComboboxButton
                        class="absolute inset-y-0 right-0 flex items-center pr-2"
                    >
                        <ChevronsUpDown
                            class="h-5 w-5 text-slate-400"
                            aria-hidden="true"
                        />
                    </ComboboxButton>
                </div>
                
                <TransitionRoot
                    leave="transition ease-in duration-100"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                    @after-leave="query = ''"
                >
                    <ComboboxOptions
                        class="absolute mt-2 max-h-60 w-full overflow-auto rounded-2xl bg-white py-1 text-base shadow-2xl ring-1 ring-black/5 focus:outline-none sm:text-sm z-50 border border-slate-100"
                    >
                        <div
                            v-if="filteredOptions.length === 0 && query !== ''"
                            class="relative cursor-default select-none py-4 px-4 text-slate-500 italic"
                        >
                            No se encontraron resultados.
                        </div>

                        <ComboboxOption
                            v-for="option in filteredOptions"
                            :key="option.id || option.value"
                            :value="option.id || option.value"
                            as="template"
                            v-slot="{ selected, active }"
                        >
                            <li
                                class="relative cursor-default select-none py-3 pl-10 pr-4 transition-colors"
                                :class="{
                                    'bg-indigo-600 text-white': active,
                                    'text-slate-900': !active,
                                }"
                            >
                                <div class="flex flex-col">
                                    <span
                                        class="block truncate font-bold"
                                        :class="{ 'font-black': selected, 'font-medium': !selected }"
                                    >
                                        {{ option.name || option.label }}
                                    </span>
                                    <span 
                                        v-if="option.email" 
                                        class="block truncate text-xs"
                                        :class="active ? 'text-indigo-100' : 'text-slate-400'"
                                    >
                                        {{ option.email }}
                                    </span>
                                </div>
                                <span
                                    v-if="selected"
                                    class="absolute inset-y-0 left-0 flex items-center pl-3"
                                    :class="{ 'text-white': active, 'text-indigo-600': !active }"
                                >
                                    <Check class="h-5 w-5" aria-hidden="true" />
                                </span>
                            </li>
                        </ComboboxOption>
                    </ComboboxOptions>
                </TransitionRoot>
            </div>
        </Combobox>
    </div>
</template>
