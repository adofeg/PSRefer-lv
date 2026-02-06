<script setup>
const year = new Date().getFullYear();
</script>

<template>
    <footer class="border-t border-slate-200 bg-white/90 backdrop-blur px-4 py-4 md:px-8">
        <div class="w-full text-xs text-slate-500">
            © {{ year }} PSRefer. Todos los derechos reservados.
        </div>
    </footer>
</template>
