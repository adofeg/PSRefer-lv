<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    referral: Object,
    auth: Object
});

const updateStatusForm = useForm({
    status: '',
    notes: '',
    revenue: null
});

const updateStatus = (status) => {
    if (!confirm(`¿Estás seguro de cambiar el estatus a ${status}?`)) return;

    updateStatusForm.status = status;
    updateStatusForm.patch(route('admin.referrals.update', props.referral.id), {
        onSuccess: () => updateStatusForm.reset()
    });
};
</script>

<template>
    <Head :title="`Referido: ${referral.client_name}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.referrals.index')" class="p-2 hover:bg-slate-100 rounded-full transition">
                    <ArrowLeft :size="24" class="text-slate-500" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ referral.client_name }}</h1>
                    <p class="text-slate-500">Detalles de la referencia</p>
                </div>
                <div class="ml-auto">
                    <Badge :status="referral.status" class="text-sm px-3 py-1" />
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
                            <dt class="text-sm text-slate-500">Notas</dt>
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

                <!-- Admin Action Area -->
                <Card v-if="$page.props.auth.user.role === 'psadmin'" class="md:col-span-2 border-indigo-100 bg-indigo-50/30">
                     <h3 class="font-bold text-lg mb-4 text-indigo-900">Gestión Administrativa</h3>
                     <div class="flex flex-wrap gap-3">
                        <button v-if="referral.status !== 'Contactado'" @click="updateStatus('Contactado')" class="px-4 py-2 bg-white border border-indigo-200 text-indigo-700 rounded hover:bg-indigo-50">Marcar Contactado</button>
                        <button v-if="referral.status !== 'En Proceso'" @click="updateStatus('En Proceso')" class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200">En Proceso</button>
                        <button v-if="referral.status !== 'Cerrado'" @click="updateStatus('Cerrado')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold shadow-sm">Cerrar Venta (Ganada)</button>
                        <button v-if="referral.status !== 'Perdido'" @click="updateStatus('Perdido')" class="px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200">Marcar Perdido</button>
                     </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
