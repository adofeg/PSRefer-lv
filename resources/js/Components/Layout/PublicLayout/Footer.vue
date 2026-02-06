<script setup>
const year = new Date().getFullYear();
</script>

<template>
    <footer class="bg-slate-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white flex items-center justify-center text-xs font-bold">PS</div>
                        <div>
                            <div class="text-sm font-extrabold">PS Refer</div>
                            <div class="text-[11px] text-slate-400">Catálogo & Referencias</div>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-400">
                        Plataforma profesional para gestión de servicios, referencias y comisiones.
                    </p>
                </div>

                <div>
                    <h4 class="text-sm font-semibold">Cumplimiento</h4>
                    <p class="mt-3 text-sm text-slate-400">
                        Reglas claras para roles, conflicto de interés y comisiones transparentes.
                    </p>
                </div>
            </div>

            <div class="mt-10 border-t border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>© {{ year }} PSRefer. Todos los derechos reservados.</p>
                <p>Seguridad y cumplimiento para referencias profesionales.</p>
            </div>
        </div>
    </footer>
</template>
