<script setup>
import { ref, onMounted, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Download, Image as ImageIcon, Sparkles, Palette, RefreshCw } from 'lucide-vue-next';

const props = defineProps({
    offerings: Array,
    user: Object
});

const selectedOffering = ref(null);
const canvasRef = ref(null);
const selectedTemplate = ref('gradient');
const customColor1 = ref('#6366f1'); // indigo-600
const customColor2 = ref('#a855f7'); // purple-600

const templates = [
    { id: 'gradient', name: 'Gradiente Premium', colors: ['#6366f1', '#a855f7'] },
    { id: 'blue', name: 'Azul Profesional', colors: ['#0284c7', '#0ea5e9'] },
    { id: 'green', name: 'Verde Confianza', colors: ['#059669', '#10b981'] },
    { id: 'orange', name: 'Naranja Energía', colors: ['#ea580c', '#f97316'] },
    { id: 'pink', name: 'Rosa Moderno', colors: ['#db2777', '#ec4899'] },
    { id: 'dark', name: 'Oscuro Elegante', colors: ['#1e293b', '#334155'] },
];

const selectOffering = (offering) => {
    selectedOffering.value = offering;
    setTimeout(generateCanvas, 100);
};

const selectTemplate = (template) => {
    selectedTemplate.value = template.id;
    customColor1.value = template.colors[0];
    customColor2.value = template.colors[1];
    if (selectedOffering.value) {
        setTimeout(generateCanvas, 100);
    }
};

const generateCanvas = () => {
    if (!canvasRef.value || !selectedOffering.value) return;
    
    const canvas = canvasRef.value;
    const ctx = canvas.getContext('2d');
    
    // Set canvas size (Instagram square)
    canvas.width = 1080;
    canvas.height = 1080;
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Background gradient
    const gradient = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
    gradient.addColorStop(0, customColor1.value);
    gradient.addColorStop(1, customColor2.value);
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Semi-transparent overlay for better text readability
    ctx.fillStyle = 'rgba(0, 0, 0, 0.15)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Top decoration
    ctx.fillStyle = 'rgba(255, 255, 255, 0.1)';
    ctx.beginPath();
    ctx.arc(200, 150, 300, 0, Math.PI * 2);
    ctx.fill();
    
    ctx.beginPath();
    ctx.arc(900, 200, 250, 0, Math.PI * 2);
    ctx.fill();
    
    // Main content box
    ctx.fillStyle = 'rgba(255, 255, 255, 0.95)';
    ctx.shadowColor = 'rgba(0, 0, 0, 0.2)';
    ctx.shadowBlur = 30;
    ctx.shadowOffsetY = 10;
    ctx.roundRect(80, 250, 920, 600, 30);
    ctx.fill();
    ctx.shadowBlur = 0;
    ctx.shadowOffsetY = 0;
    
    // Offering name
    ctx.fillStyle = '#1e293b';
    ctx.font = 'bold 72px Inter, system-ui, sans-serif';
    ctx.textAlign = 'center';
    
    const offeringName = selectedOffering.value.name;
    const maxWidth = 800;
    let fontSize = 72;
    ctx.font = `bold ${fontSize}px Inter, system-ui, sans-serif`;
    
    // Auto-adjust font size if text is too long
    while (ctx.measureText(offeringName).width > maxWidth && fontSize > 40) {
        fontSize -= 2;
        ctx.font = `bold ${fontSize}px Inter, system-ui, sans-serif`;
    }
    
    ctx.fillText(offeringName, canvas.width / 2, 380);
    
    // Commission rate (if exists)
    if (selectedOffering.value.commission_rate) {
        ctx.fillStyle = customColor1.value;
        ctx.font = 'bold 56px Inter, system-ui, sans-serif';
        ctx.fillText(`${selectedOffering.value.commission_rate}% Comisión`, canvas.width / 2, 480);
    }
    
    // Description (truncated)
    if (selectedOffering.value.description) {
        ctx.fillStyle = '#64748b';
        ctx.font = '32px Inter, system-ui, sans-serif';
        const desc = selectedOffering.value.description.substring(0, 80) + '...';
        ctx.fillText(desc, canvas.width / 2, 560);
    }
    
    // Category badge
    if (selectedOffering.value.category) {
        ctx.fillStyle = 'rgba(99, 102, 241, 0.1)';
        ctx.fillRect(340, 630, 400, 60);
        ctx.fillStyle = customColor1.value;
        ctx.font = 'bold 28px Inter, system-ui, sans-serif';
        ctx.fillText(selectedOffering.value.category, canvas.width / 2, 670);
    }
    
    // Bottom section - User info
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(80, 850, 920, 150);
    
    // Separator line
    ctx.strokeStyle = customColor1.value;
    ctx.lineWidth = 4;
    ctx.beginPath();
    ctx.moveTo(80, 850);
    ctx.lineTo(1000, 850);
    ctx.stroke();
    
    // User name
    ctx.fillStyle = '#1e293b';
    ctx.font = 'bold 48px Inter, system-ui, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(props.user.name, canvas.width / 2, 920);
    
    // Contact info
    const contact = props.user.phone || props.user.email;
    ctx.fillStyle = '#64748b';
    ctx.font = '36px Inter, system-ui, sans-serif';
    ctx.fillText(contact, canvas.width / 2, 975);
    
    // Branding
    ctx.fillStyle = customColor1.value;
    ctx.font = 'bold 28px Inter, system-ui, sans-serif';
    ctx.textAlign = 'right';
    ctx.fillText('PSRefer', 980, 1050);
};

