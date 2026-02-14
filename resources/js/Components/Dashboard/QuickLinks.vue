<script setup>
import { ref, onMounted } from 'vue';
import { Copy, Check, QrCode, Download, X, Briefcase, HeartPulse, Users } from 'lucide-vue-next';
import axios from 'axios';
import { copyText } from '@/Utils/clipboard';

const props = defineProps({
    user: Object
});

const copiedId = ref(null);
const showQrModal = ref(null);
const qrCodes = ref({});
const links = ref([]);

const loadLinks = async () => {
    // We'll simulate the specialized links logic for parity
    const baseUrl = window.location.origin;
    
    // In a real app, these could come from an API
    links.value = [
        {
            id: 'general',
            title: 'Enlace General (Associate)',
            description: 'Link para registro de nuevos asociados en tu red.',
            url: `${baseUrl}/register?ref=${props.user.id}`,
            icon: Users,
            color: 'bg-indigo-600'
        },
        {
            id: 'commercial',
            title: 'Referencia Comercial',
            description: 'Link para referir servicios comerciales y marketing.',
            url: `${baseUrl}/apply/referencia-general?ref=${props.user.id}`,
            icon: Briefcase,
            color: 'bg-blue-600'
        },
        {
            id: 'medical',
            title: 'Referencia Médica',
            description: 'Link específico para servicios especializados de salud.',
            url: `${baseUrl}/apply/referencia-general?ref=${props.user.id}&cat=medical`,
            icon: HeartPulse,
            color: 'bg-rose-600'
        }
    ];

    // Pre-generate QR URLs
    links.value.forEach(link => {
        qrCodes.value[link.id] = `https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=${encodeURIComponent(link.url)}&format=png&margin=2&color=1e293b&bgcolor=ffffff`;
    });
};

const copyToClipboard = async (link) => {
    try {
        const copied = await copyText(link.url);
        if (!copied) {
            window.prompt('Copia este enlace manualmente:', link.url);
            return;
        }

        copiedId.value = link.id;
        setTimeout(() => copiedId.value = null, 2000);
        
        // Optional: track click via API (Parity with JS)
        // axios.post('/api/referral-links/track', { type: link.id });
    } catch (err) {
        console.error('Failed to copy!', err);
    }
};

const downloadQr = async (link) => {
    const qrUrl = qrCodes.value[link.id];
    try {
        const response = await fetch(qrUrl);
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `qr-${link.id}-${props.user.name.split(' ')[0]}.png`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    } catch (e) {
        window.open(qrUrl, '_blank');
    }
};

onMounted(loadLinks);
</script>

<template>
    <div class="bg-white/90 rounded-2xl shadow-sm border border-slate-200/80 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <QrCode :size="20" class="text-indigo-600" />
            Links de Referencia Rápida
        </h3>
        <p class="text-sm text-slate-600 mb-6">
            Comparte estos enlaces para referir colegas y clientes. Cada enlace incluye tu código único.
        </p>

        <div class="space-y-4">
            <div
                v-for="link in links"
                :key="link.id"
                class="flex items-center gap-3 p-4 bg-slate-50 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors"
            >
                <div :class="['p-2 rounded-lg text-white shadow-sm', link.color]">
                    <component :is="link.icon" :size="20" />
                </div>

                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-slate-800 text-sm">
                        {{ link.title }}
                    </div>
                    <div class="text-xs text-slate-600 mb-2 truncate">
                        {{ link.description }}
                    </div>
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            :value="link.url"
                            readOnly
                            class="flex-1 px-3 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono"
                            @click="$event.target.select()"
                        />
                        <button
                            @click="copyToClipboard(link)"
                            :class="[
                                'px-3 py-1.5 rounded-md text-white text-xs font-medium transition-all flex items-center gap-1 min-w-[80px] justify-center',
                                copiedId === link.id ? 'bg-green-600' : 'bg-indigo-600 hover:bg-indigo-700'
                            ]"
                        >
                            <Check v-if="copiedId === link.id" :size="14" />
                            <Copy v-else :size="14" />
                            {{ copiedId === link.id ? 'Copiado' : 'Copiar' }}
                        </button>
                        <button
                            @click="showQrModal = link"
                            class="p-1.5 rounded-md bg-slate-200 text-slate-600 hover:bg-slate-300 transition-colors"
                            title="Mostrar QR"
                        >
                            <QrCode :size="16" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal QR Preview -->
        <Teleport to="body">
            <div v-if="showQrModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4" @click.self="showQrModal = null">
                <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden animate-scale-in">
                    <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h4 class="font-bold text-slate-800">Código QR</h4>
                        <button @click="showQrModal = null" class="text-slate-400 hover:text-slate-600 transition">
                            <X :size="20" />
                        </button>
                    </div>
                    <div class="p-8 flex flex-col items-center gap-6">
                        <div class="p-4 bg-white border-4 border-slate-100 rounded-2xl shadow-sm">
                            <img :src="qrCodes[showQrModal.id]" alt="QR Code" class="w-64 h-64" />
                        </div>
                        <div class="text-center space-y-2">
                            <p class="font-medium text-slate-800">{{ showQrModal.title }}</p>
                            <p class="text-xs text-slate-500 max-w-[240px]">{{ showQrModal.description }}</p>
                        </div>
                        <button 
                            @click="downloadQr(showQrModal)"
                            class="w-full flex items-center justify-center gap-2 bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition"
                        >
                            <Download :size="18" />
                            Descargar Imagen
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.animate-scale-in {
    animation: scaleIn 0.2s ease-out forwards;
}

@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>
