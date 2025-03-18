import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createPinia } from 'pinia'
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

//const appName = import.meta.env.VITE_APP_NAME || 'Laravel';  - ${appName}
const pinia = createPinia();
import { PerfectScrollbarPlugin } from 'vue3-perfect-scrollbar';
import 'vue3-perfect-scrollbar/style.css';

createInertiaApp({
    title: (title) => `${title}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue)
            .use(PerfectScrollbarPlugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
