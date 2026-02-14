<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    LayoutDashboard,
    ShoppingBag,
    Users,
    DollarSign,
    X,
    LayoutGrid,
    Image as ImageIcon,
    Tag,
    Mail,
    UserCog,
    ArrowRightLeft,
    BarChart3,
    Settings,
    ClipboardList,
    ChevronDown,
    ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
    user: Object,
    isOpen: Boolean
});

const emit = defineEmits(['close']);
const page = usePage();

const routeIsCurrent = (patterns = []) => {
    return patterns.some((pattern) => route().current(pattern));
};

const adminRoles = ['admin', 'psadmin'];
const associateRoles = ['associate'];

const currentRole = computed(() => {
    return props.user?.role || page.props.auth?.user?.role || 'guest';
});

const navigationGroups = [
    {
        label: 'Gestión Financiera',
        items: [
             { name: 'Comisiones', href: route('admin.commissions.index'), icon: ArrowRightLeft, match: ['admin.commissions.index'], roles: adminRoles },
             { name: 'Reportes', href: route('admin.commissions.report'), icon: BarChart3, match: ['admin.commissions.report'], roles: adminRoles },
             { name: 'Reglas y Ajustes', href: route('admin.commissions.overrides.index'), icon: Settings, match: ['admin.commissions.overrides.index'], roles: adminRoles },
             { name: 'Mis Comisiones', href: route('associate.commissions'), icon: DollarSign, match: ['associate.commissions'], roles: associateRoles },
        ]
    },
    {
        label: 'Ajustes del Sistema',
        items: [
            { name: 'Usuarios', href: route('admin.users.index'), icon: UserCog, match: ['admin.users.index'], roles: adminRoles },
            { name: 'Marketing', href: route('admin.marketing'), icon: ImageIcon, match: ['admin.marketing'], roles: ['admin'] },
            { name: 'Categorías', href: route('admin.categories.index'), icon: Tag, match: ['admin.categories.index'], roles: ['admin'] },
            { name: 'Bitácora', href: route('admin.audit-logs.index'), icon: ClipboardList, match: ['admin.audit-logs.index'], roles: ['admin'] },
            { name: 'Configuración SMTP', href: route('admin.settings.smtp'), icon: Mail, match: ['admin.settings.smtp'], roles: ['admin'] },
        ]
    },
];

const standaloneItems = [
    { name: 'Dashboard', href: route('admin.dashboard'), icon: LayoutDashboard, match: ['admin.dashboard'], roles: adminRoles },
    { name: 'Dashboard', href: route('associate.dashboard'), icon: LayoutDashboard, match: ['associate.dashboard'], roles: associateRoles },
    
    { name: 'Catálogo', href: route('admin.offerings.index'), icon: ShoppingBag, match: ['admin.offerings.index'], roles: adminRoles },
    { name: 'Catálogo', href: route('associate.offerings.index'), icon: ShoppingBag, match: ['associate.offerings.index'], roles: associateRoles },
    
    { name: 'Referidos', href: route('admin.referrals.index'), icon: Users, match: ['admin.referrals.index'], roles: adminRoles },
    { name: 'Referidos', href: route('associate.referrals.index'), icon: Users, match: ['associate.referrals.index'], roles: associateRoles },
    
    { name: 'Pipeline', href: route('admin.referrals.pipeline'), icon: LayoutGrid, match: ['admin.referrals.pipeline'], roles: ['admin', 'psadmin'] },
];

const itemIsCurrent = (item) => routeIsCurrent(item.match || []);

const visibleStandaloneItems = computed(() => {
    return standaloneItems.filter((item) => item.roles.includes(currentRole.value));
});

const visibleNavigationGroups = computed(() => {
    return navigationGroups
        .map((group) => ({
            ...group,
            items: group.items.filter((item) => item.roles.includes(currentRole.value)),
        }))
        .filter((group) => group.items.length > 0);
});

// Helper to check if a group should be expanded based on current route
const isGroupActive = (label) => {
    if (label === 'Gestión Financiera') {
        return routeIsCurrent(['admin.commissions.*', 'associate.commissions']);
    }
    if (label === 'Ajustes del Sistema') {
        return routeIsCurrent([
            'admin.users.*', 
            'admin.marketing*', 
            'admin.categories.*', 
            'admin.audit-logs.*', 
            'admin.settings.*'
        ]);
    }
    return false;
};

// State for collapsible sections - Managed to force expand on active routes
const manualToggles = ref({});
const expandedSections = computed(() => {
    const state = {};
    visibleNavigationGroups.value.forEach(group => {
        // If it's active by route, it MUST be expanded. 
        // Otherwise, respect manual toggle or default to false.
        const isActive = isGroupActive(group.label);
        state[group.label] = isActive || (manualToggles.value[group.label] ?? false);
    });
    return state;
});

