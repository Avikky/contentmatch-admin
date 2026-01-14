import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { useToast } from '@/Composables/useToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Setup global toast instance (must be outside createInertiaApp to persist across navigation)
const { success, error } = useToast();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);
        
        return vueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Global toast notifications handler - listens to all Inertia navigation events
router.on('finish', (event) => {
    // Access page data from the router's current page state
    const page = router.page;
    
    if (!page || !page.props) return;
    
    // Handle success flash messages
    if (page.props.flash?.success) {
        success(page.props.flash.success);
    }
    
    if (page.props.flash?.message) {
        success(page.props.flash.message);
    }
    
    // Handle error flash messages
    if (page.props.flash?.error) {
        error(page.props.flash.error);
    }
    
    // Handle validation errors (show first error only)
    if (page.props.errors && typeof page.props.errors === 'object') {
        const errorKeys = Object.keys(page.props.errors);
        if (errorKeys.length > 0) {
            const firstError = page.props.errors[errorKeys[0]];
            if (Array.isArray(firstError) && firstError[0]) {
                error(firstError[0]);
            } else if (typeof firstError === 'string') {
                error(firstError);
            }
        }
    }
});

// Handle network and exception errors
router.on('exception', (event) => {
    console.error('Inertia exception:', event.detail);
    error('An unexpected error occurred. Please try again.');
});
