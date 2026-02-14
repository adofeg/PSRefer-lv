<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle, Share2, Download, Loader, ImageIcon } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { copyText } from '@/Utils/clipboard';
import { normalizeResource } from '@/Utils/inertia';

const props = defineProps({
    offering: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const offering = normalizeResource(props.offering, {});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

// --- Marketing Logic ---
const isGenerating = ref(false);
const canvasRef = ref(null);

const STOCK_IMAGES = {
    'Health': 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=1000&auto=format&fit=crop',
    'Taxes': 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?q=80&w=1000&auto=format&fit=crop',
    'Real Estate': 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=1000&auto=format&fit=crop',
    'default': 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1000&auto=format&fit=crop'
};

const getImageUrl = (category) => {
    return STOCK_IMAGES[category] || STOCK_IMAGES['default'];
};

const handleDownload = async () => {
    isGenerating.value = true;
    try {
        // Use offering specific image if available, else stock
        const imgUrl = offering.image_url || getImageUrl(offering.category?.name || offering.category);

        const img = new Image();
        img.crossOrigin = "anonymous";
        img.src = imgUrl;

        await new Promise((resolve, reject) => {
            img.onload = resolve;
            img.onerror = reject;
        });

        const canvas = canvasRef.value;
        const ctx = canvas.getContext('2d');
        canvas.width = img.width;
        canvas.height = img.height;

        ctx.drawImage(img, 0, 0);

        // Draw Overlay (White bar at bottom)
        const barHeight = canvas.height * 0.15;
        const yPos = canvas.height - barHeight;
        ctx.fillStyle = 'white';
        ctx.fillRect(0, yPos, canvas.width, barHeight);

        // Draw Content
        const padding = canvas.width * 0.05;
        const fontSize = Math.floor(canvas.height * 0.04);

        ctx.fillStyle = '#1e293b';
        ctx.font = `bold ${fontSize}px sans-serif`;
        ctx.fillText("Contáctame:", padding, yPos + barHeight * 0.35);

        ctx.fillStyle = '#4f46e5';
        ctx.font = `bold ${fontSize * 1.5}px sans-serif`;
        ctx.fillText(user.value.phone || 'Soporte PS Refer', padding, yPos + barHeight * 0.75);

        // Offering Name as Title Overlay
        ctx.save();
        ctx.shadowColor = "rgba(0,0,0,0.5)";
        ctx.shadowBlur = 10;
        ctx.fillStyle = 'white';
        ctx.font = `900 ${fontSize * 2}px sans-serif`;
        ctx.textAlign = 'center';
        ctx.fillText(offering.name.toUpperCase(), canvas.width / 2, canvas.height / 2);
        ctx.restore();

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
        alert("Error al generar la imagen. Por favor intenta de nuevo.");
    } finally {
        isGenerating.value = false;
    }
};

const copyLink = async () => {
    const link = offering.share_url
        || route('site.apply', {
            offeringId: offering.id,
            ref: user.value.profileable_id || user.value.id,
        });
    const copied = await copyText(link);

    if (copied) {
        alert("Enlace copiado al portapapeles");
        return;
    }

    window.prompt("Copia este enlace manualmente:", link);
};
</script>

<template>
    <Head :title="offering.name" />

    <AppLayout>
        <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <Link :href="route('associate.offerings.index')" class="flex items-center text-gray-500 hover:text-gray-700 transition">
                    <ArrowLeft class="w-5 h-5 mr-2" />
                    Volver al Catálogo
                </Link>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Header / Banner -->
                <div class="relative bg-gradient-to-br from-slate-900 to-slate-800 border-b border-slate-100 min-h-[180px] flex items-end overflow-hidden">
                    <!-- Decorate Background -->
                    <div class="absolute top-0 right-0 p-10 opacity-10 transform translate-x-10 -translate-y-10">
                        <ImageIcon :size="400" class="text-white" />
                    </div>

                    <div class="relative z-10 w-full px-8 py-8 flex flex-col md:flex-row justify-between items-end gap-6">
                        <div>
                             <div class="flex items-center gap-3 mb-3">
                                <span class="bg-white/10 backdrop-blur-md text-slate-200 border border-white/10 px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase">
                                    {{ offering.category?.name || 'Servicio' }}
                                </span>
                             </div>
                            <h1 class="text-3xl md:text-4xl font-black text-white mb-2 leading-tight tracking-tight">{{ offering.name }}</h1>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md p-5 rounded-xl border border-white/10 text-center min-w-[160px]">
                            <p class="text-[10px] text-slate-300 uppercase font-bold tracking-wider mb-1">Tu Comisión</p>
                             <p class="text-3xl font-black text-white">
                                {{ parseFloat(offering.commission_rate) > 0 ? `${offering.commission_rate}%` : formatCurrency(offering.base_commission) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Details -->
                    <div class="md:col-span-2 space-y-8">
                        <section>
                            <h3 class="text-lg font-bold text-slate-900 mb-4">Descripción</h3>
                            <p class="text-lg text-slate-600 leading-relaxed">{{ offering.description }}</p>
                        </section>

                        <section>
                            <h3 class="text-lg font-bold text-slate-900 mb-4">Detalles del Servicio</h3>
                            <div class="prose prose-slate max-w-none text-slate-600 bg-slate-50 p-6 rounded-xl border border-slate-100">
                                <p>{{ offering.details || 'No hay detalles adicionales disponibles para esta oferta.' }}</p>
                            </div>
                        </section>

                        <section>
                            <h3 class="text-lg font-bold text-slate-900 mb-4">¿Cómo funciona?</h3>
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <div class="bg-green-100 p-1 rounded-full mr-3 mt-0.5"><CheckCircle class="w-4 h-4 text-green-600 flex-shrink-0" /></div>
                                    <span class="text-slate-600">Comparte tu enlace único o crea un referido manual.</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="bg-green-100 p-1 rounded-full mr-3 mt-0.5"><CheckCircle class="w-4 h-4 text-green-600 flex-shrink-0" /></div>
                                    <span class="text-slate-600">Nuestros expertos contactan al cliente y cierran la venta.</span>
                                </li>
                                <li class="flex items-start">
                                    <div class="bg-green-100 p-1 rounded-full mr-3 mt-0.5"><CheckCircle class="w-4 h-4 text-green-600 flex-shrink-0" /></div>
                                    <span class="text-slate-600">Recibe tu comisión automáticamente en tu cuenta.</span>
                                </li>
                            </ul>
                        </section>
                    </div>

                    <!-- Action Sidebar -->
                    <div class="md:col-span-1 space-y-6">
                        <!-- CTA Card -->
                        <div class="bg-indigo-600 rounded-xl p-6 shadow-xl shadow-indigo-900/10 text-white relative overflow-hidden">
                             <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500 rounded-full opacity-50 blur-2xl"></div>
                            <h3 class="font-bold text-white mb-2 relative z-10">¿Listo para referir?</h3>
                            <p class="text-indigo-100 text-sm mb-6 relative z-10">Comienza ahora y aumenta tus ingresos.</p>
                            
                            <Link 
                                :href="route('associate.referrals.create', { offering_id: offering.id })"
                                class="block w-full text-center bg-white text-indigo-600 font-black py-3 px-4 rounded-lg hover:bg-indigo-50 transition shadow-lg relative z-10"
                            >
                                Crear Referido
                            </Link>
                        </div>

                        <!-- Marketing Tools Card (NEW) -->
                        <div class="bg-white rounded-xl p-6 border border-slate-200">
                             <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <ImageIcon :size="18" class="text-slate-400" />
                                Marketing
                             </h3>
                             
                             <div class="space-y-3">
                                <button
                                    @click="copyLink"
                                    class="w-full flex items-center justify-center gap-2 bg-slate-50 text-slate-600 py-2.5 rounded-lg border border-slate-200 font-bold text-xs uppercase tracking-wide hover:bg-slate-100 transition"
                                >
                                    <Share2 :size="14" /> Copiar Link
                                </button>
                                
                                <button
                                    @click="handleDownload"
                                    :disabled="isGenerating"
                                    class="w-full flex items-center justify-center gap-2 bg-slate-50 text-slate-600 py-2.5 rounded-lg border border-slate-200 font-bold text-xs uppercase tracking-wide hover:bg-slate-100 transition disabled:opacity-50"
                                >
                                    <Loader v-if="isGenerating" :size="14" class="animate-spin" />
                                    <Download v-else :size="14" />
                                    Descargar Promo
                                </button>
                                <p class="text-[10px] text-slate-400 text-center mt-2">
                                    Genera una imagen personalizada con tu número de teléfono para compartir en redes.
                                </p>
                             </div>
                        </div>

                    </div>
                </div>
            </div>
            
            <canvas ref="canvasRef" class="hidden"></canvas>
        </div>
    </AppLayout>
</template>