const toggleSection = (label) => {
    // If the group is active, we might want to allow closing it manually, 
    // but the user specifically said "no debe de ocultarse".
    // Let's allow toggling but the computed property will handle the "always open if active" logic if we want.
    // However, if we want to allow closing even if active, we'd need to change logic.
    // The user's request "ese grupo no debe de ocultarse por que estoy en unaa opción del grupo"
    // suggests that it should stay open NO MATTER WHAT if active.
    
    if (isGroupActive(label)) return; // Prevent closing if active
    manualToggles.value[label] = !expandedSections.value[label];
};
</script>

<template>
    <div
        :class="[
            'fixed inset-y-0 left-0 z-50 w-64 h-screen bg-slate-950 text-white transform transition-transform duration-300 ease-in-out border-r border-slate-800 shadow-2xl',
            isOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
    >
        <div class="flex items-center justify-between p-4 h-16 border-b border-slate-800/60">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center font-bold text-xs shadow-lg shadow-indigo-900/40">PS</div>
                <div class="leading-tight">
                    <span class="font-bold text-sm tracking-tight text-slate-100 uppercase">PS Refer</span>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">{{ currentRole }}</p>
                </div>
            </div>
            <button @click="$emit('close')" class="md:hidden text-slate-400 hover:text-white transition">
                <X :size="20" />
            </button>
        </div>

        <nav class="px-4 py-6 space-y-6 overflow-y-auto h-[calc(100vh-64px)] scrollbar-hide">
            <!-- Core Standalone Navigation -->
            <div class="space-y-1">
                <Link
                    v-for="item in visibleStandaloneItems"
                    :key="item.name"
                    :href="item.href"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 border border-transparent"
                    :class="itemIsCurrent(item) ? 'bg-slate-800/80 text-white border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-900/60 hover:text-white'
                    "
                >
                    <div 
                        class="flex items-center justify-center w-8 h-8 rounded-lg transition shrink-0"
                        :class="itemIsCurrent(item) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-900 text-slate-500 group-hover:bg-slate-800 group-hover:text-slate-300'"
                    >
                        <component :is="item.icon" :size="18" />
                    </div>
                    <span class="flex-1 font-semibold text-sm">
                    {{ item.name }}
                    </span>
                    <span
                        v-if="itemIsCurrent(item)"
                        class="h-1.5 w-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,1)]"
                    ></span>
                </Link>
            </div>

            <!-- Grouped Sections -->
            <div v-for="group in visibleNavigationGroups" :key="group.label" class="space-y-1">
                <button 
                    @click="toggleSection(group.label)"
                    class="w-full flex items-center justify-between px-3 py-2 group select-none"
                >
                    <span class="text-[10px] uppercase tracking-[0.15em] font-black text-slate-500 group-hover:text-indigo-400 transition">{{ group.label }}</span>
                    <component 
                        :is="expandedSections[group.label] ? ChevronDown : ChevronRight" 
                        :size="12" 
                        class="text-slate-600 group-hover:text-slate-400 transition"
                    />
                </button>
                
                <div 
                    v-show="expandedSections[group.label]" 
                    :class="[
                        'space-y-1 overflow-hidden',
                        isGroupActive(group.label) ? '' : 'animate-slide-down'
                    ]"
                >
                    <Link
                        v-for="item in group.items"
                        :key="item.name"
                        :href="item.href"
                        class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 border border-transparent"
                        :class="itemIsCurrent(item) ? 'bg-slate-800/80 text-white border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-900/40 hover:text-white'
                        "
                        :aria-current="itemIsCurrent(item) ? 'page' : undefined"
                    >
                        <div 
                            class="flex items-center justify-center w-8 h-8 rounded-lg transition shrink-0"
                            :class="itemIsCurrent(item) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-900 text-slate-500 group-hover:bg-slate-800 group-hover:text-slate-300'"
                        >
                            <component :is="item.icon" :size="16" />
                        </div>
                        <span class="flex-1 text-sm font-semibold">
                        {{ item.name }}
                        </span>
                        <span
                            v-if="itemIsCurrent(item)"
                            class="h-1.5 w-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,1)]"
                        ></span>
                    </Link>
                </div>
            </div>
        </nav>

        <div class="absolute bottom-0 w-full p-4 border-t border-slate-800/60 bg-slate-950/80 backdrop-blur-md">
            <div class="text-[10px] text-slate-600 uppercase font-bold tracking-widest">© {{ new Date().getFullYear() }} PSRefer</div>
        </div>
    </div>

    <!-- Overlay -->
    <div v-if="isOpen" @click="$emit('close')" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden"></div>
</template>
