<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-white to-indigo-50 px-4 py-10">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-indigo-200/40 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-purple-200/40 blur-3xl"></div>
        </div>

        <main class="relative z-10 w-full max-w-5xl">
            <slot />
        </main>
    </div>
</template>
