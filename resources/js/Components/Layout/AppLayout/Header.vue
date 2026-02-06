<script setup>
import { Menu, X, ChevronDown, LogOut, User } from 'lucide-vue-next';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    sidebarOpen: {
        type: Boolean,
        default: true
    }
});

defineEmits(['toggleMenu']);

const menuOpen = ref(false);

const handleDocumentClick = (event) => {
    if (!event.target.closest('[data-user-menu]')) {
        menuOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
});
</script>

<template>
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200/80 h-16 flex items-center justify-between px-4 md:px-8 shadow-sm">
        <div class="flex items-center gap-3">
            <button
                @click="$emit('toggleMenu')"
                class="group inline-flex items-center gap-2 h-10 px-3 rounded-xl text-slate-700 bg-slate-100/60 hover:bg-slate-200/70 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/50"
                :aria-expanded="sidebarOpen"
                aria-label="Mostrar/Ocultar menú"
            >
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white/70 border border-slate-200 text-slate-700 group-hover:text-slate-900">
                    <Menu v-if="!sidebarOpen" :size="18" />
                    <X v-else :size="18" />
                </span>
                <span class="hidden sm:inline text-xs font-semibold tracking-wide uppercase">Menú</span>
            </button>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative" data-user-menu>
                <button
                    type="button"
                    class="group flex items-center gap-3 pl-4 border-l border-slate-200 rounded-xl hover:bg-slate-50/80 transition pr-2 py-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/40"
                    @click.stop="menuOpen = !menuOpen"
                    :aria-expanded="menuOpen"
                >
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-800">{{ user?.name }}</p>
                        <p class="text-xs text-slate-500">{{ user?.email }}</p>
                    </div>
                    <div class="relative">
                        <span
                            class="absolute -inset-0.5 rounded-full bg-indigo-500/20 blur opacity-0 transition duration-300 group-hover:opacity-100"
                        ></span>
                        <img
                            :src="user?.logo_url || 'https://ui-avatars.com/api/?name=' + user?.name"
                            alt="User"
                            class="relative w-10 h-10 rounded-full bg-slate-100 object-cover border border-slate-200 shadow-sm ring-2 ring-white"
                        />
                    </div>
                    <ChevronDown
                        :size="16"
                        class="text-slate-400 hidden sm:block transition"
                        :class="menuOpen ? 'rotate-180' : 'rotate-0'"
                    />
                </button>

                <transition name="dropdown-fade">
                    <div
                        v-if="menuOpen"
                        class="absolute right-0 mt-2 w-56 rounded-2xl border border-slate-200/80 bg-white shadow-xl overflow-hidden z-50 origin-top-right"
                    >
                        <div class="p-3">
                            <p class="text-sm font-semibold text-slate-800">{{ user?.name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ user?.email }}</p>
                        </div>
                        <div class="h-px bg-slate-100"></div>
                        <Link
                            :href="route('settings')"
                            class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50"
                        >
                            <User :size="16" />
                            Perfil
                        </Link>
                        <div class="h-px bg-slate-100"></div>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="flex items-center gap-2 w-full px-4 py-3 text-sm text-slate-700 hover:bg-slate-50"
                        >
                            <LogOut :size="16" />
                            Cerrar sesión
                        </Link>
                    </div>
                </transition>
            </div>
        </div>
    </header>
</template>

<style scoped>
.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
    transition: opacity 0.18s ease, transform 0.18s ease;
}
.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
    opacity: 0;
    transform: translateY(-6px) scale(0.98);
}
</style>
