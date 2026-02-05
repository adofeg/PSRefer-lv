<script setup>
import { ref } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import Header from '@/Components/Header.vue';

const props = defineProps({
    user: Object,
});

const mobileMenuOpen = ref(false);
</script>

<template>
    <div class="flex h-screen bg-slate-50">
        <Sidebar
            :user="$page.props.auth.user"
            :isOpen="mobileMenuOpen"
            @close="mobileMenuOpen = false"
        />

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <Header
                :user="$page.props.auth.user"
                @toggleMenu="mobileMenuOpen = !mobileMenuOpen"
            />

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth">
                <div class="max-w-7xl mx-auto animate-fade-in">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
