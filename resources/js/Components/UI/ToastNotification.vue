<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, XCircle, AlertTriangle, X } from 'lucide-vue-next';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('success'); // success, error, warning
let timeout = null;

const showToast = (msg, msgType = 'success') => {
    message.value = msg;
    type.value = msgType;
    show.value = true;

    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
        show.value = false;
    }, 4000); // 4 seconds
};

// Watch for Inertia flash messages
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        showToast(flash.success, 'success');
    } else if (flash?.error) {
        showToast(flash.error, 'error');
    } else if (flash?.warning) {
        showToast(flash.warning, 'warning');
    }
}, { deep: true });

// Also check on mount
onMounted(() => {
    if (page.props.flash?.success) showToast(page.props.flash.success, 'success');
    if (page.props.flash?.error) showToast(page.props.flash.error, 'error');
    if (page.props.flash?.warning) showToast(page.props.flash.warning, 'warning');
});
</script>

<template>
    <div 
        aria-live="assertive" 
        class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-[100]"
    >
        <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
            <Transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="show" class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <CheckCircle2 v-if="type === 'success'" class="h-6 w-6 text-green-400" />
                                <XCircle v-if="type === 'error'" class="h-6 w-6 text-red-400" />
                                <AlertTriangle v-if="type === 'warning'" class="h-6 w-6 text-amber-400" />
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ type === 'success' ? 'Éxito' : (type === 'error' ? 'Error' : 'Atención') }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ message }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button @click="show = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <span class="sr-only">Cerrar</span>
                                    <X class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Optional Progress Bar -->
                    <div v-if="show" class="h-1 bg-gray-100 w-full overflow-hidden">
                        <div 
                            class="h-full transition-all duration-[4000ms] ease-linear w-0 animate-progress"
                            :class="{
                                'bg-green-500': type === 'success',
                                'bg-red-500': type === 'error',
                                'bg-amber-500': type === 'warning',
                            }"
                            style="width: 100%; transition: width 4s linear;"
                        ></div>
                    </div>
                </div>
            </Transition>
        </div>
    </div>
</template>

<style scoped>
/* Custom progress animation if needed, but width transition works too */
</style>