const downloadImage = () => {
    if (!canvasRef.value || !selectedOffering.value) return;
    
    const canvas = canvasRef.value;
    const link = document.createElement('a');
    const fileName = `${selectedOffering.value.name.replace(/\s+/g, '-')}-promo.png`;
    link.download = fileName;
    link.href = canvas.toDataURL('image/png', 1.0);
    link.click();
};

const regenerateWithRandomColors = () => {
    const randomColor1 = '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
    const randomColor2 = '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
    customColor1.value = randomColor1;
    customColor2.value = randomColor2;
    selectedTemplate.value = 'custom';
    if (selectedOffering.value) {
        setTimeout(generateCanvas, 100);
    }
};

// Watch for color changes
watch([customColor1, customColor2], () => {
    if (selectedOffering.value) {
        setTimeout(generateCanvas, 100);
    }
});
</script>

<template>
    <Head title="Centro de Marketing" />

    <AppLayout>
        <div class="w-full space-y-8">
            <!-- Header -->
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl">
                        <Sparkles :size="28" class="text-white" />
                    </div>
                    <h1 class="text-3xl font-bold text-slate-900">Centro de Marketing</h1>
                </div>
                <p class="text-slate-600">
                    Genera gráficos promocionales personalizados para compartir en redes sociales
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left: Configuration -->
                <div class="space-y-6">
                    <!-- Offering Selector -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                            <ImageIcon :size="20" class="text-indigo-600" />
                            Selecciona una Oferta
                        </h3>
                        <div class="grid grid-cols-1 gap-3 max-h-96 overflow-y-auto">
                            <button
                                v-for="offering in offerings"
                                :key="offering.id"
                                @click="selectOffering(offering)"
                                class="text-left p-4 border-2 rounded-lg transition hover:border-indigo-500 hover:bg-indigo-50"
                                :class="selectedOffering?.id === offering.id 
                                    ? 'border-indigo-500 bg-indigo-50' 
                                    : 'border-slate-200'"
                            >
                                <p class="font-semibold text-slate-900">{{ offering.name }}</p>
                                <p class="text-sm text-slate-600 mt-1">{{ offering.category }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded">
                                        {{ offering.commission_rate }}% comisión
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Template Selector -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                            <Palette :size="20" class="text-indigo-600" />
                            Paleta de Colores
                        </h3>

                        <div class="grid grid-cols-2 gap-3">
                            <button
                                v-for="template in templates"
                                :key="template.id"
                                @click="selectTemplate(template)"
                                class="border-2 rounded-lg p-3 transition text-left"
                                :class="selectedTemplate === template.id ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200'"
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full"
                                        :style="`background: linear-gradient(135deg, ${template.colors[0]}, ${template.colors[1]})`"
                                    ></div>
                                    <span class="text-sm font-medium text-slate-700">{{ template.name }}</span>
                                </div>
                            </button>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <input v-model="customColor1" type="color" class="w-10 h-10 border border-slate-200 rounded-lg" />
                            <input v-model="customColor2" type="color" class="w-10 h-10 border border-slate-200 rounded-lg" />
                            <button
                                @click="regenerateWithRandomColors"
                                class="ml-auto flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                            >
                                <RefreshCw :size="16" />
                                Aleatorio
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right: Canvas Preview -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                                <Sparkles :size="20" class="text-indigo-600" />
                                Vista Previa
                            </h3>
                            <button
                                @click="downloadImage"
                                :disabled="!selectedOffering"
                                class="flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                <Download :size="16" />
                                Descargar
                            </button>
                        </div>

                        <div class="bg-slate-50 rounded-lg p-4 flex items-center justify-center min-h-[420px]">
                            <canvas ref="canvasRef" class="w-full max-w-[360px] rounded-lg shadow-md"></canvas>
                            <div v-if="!selectedOffering" class="text-center text-slate-400">
                                Selecciona una oferta para generar el diseño
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>