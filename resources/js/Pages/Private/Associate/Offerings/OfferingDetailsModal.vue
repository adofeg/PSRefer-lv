<script setup>
import Modal from '@/Components/UI/Modal.vue';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { CheckCircle, Share2, Download, Loader, ImageIcon, User, Mail, Phone, MapPin, FileText, AlertCircle, Briefcase } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
import { copyText } from '@/Utils/clipboard';
import { normalizeResource } from '@/Utils/inertia';
import DynamicForm from '@/Components/Forms/DynamicForm.vue';

const props = defineProps({
    show: Boolean,
    offering: Object,
});

const emit = defineEmits(['close']);

const page = usePage();
const user = computed(() => page.props.auth.user);
const offeringResource = computed(() => normalizeResource(props.offering, null));

// --- Tab & Form Logic ---
const activeTab = ref('details'); // 'details' | 'register'

const form = useForm({
    offering_id: '',
    form_data: {},
    notes: '',
    consent_confirmed: false,
});

// Reset when offering changes
watch(() => props.offering, (newVal) => {
    if (newVal) {
        activeTab.value = 'details';
        form.reset();
        form.offering_id = offeringResource.value?.id || '';
    }
});

const submitReferral = () => {
    form.post(route('associate.referrals.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // alert("Referido creado exitosamente."); // Optional: Toast preferred if available
            emit('close');
            form.reset();
        },
        onError: () => {
            // Keep modal open, errors displayed in form
        }
    });
};


const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

// --- Marketing Logic (Preserved from Show.vue) ---
const isGenerating = ref(false);
const canvasRef = ref(null);

