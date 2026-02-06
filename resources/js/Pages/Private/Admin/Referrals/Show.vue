<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';
import StatusChangeModal from '@/Components/Referrals/StatusChangeModal.vue';
import AuditTimeline from '@/Components/Referrals/AuditTimeline.vue';
import { ArrowLeft, History } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    referral: Object,
    auth: Object
});

const showStatusModal = ref(false);

const handleStatusUpdated = () => {
    // Inertia will auto-refresh the page data
    showStatusModal.value = false;
};
</script>

<template>
    <Head :title="`Referido: ${referral.client_name}`" />

    <AppLayout>
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.referrals.index')" class="p-2 hover:bg-slate-100 rounded-full transition">
                    <ArrowLeft :size="24" class="text-slate-500" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ referral.client_name }}</h1>
                    <p class="text-slate-500">Detalles de la referencia</p>
                </div>
                <div class="ml-auto flex items-center gap-3">
                    <Badge :status="referral.status" class="text-sm px-3 py-1" />
                    <button 
                        v-if="['psadmin'].includes($page.props.auth.user.role)"
                        @click="showStatusModal = true" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium text-sm"
                    >
                        Cambiar Estado
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Info -->
                <Card>
                    <h3 class="font-bold text-lg mb-4 text-slate-800">Información del Cliente</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-slate-500">Nombre</dt>
                            <dd class="font-medium text-slate-800">{{ referral.client_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-slate-500">Contacto</dt>
                            <dd class="font-medium text-slate-800">{{ referral.client_contact || 'No registrado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-slate-500">Notas Iniciales</dt>
                            <dd class="text-slate-600 bg-slate-50 p-3 rounded-lg">{{ referral.notes || 'Sin notas' }}</dd>
                        </div>
                    </dl>
                </Card>

                <!-- Service Info -->
                <Card>
                    <h3 class="font-bold text-lg mb-4 text-slate-800">Servicio Referido</h3>
                    <div v-if="referral.offering">
                        <p class="font-bold text-indigo-600 mb-1">{{ referral.offering.name }}</p>
                        <p class="text-sm text-slate-600 mb-2">{{ referral.offering.category }}</p>
                        <div class="text-sm border-t border-slate-100 pt-2 mt-2">
                            <span class="text-slate-500">Comisión Potencial:</span>
                            <span class="font-bold text-green-600 ml-2">
                                {{ referral.offering.commission_rate ? `${referral.offering.commission_rate}%` : `$${referral.offering.base_commission}` }}
                            </span>
                        </div>
                    </div>
                </Card>

                <!-- Financial Details -->
                <Card v-if="referral.status === 'Cerrado' || referral.contract_id" class="md:col-span-2">
                    <h3 class="font-bold text-lg mb-4 text-slate-800">Detalles Financieros de la Venta</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <dt class="text-xs text-slate-500 uppercase font-bold mb-1">ID Contrato</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ referral.contract_id || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500 uppercase font-bold mb-1">Método Pago</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ referral.payment_method || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500 uppercase font-bold mb-1">Pago Inicial</dt>
                            <dd class="text-sm font-medium text-green-600 font-mono">${{ referral.down_payment || '0.00' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500 uppercase font-bold mb-1">Venta Total</dt>
                            <dd class="text-sm font-bold text-slate-900 font-mono">${{ referral.revenue_generated || '0.00' }}</dd>
                        </div>
                        <div v-if="referral.agency_fee">
                            <dt class="text-xs text-slate-500 uppercase font-bold mb-1">Agency Fee</dt>
                            <dd class="text-sm font-medium text-amber-600 font-mono">${{ referral.agency_fee }}</dd>
                        </div>
                    </div>
                </Card>

                <!-- Audit History Timeline -->
                <Card class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <History :size="20" class="text-slate-600" />
                        <h3 class="font-bold text-lg text-slate-800">Bitácora de Seguimiento</h3>
                        <span class="text-xs text-slate-500 ml-2">({{ referral.history?.length || 0 }} cambios)</span>
                    </div>
                    <AuditTimeline :history="referral.history || []" />
                </Card>
            </div>
        </div>

        <!-- Status Change Modal -->
        <StatusChangeModal 
            :show="showStatusModal"
            :referral-id="referral.id"
            :current-status="referral.status"
            @close="showStatusModal = false"
            @updated="handleStatusUpdated"
        />
    </AppLayout>
</template>