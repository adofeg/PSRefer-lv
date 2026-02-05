<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    ShoppingBag,
    Users,
    DollarSign,
    Network,
    Settings,
    LogOut,
    X
} from 'lucide-vue-next';

const props = defineProps({
    user: Object,
    isOpen: Boolean
});

const emit = defineEmits(['close']);
const page = usePage();

const isActive = (routeStr) => {
    return route().current(routeStr);
};

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: LayoutDashboard, current: isActive('dashboard') },
    { name: 'Catálogo', href: route('offerings.index'), icon: ShoppingBag, current: isActive('offerings.index') },
    { name: 'Referidos', href: route('referrals.index'), icon: Users, current: isActive('referrals.index') },
   // { name: 'Comisiones', href: route('commissions.index'), icon: DollarSign, current: isActive('commissions.index') },
   // { name: 'Mi Red', href: route('network.index'), icon: Network, current: isActive('network.index') },
];
</script>

<template>
    <div
        :class="[
            'fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
    >
        <div class="flex items-center justify-between p-4 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center font-bold">PS</div>
                <span class="font-bold text-lg">PS Refer</span>
            </div>
            <button @click="$emit('close')" class="md:hidden text-slate-400 hover:text-white">
                <X :size="24" />
            </button>
        </div>

        <nav class="p-4 space-y-1">
            <Link
                v-for="item in navigation"
                :key="item.name"
                :href="item.href"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
                :class="item.current ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
            >
                <component :is="item.icon" :size="20" />
                {{ item.name }}
            </Link>
        </nav>

        <div class="absolute bottom-0 w-full p-4 border-t border-slate-800">
            <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white w-full">
                <LogOut :size="20" />
                Cerrar Sesión
            </Link>
        </div>
    </div>

    <!-- Overlay -->
    <div v-if="isOpen" @click="$emit('close')" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>
</template>
