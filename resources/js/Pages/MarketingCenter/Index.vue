<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import { ref, computed, onMounted } from 'vue';
import { Download, Loader, Check, Briefcase, Share2 } from 'lucide-vue-next';

const props = defineProps({
    offerings: Array,
    categories: Array,
    user: Object
});

const selectedCategory = ref('All');
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

const filteredOfferings = computed(() => {
    return props.offerings.filter(offering => {
        // Exclusion Rule: Associate cannot refer services in their own business category
        if (props.user.category && offering.category) {
            if (props.user.category.toLowerCase().trim() === offering.category.toLowerCase().trim()) return false;
        }
        
        // Category Filter
        if (selectedCategory.value !== 'All' && offering.category !== selectedCategory.value) return false;

        return true;
    });
});

const handleDownload = async (offering) => {
    isGenerating.value = true;
    try {
        const imgUrl = getImageUrl(offering.category);

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
        ctx.fillText(props.user.phone || 'Soporte PS Refer', padding, yPos + barHeight * 0.75);

        // Offering Name as Title Overlay
        ctx.save();
        ctx.shadowColor = "rgba(0,0,0,0.5)";
        ctx.shadowBlur = 10;
        ctx.fillStyle = 'white';
        ctx.font = `900 ${fontSize * 2}px sans-serif`;
        ctx.textAlign = 'center';
        ctx.fillText(offering.name.toUpperCase(), canvas.width / 2, canvas.height / 2);
        ctx.restore();

        const dataUrl = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = `Promo-${offering.name.replace(/\s+/g, '-')}.png`;
        link.href = dataUrl;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

    } catch (error) {
        console.error("Canvas error:", error);
        alert("Error al generar la imagen. Por favor intenta de nuevo.");
    } finally {
        isGenerating.value = false;
    }
};

const copyLink = (offeringId) => {
    const link = `${window.location.origin}/public/apply/${offeringId}?ref=${props.user.id}`;
    navigator.clipboard.writeText(link);
    alert("Enlace copiado al portapapeles");
};
</script>

<template>
    <Head title="Marketing Center" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-8 md:p-10 text-center rounded-2xl shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
                <h1 class="text-4xl font-black uppercase tracking-wider mb-2 z-10 relative">Centro de Marketing</h1>
                <p class="text-red-100 text-lg z-10 relative">Descarga materiales personalizados y comparte tus enlaces de referido</p>
            </div>

            <!-- Categories Filter -->
            <div class="flex flex-wrap gap-2 pb-2">
                <button
                    @click="selectedCategory = 'All'"
                    :class="[
                        'px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm border',
                        selectedCategory === 'All' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
                    ]"
                >
                    Todos
                </button>
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="selectedCategory = cat.name"
                    :class="[
                        'px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm border',
                        selectedCategory === cat.name ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
                    ]"
                >
                    {{ cat.name }}
                </button>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <Card v-for="offering in filteredOfferings" :key="offering.id" class="overflow-hidden p-0 group border-none shadow-md hover:shadow-xl transition-all duration-300 flex flex-col h-full">
                    <div class="relative aspect-[16/9] overflow-hidden bg-slate-100">
                        <img
                            :src="getImageUrl(offering.category)"
                            :alt="offering.name"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center p-6 text-center backdrop-blur-[1px] group-hover:backdrop-blur-none transition-all">
                            <h3 class="text-white text-2xl font-black uppercase leading-tight tracking-tight">
                                {{ offering.name }}
                            </h3>
                        </div>
                        <div class="absolute top-3 left-3">
                            <span class="bg-indigo-600/90 text-white text-[10px] font-black uppercase px-2 py-1 rounded-md backdrop-blur-sm">
                                {{ offering.category }}
                            </span>
                        </div>
                    </div>

                    <div class="p-5 bg-white flex-1 flex flex-col">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-2 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                <Briefcase :size="14" class="text-indigo-400" />
                                {{ offering.category }}
                            </div>
                            <div class="bg-green-50 text-green-700 font-black text-sm px-3 py-1 rounded-full border border-green-100">
                                {{ offering.commission_rate }}% Com.
                            </div>
                        </div>

                        <div class="mt-auto grid grid-cols-2 gap-4">
                            <button
                                @click="copyLink(offering.id)"
                                class="flex items-center justify-center gap-2 bg-slate-100 text-slate-700 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition active:scale-95"
                            >
                                <Share2 :size="16" /> Link
                            </button>
                            <button
                                @click="handleDownload(offering)"
                                :disabled="isGenerating"
                                class="flex items-center justify-center gap-2 bg-indigo-600 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 active:scale-95 disabled:opacity-70"
                            >
                                <Loader v-if="isGenerating" :size="16" class="animate-spin" />
                                <Download v-else :size="16" />
                                Imagen
                            </button>
                        </div>
                    </div>
                </Card>
            </div>

            <div v-if="filteredOfferings.length === 0" class="text-center py-24 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                <div class="inline-flex p-4 rounded-full bg-slate-100 text-slate-400 mb-4">
                    <Briefcase :size="48" />
                </div>
                <h3 class="text-xl font-bold text-slate-400">No hay ofertas disponibles</h3>
                <p class="text-slate-400">Intenta filtrando por otra categoría</p>
            </div>

            <canvas ref="canvasRef" class="hidden"></canvas>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
