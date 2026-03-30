<script setup>
import { Bell, Calendar, X, Check } from 'lucide-vue-next';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const notifications = ref([]);
const unreadCount = ref(0);
const isOpen = ref(false);

const fetchNotifications = async () => {
    try {
        const response = await axios.get('/api/notifications');
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.unread_count;
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    }
};

const markAsRead = async (id) => {
    try {
        await axios.post(`/api/notifications/${id}/mark-as-read`);
        await fetchNotifications();
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

const markAllRead = async () => {
    try {
        await axios.post('/api/notifications/mark-all-as-read');
        await fetchNotifications();
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error);
    }
};

const handleNotificationClick = (notification) => {
    markAsRead(notification.id);
    isOpen.value = false;
    if (notification.data?.url) {
        router.visit(notification.data.url);
    }
};

const toggleBell = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        fetchNotifications();
    }
};

const handleClickOutside = (event) => {
    if (!event.target.closest('[data-notification-bell]')) {
        isOpen.value = false;
    }
};

let interval;
onMounted(() => {
    fetchNotifications();
    document.addEventListener('click', handleClickOutside);
    // Poll every 2 minutes
    interval = setInterval(fetchNotifications, 120000);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    clearInterval(interval);
});
</script>

<template>
    <div class="relative" data-notification-bell>
        <!-- Bell Trigger -->
        <button
            @click="toggleBell"
            class="relative p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-xl transition"
            aria-label="Notificaciones"
        >
            <Bell :size="22" />
            <span
                v-if="unreadCount > 0"
                class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center ring-2 ring-white animate-pulse"
            >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <transition name="fade-slide">
            <div
                v-if="isOpen"
                class="absolute right-0 mt-3 w-80 md:w-96 bg-white border border-slate-200 shadow-2xl rounded-2xl overflow-hidden z-50 origin-top-right"
            >
                <!-- Header -->
                <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <Bell :size="16" class="text-indigo-600" />
                        Notificaciones
                    </h3>
                    <button 
                        v-if="unreadCount > 0"
                        @click="markAllRead" 
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition"
                    >
                        Marcar todas como leídas
                    </button>
                </div>

                <!-- List -->
                <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                    <div v-if="notifications.length === 0" class="p-8 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <Check :size="24" class="text-slate-300" />
                        </div>
                        <p class="text-sm text-slate-400 font-medium italic">No tienes notificaciones nuevas</p>
                    </div>

                    <div 
                        v-for="notification in notifications" 
                        :key="notification.id"
                        @click="handleNotificationClick(notification)"
                        class="p-4 border-b border-slate-50 hover:bg-slate-50/80 cursor-pointer transition flex gap-3 group items-start"
                    >
                        <!-- Icon based on type -->
                        <div class="shrink-0 mt-0.5">
                            <div v-if="notification.data?.type === 'agenda'" class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 group-hover:bg-amber-100 transition">
                                <Calendar :size="18" />
                            </div>
                            <div v-else class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-100 transition">
                                <Bell :size="18" />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 leading-tight mb-1 truncate">
                                {{ notification.data?.client_name || 'Alerta de Sistema' }}
                            </p>
                            <p class="text-xs text-slate-500 leading-relaxed mb-2">
                                {{ notification.data?.message || 'Tienes una actividad pendiente.' }}
                            </p>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                {{ new Date(notification.created_at).toLocaleDateString() }}
                            </span>
                        </div>
                        
                        <!-- Actions -->
                        <div class="shrink-0 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition">
                            <button 
                                @click.stop="markAsRead(notification.id)" 
                                class="p-1 px-2.5 bg-green-50 text-green-600 rounded-full text-[10px] font-bold hover:bg-green-100 transition"
                                title="Marcar como leída"
                            >
                                <Check :size="12" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-3 bg-slate-50/30 text-center border-t border-slate-100">
                    <button @click="isOpen = false" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition">
                        Cerrar panel
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>
