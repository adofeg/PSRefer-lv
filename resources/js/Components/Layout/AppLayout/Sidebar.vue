<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    LayoutDashboard,
    ShoppingBag,
    Users,
    DollarSign,
    LogOut,
    X,
    LayoutGrid,
    Image as ImageIcon,
    Tag,
    Mail,
    UserCog,
    ArrowRightLeft,
    BarChart3,
    Settings,
    ClipboardList
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

const isActiveAny = (routes) => {
    return routes.some((routeStr) => route().current(routeStr));
};

const adminRoles = ['admin', 'psadmin'];
const associateRoles = ['associate'];
const psAdminRoles = ['psadmin'];

const currentRole = computed(() => {
    return props.user?.role || page.props.auth?.user?.role || 'guest';
});

const navigationSections = [
    {
        label: 'General',
        items: [
            { name: 'Dashboard', href: route('admin.dashboard'), icon: LayoutDashboard, current: isActiveAny(['admin.dashboard', 'dashboard']), roles: [...adminRoles, ...associateRoles] },
            { name: 'Catálogo', href: route('admin.offerings.index'), icon: ShoppingBag, current: isActive('admin.offerings.index'), roles: [...adminRoles, ...associateRoles] },
            { name: 'Referidos', href: route('admin.referrals.index'), icon: Users, current: isActive('admin.referrals.index'), roles: [...adminRoles, ...associateRoles] },
        ]
    },
    {
        label: 'Gestión de Comisiones',
        items: [
             { name: 'Operaciones', href: route('admin.commissions.index'), icon: ArrowRightLeft, current: isActive('admin.commissions.index'), roles: adminRoles },
             { name: 'Reportes Financieros', href: route('admin.commissions.report'), icon: BarChart3, current: isActive('admin.commissions.report'), roles: adminRoles },
             { name: 'Reglas y Excepciones', href: route('admin.commissions.overrides.index'), icon: Settings, current: isActive('admin.commissions.overrides.index'), roles: adminRoles },
        ]
    },
    {
        label: 'Associates Only',
        items: [
             // Associate specific links can go here if distinct from dynamic role Filtering
             { name: 'Mis Comisiones', href: route('admin.commissions.index'), icon: DollarSign, current: isActive('admin.commissions.index'), roles: associateRoles },
        ]
    },
    {
        label: 'Administración',
        items: [
            { name: 'Usuarios', href: route('admin.users.index'), icon: UserCog, current: isActive('admin.users.index'), roles: adminRoles },
            { name: 'Categorías', href: route('admin.categories.index'), icon: Tag, current: isActive('admin.categories.index'), roles: ['admin'] },
            { name: 'Pipeline', href: route('admin.referrals.pipeline'), icon: LayoutGrid, current: isActive('admin.referrals.pipeline'), roles: ['admin', 'psadmin'] },
            { name: 'SMTP Config', href: route('admin.settings.smtp'), icon: Mail, current: isActive('admin.settings.smtp'), roles: ['admin'] },
            { name: 'Bitácora', href: route('admin.audit-logs.index'), icon: ClipboardList, current: isActive('admin.audit-logs.index'), roles: ['admin'] },
            { name: 'Marketing', href: route('admin.marketing'), icon: ImageIcon, current: isActive('admin.marketing'), roles: ['admin'] },
        ]
    },
];

const visibleNavigationSections = computed(() => {
    return navigationSections
        .map((section) => ({
            ...section,
            items: section.items.filter((item) => item.roles.includes(currentRole.value)),
        }))
        .filter((section) => section.items.length > 0);
});
</script>

<template>
    <div
        :class="[
            'fixed inset-y-0 left-0 z-50 w-64 h-screen bg-gradient-to-b from-slate-950 via-slate-950 to-slate-900 text-white transform transition-transform duration-300 ease-in-out border-r border-slate-800/60 shadow-2xl',
            isOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
    >
        <div class="flex items-center justify-between p-4 border-b border-slate-800/70">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center font-bold text-sm shadow-sm">PS</div>
                <div class="leading-tight">
                    <span class="font-bold text-base">PS Refer</span>
                    <p class="text-xs text-slate-400 capitalize">{{ currentRole }}</p>
                </div>
            </div>
            <button @click="$emit('close')" class="md:hidden text-slate-400 hover:text-white hover:bg-slate-800/60 rounded-lg p-1 transition">
                <X :size="24" />
            </button>
        </div>

        <nav class="px-4 py-5 space-y-6 overflow-y-auto h-[calc(100vh-120px)]">
            <div v-for="section in visibleNavigationSections" :key="section.label">
                <p class="px-3 text-[11px] uppercase tracking-widest text-slate-500">{{ section.label }}</p>
                <div class="mt-2 space-y-1">
                    <Link
                        v-for="item in section.items"
                        :key="item.name"
                        :href="item.href"
                        class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 border"
                        :class="item.current ? 'bg-indigo-600/95 text-white shadow-sm ring-1 ring-white/10 border-indigo-400/30 translate-x-[2px]' : 'text-slate-300 hover:bg-slate-900/80 hover:text-white border-transparent hover:translate-x-[2px]'
                        "
                        :aria-current="item.current ? 'page' : undefined"
                    >
                        <span
                            class="flex items-center justify-center w-8 h-8 rounded-lg transition"
                            :class="item.current ? 'bg-white/10 text-white' : 'bg-slate-900/60 text-slate-300 group-hover:bg-slate-800/80 group-hover:text-white'"
                        >
                            <component :is="item.icon" :size="18" />
                        </span>
                        <span class="flex-1">
                        {{ item.name }}
                        </span>
                        <span
                            class="h-2 w-2 rounded-full transition"
                            :class="item.current ? 'bg-white/70' : 'bg-transparent group-hover:bg-white/30'"
                        ></span>
                    </Link>
                </div>
            </div>
        </nav>

        <div class="absolute bottom-0 w-full p-4 border-t border-slate-800/70">
            <div class="text-xs text-slate-500">© {{ new Date().getFullYear() }} PSRefer</div>
        </div>
    </div>

    <!-- Overlay -->
    <div v-if="isOpen" @click="$emit('close')" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 md:hidden"></div>
</template>
