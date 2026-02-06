<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Sidebar from '@/Components/Layout/AppLayout/Sidebar.vue';
import Header from '@/Components/Layout/AppLayout/Header.vue';
import AppFooter from '@/Components/Layout/AppLayout/Footer.vue';

const props = defineProps({
    user: Object,
});

const sidebarOpen = ref(true);
const page = usePage();
const pageKey = computed(() => page.component || page.url);
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <div class="flex min-h-screen">
            <Sidebar
                :user="$page.props.auth.user"
                :isOpen="sidebarOpen"
                @close="sidebarOpen = false"
            />

            <div
                class="flex-1 flex flex-col min-w-0"
                :class="sidebarOpen ? 'md:pl-64' : 'md:pl-0'"
            >
                <Header
                    :user="$page.props.auth.user"
                    :sidebar-open="sidebarOpen"
                    @toggleMenu="sidebarOpen = !sidebarOpen"
                />

                <main class="flex-1 overflow-y-auto scroll-smooth bg-slate-50">
                    <div class="px-4 md:px-8 py-6">
                        <transition name="page-fade" mode="out-in" appear>
                            <div :key="pageKey" class="w-full">
                                <slot />
                            </div>
                        </transition>
                    </div>
                </main>

                <AppFooter />
            </div>
        </div>
    </div>
</template>

<style scoped>
.page-fade-enter-active,
.page-fade-leave-active {
    transition: opacity 0.18s ease, transform 0.18s ease;
}
.page-fade-enter-from,
.page-fade-leave-to {
    opacity: 0;
    transform: translateY(6px);
}
</style>