const handleDownload = async () => {
    if (!offeringResource.value) return;
    
    isGenerating.value = true;
    try {
        const offering = offeringResource.value;
        const canvas = canvasRef.value;
        const ctx = canvas.getContext('2d');
        
        // Dimensions
        canvas.width = 1080;
        canvas.height = 1080;

        // 1. Background Gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, '#0f172a'); // slate-900
        gradient.addColorStop(1, '#1e293b'); // slate-800
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // 2. Decorative Circles
        ctx.fillStyle = 'rgba(255, 255, 255, 0.03)';
        ctx.beginPath();
        ctx.arc(canvas.width, 0, 400, 0, Math.PI * 2);
        ctx.fill();
        
        ctx.beginPath();
        ctx.arc(0, canvas.height, 300, 0, Math.PI * 2);
        ctx.fill();

        // 3. Category Badge
        const categoryText = (offering.category?.name || offering.category || 'SERVICIO').toUpperCase();
        ctx.font = 'bold 30px Inter, sans-serif';
        const textMetrics = ctx.measureText(categoryText);
        const badgeWidth = textMetrics.width + 60;
        const badgeHeight = 60;
        const badgeX = (canvas.width - badgeWidth) / 2;
        const badgeY = 150;

        ctx.fillStyle = 'rgba(99, 102, 241, 0.2)'; 
        ctx.roundRect(badgeX, badgeY, badgeWidth, badgeHeight, 15);
        ctx.fill();
        ctx.strokeStyle = 'rgba(99, 102, 241, 0.5)';
        ctx.lineWidth = 2;
        ctx.stroke();

        ctx.fillStyle = '#818cf8'; 
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(categoryText, canvas.width / 2, badgeY + badgeHeight / 2);

        // 4. Title
        ctx.fillStyle = 'white';
        ctx.font = '900 80px Inter, sans-serif';
        ctx.shadowColor = "rgba(0,0,0,0.3)";
        ctx.shadowBlur = 20;
        
        const words = offering.name.split(' ');
        let line = '';
        let y = 350;
        const lineHeight = 90;
        const maxWidth = 900;

        for(let n = 0; n < words.length; n++) {
            const testLine = line + words[n] + ' ';
            const metrics = ctx.measureText(testLine);
            const testWidth = metrics.width;
            if (testWidth > maxWidth && n > 0) {
                ctx.fillText(line, canvas.width / 2, y);
                line = words[n] + ' ';
                y += lineHeight;
            } else {
                line = testLine;
            }
        }
        ctx.fillText(line, canvas.width / 2, y);
        ctx.shadowBlur = 0; 

        // 5. Description/Hook (Instead of Commission)
        y += 100;
        ctx.fillStyle = '#cbd5e1'; // Slate-300
        ctx.font = 'normal 40px Inter, sans-serif';
        // Simple generic hook based on category or default
        const hook = "¡Pregúntame cómo obtener este servicio hoy!";
        ctx.fillText(hook, canvas.width / 2, y);

        // 6. Contact Section (Footer) with QR Code
        const footerY = canvas.height - 300;
        
        // Draw White Footer Background
        ctx.fillStyle = 'white';
        ctx.fillRect(0, footerY, canvas.width, 300);

        // QR Code (Left Side)
        const qrSize = 200;
        const qrX = 80;
        const qrY = footerY + (300 - qrSize) / 2;
        
        // Load QR Code from API (Simple solution)
        // Link to public offering page with ref - NOW SECURE SIGNED URL
        const shareUrl = offering.share_url;
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(shareUrl)}`;

        const qrImg = new Image();
        qrImg.crossOrigin = "anonymous";
        qrImg.src = qrUrl;

        await new Promise((resolve) => {
            qrImg.onload = resolve;
            qrImg.onerror = () => {
                console.warn("Could not load QR code");
                resolve(); // Continue without QR
            };
        });
        
        if (qrImg.complete && qrImg.naturalHeight !== 0) {
             ctx.drawImage(qrImg, qrX, qrY, qrSize, qrSize);
        }

        // Text Content (Right Side)
        const textX = qrX + qrSize + 60;
        const textCenterY = footerY + 150;

        ctx.textAlign = 'left';
        
        ctx.fillStyle = '#64748b'; // Slate-500
        ctx.font = 'bold 30px Inter, sans-serif';
        ctx.fillText("CONTÁCTAME:", textX, textCenterY - 50);

        ctx.fillStyle = '#0f172a'; // Slate-900
        ctx.font = 'bold 70px Inter, sans-serif';
        const name = user.value.name || 'Agente Asociado';
        ctx.fillText(name, textX, textCenterY + 30);
        
        if (user.value.phone) {
             ctx.fillStyle = '#16a34a'; // Green-600
             ctx.font = 'bold 50px Inter, sans-serif';
             ctx.fillText(user.value.phone, textX, textCenterY + 100);
        }

        // 7. Download
        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
        if (!blob) {
            throw new Error('No se pudo generar la imagen');
        }

        const downloadUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.download = `Promo-${offering.name.replace(/\s+/g, '-')}.png`;
        link.href = downloadUrl;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(downloadUrl);

    } catch (error) {
        console.error("Canvas error:", error);
        alert("Error al generar la imagen.");
    } finally {
        isGenerating.value = false;
    }
};

const copyLink = async () => {
    if (!offeringResource.value || !offeringResource.value.share_url) return;
    const link = offeringResource.value.share_url;
    const copied = await copyText(link);

    if (copied) {
        alert("¡Enlace seguro copiado! Compártelo con total confianza.");
        return;
    }

    window.prompt("Copia este enlace manualmente:", link);
};
</script>

<template>
    <Modal :show="show" maxWidth="4xl" @close="$emit('close')">
        <div v-if="offering" class="bg-white rounded-2xl overflow-hidden relative flex flex-col max-h-[85vh]">
            
            <!-- Close Button -->
            <button @click="$emit('close')" class="absolute top-4 right-4 z-20 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-full p-2 transition">
                <X class="w-5 h-5" />
            </button>

            <!-- Header -->
            <div class="px-8 pt-8 pb-4 border-b border-slate-100 bg-white flex-shrink-0">
                <div class="flex flex-col md:flex-row justify-between items-start gap-4 pr-10">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                             <span class="bg-indigo-50 text-indigo-700 border border-indigo-100 px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase">
                                {{ offering.category?.name || offering.category || 'Servicio' }}
                            </span>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-black text-slate-800 leading-tight tracking-tight">{{ offering.name }}</h2>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex items-center gap-6 mt-6 border-b border-white">
                    <button 
                        @click="activeTab = 'details'"
                        class="pb-3 text-sm font-bold uppercase tracking-wider border-b-2 transition-colors duration-200"
                        :class="activeTab === 'details' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
                    >
                        Detalles
                    </button>
                    <button 
                        @click="activeTab = 'register'"
                        class="pb-3 text-sm font-bold uppercase tracking-wider border-b-2 transition-colors duration-200 flex items-center gap-2"
                        :class="activeTab === 'register' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
                    >
                        Registrar Referido
                    </button>
                </div>
            </div>

            <!-- Content Area (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-8">
                
                <!-- TAB 1: DETAILS -->
                <div v-if="activeTab === 'details'" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Details Columns -->
                    <div class="md:col-span-2 space-y-8">
                        <section>
                            <h3 class="text-sm font-bold text-slate-900 mb-2 uppercase tracking-wide flex items-center gap-2">
                                <FileText class="w-4 h-4 text-slate-400" />
                                Descripción
                            </h3>
                            <p class="text-base text-slate-600 leading-relaxed">{{ offering.description }}</p>
                        </section>

                        <section v-if="offering.details">
                            <h3 class="text-sm font-bold text-slate-900 mb-2 uppercase tracking-wide">Detalles</h3>
                            <div class="prose prose-sm prose-slate max-w-none text-slate-600 bg-slate-50 p-6 rounded-xl border border-slate-100">
                                <p>{{ offering.details }}</p>
                            </div>
                        </section>

                        <section>
                            <h3 class="text-sm font-bold text-slate-900 mb-3 uppercase tracking-wide">¿Cómo funciona?</h3>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <div class="bg-green-100 p-1 rounded-full mr-3 mt-0.5"><CheckCircle class="w-3 h-3 text-green-600 flex-shrink-0" /></div>
                                    <span class="text-sm text-slate-600">Comparte tu enlace único o crea un referido manual.</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="bg-green-100 p-1 rounded-full mr-3 mt-0.5"><CheckCircle class="w-3 h-3 text-green-600 flex-shrink-0" /></div>
                                    <span class="text-sm text-slate-600">Nuestros expertos contactan al cliente y cierran la venta.</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="bg-green-100 p-1 rounded-full mr-3 mt-0.5"><CheckCircle class="w-3 h-3 text-green-600 flex-shrink-0" /></div>
                                    <span class="text-sm text-slate-600">Recibe tu comisión automáticamente.</span>
                                </li>
                            </ul>
                        </section>
                    </div>

                    <!-- Sidebar -->
                    <div class="md:col-span-1 space-y-6">
                        <!-- Commission Card -->
                         <div class="bg-white p-5 rounded-xl border border-green-100 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 opacity-10">
                                <CheckCircle class="w-16 h-16 text-green-600" />
                            </div>
                             <p class="text-2xl font-black text-green-600">
                                {{ offering.commission_type === 'percentage' ? `${offering.base_commission}%` : formatCurrency(offering.base_commission) }}
                            </p>
                            <p class="text-xs text-slate-400 mt-1">Por venta cerrada</p>

                            <!-- Commission Rules List -->
                            <div v-if="offering.commission_rules && offering.commission_rules.length > 0" class="mt-4 pt-4 border-t border-green-50">
                                <p class="text-[9px] text-slate-400 uppercase font-bold tracking-wider mb-2">Reglas Especiales</p>
                                <div class="space-y-2">
                                    <div v-for="(rule, idx) in offering.commission_rules" :key="idx" class="flex items-start gap-2 bg-green-50/50 p-2 rounded-lg border border-green-100/50">
                                        <div class="mt-0.5"><CheckCircle :size="10" class="text-green-500" /></div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-700 leading-tight">{{ rule.label || 'Regla de Comisión' }}</p>
                                            <p class="text-[9px] text-slate-500">{{ rule.condition === 'default' ? 'Tarifa por defecto' : rule.condition }} → <span class="text-green-600 font-bold">{{ rule.base_commission }}{{ offering.commission_type === 'percentage' ? '%' : '$' }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Marketing Tools -->
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
                             <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2 text-sm">
                                <ImageIcon :size="16" class="text-slate-400" />
                                Material de Marketing
                             </h3>
                             
                             <div class="space-y-2">
                                <button @click="copyLink" class="w-full flex items-center justify-center gap-2 bg-white text-slate-600 py-2.5 rounded-lg border border-slate-200 font-bold text-[10px] uppercase tracking-wide hover:border-indigo-300 hover:text-indigo-600 transition">
                                    <Share2 :size="14" /> Copiar Link Seguro
                                </button>
                                
                                <button @click="handleDownload" :disabled="isGenerating" class="w-full flex items-center justify-center gap-2 bg-white text-slate-600 py-2.5 rounded-lg border border-slate-200 font-bold text-[10px] uppercase tracking-wide hover:border-indigo-300 hover:text-indigo-600 transition disabled:opacity-50">
                                    <Loader v-if="isGenerating" :size="14" class="animate-spin" />
                                    <Download v-else :size="14" />
                                    Descargar Promo
                                </button>
                             </div>
                        </div>
                    </div>
                </div>


                <!-- TAB 2: REGISTER -->
                <div v-else-if="activeTab === 'register'" class="space-y-6">
                    <form @submit.prevent="submitReferral" class="space-y-6">
                        
                        <!-- Dynamic Form (Catalog-Driven) -->
                        <!-- Dynamic Form (Catalog-Driven) -->
                        <div v-if="offeringResource?.form_schema">
                            <!-- Legacy Flat Schema Wrapper -->
                            <div v-if="Array.isArray(offeringResource.form_schema) && offeringResource.form_schema.length" class="rounded-xl p-6 border-2 border-slate-200 hover:border-indigo-200 transition-colors">
                                <div class="border-b border-slate-200 pb-3 mb-6">
                                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide flex items-center gap-2">
                                        <FileText class="w-4 h-4 text-indigo-600" />
                                        Información del Cliente
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-1">Campos configurados en el catálogo para este servicio.</p>
                                </div>
                                <DynamicForm 
                                    :schema="offeringResource.form_schema"
                                    v-model="form.form_data"
                                    :errors="form.errors"
                                />
                            </div>

                            <!-- v2.0 Grouped Schema (DynamicForm renders cards directly) -->
                            <DynamicForm 
                                v-else-if="!Array.isArray(offeringResource.form_schema)"
                                :schema="offeringResource.form_schema"
                                v-model="form.form_data"
                                :errors="form.errors"
                            />
                        </div>

                        <!-- Notes Section -->
                        <div class="rounded-xl p-6 border-2 border-slate-200 hover:border-amber-200 transition-colors">
                            <div class="border-b border-slate-200 pb-3 mb-4">
                                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide flex items-center gap-2">
                                    <FileText class="w-4 h-4 text-amber-600" />
                                    Contexto Adicional
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">Información que ayude a cerrar la venta más rápido (opcional).</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Notas Internas</label>
                                <textarea 
                                    v-model="form.notes" 
                                    rows="4" 
                                    class="w-full rounded-lg border-2 border-slate-200 focus:border-amber-500 focus:ring focus:ring-amber-200 text-sm bg-white resize-none p-4 transition" 
                                    placeholder="Ej: El cliente está interesado en cobertura familiar. Mejor horario: 10am-2pm."
                                ></textarea>
                            </div>
                        </div>

                        <!-- Consent Tracking (persisted to DB) -->
                        <div class="rounded-xl p-4 border-2 border-indigo-200 bg-indigo-50/50">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input 
                                    type="checkbox" 
                                    v-model="form.consent_confirmed" 
                                    required
                                    class="mt-0.5 rounded border-indigo-400 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0"
                                >
                                <span class="text-xs text-slate-700 leading-relaxed">
                                    Al registrar, <strong>confirmo que el cliente expresó interés genuino</strong> y autorizo el uso de esta información exclusivamente para contacto relacionado con el servicio.
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 flex justify-end">
                            <button type="submit" :disabled="form.processing || !form.consent_confirmed" class="w-full md:w-auto bg-indigo-600 text-white px-10 py-3.5 rounded-xl hover:bg-indigo-700 transition font-bold text-sm shadow-xl shadow-indigo-200 disabled:opacity-50 flex items-center justify-center gap-3 transform hover:-translate-y-0.5">
                                <Loader v-if="form.processing" class="animate-spin w-5 h-5" />
                                <span v-else>Confirmar y Enviar Referido</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Hidden Canvas for Image Generation -->
            <canvas ref="canvasRef" class="hidden"></canvas>
        </div>
    </Modal>
</template>
