import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const syncTimezoneContext = () => {
    if (typeof window === 'undefined') {
        return;
    }

    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (!timezone) {
        return;
    }

    document.cookie = `timezone=${encodeURIComponent(timezone)}; path=/; max-age=31536000; SameSite=Lax`;

    if (window.axios) {
        window.axios.defaults.headers.common['X-Timezone'] = timezone;
    }
};

syncTimezoneContext();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
