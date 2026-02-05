<script setup>
import { Menu, Bell } from 'lucide-vue-next';

defineProps({
    user: Object
});

defineEmits(['toggleMenu']);
</script>

<template>
    <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 md:px-8">
        <button @click="$emit('toggleMenu')" class="md:hidden text-slate-600 hover:text-slate-900">
            <Menu :size="24" />
        </button>

        <div class="flex items-center gap-4 ml-auto">
            <button class="relative p-2 text-slate-400 hover:text-indigo-600 transition">
                <Bell :size="20" />
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            <div class="flex items-center gap-3 pl-4 border-l border-slate-100">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-800">{{ user?.name }}</p>
                    <p class="text-xs text-slate-500 capitalize">{{ user?.role }}</p>
                </div>
                <img
                    :src="user?.logo_url || 'https://ui-avatars.com/api/?name=' + user?.name"
                    alt="User"
                    class="w-10 h-10 rounded-full bg-slate-100 object-cover border border-slate-200"
                />
            </div>
        </div>
    </header>
</template>
