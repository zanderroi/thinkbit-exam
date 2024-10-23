import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const appName = import.meta.env.VITE_APP_NAME || 'Book Store ADMIN';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, { 
                position: POSITION.TOP_RIGHT, // You can change the position as needed
                timeout: 5000, // Optional, the duration of the toast
            })
            .mount(el);
            
    },
    progress: {
        color: '#4B5563',
    },
});