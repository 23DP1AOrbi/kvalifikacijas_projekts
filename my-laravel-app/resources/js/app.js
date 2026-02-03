// import { createApp } from 'vue'
// import Home from './components/Home.vue'

// createApp(Home).mount('#app')

// import { createApp } from 'vue'
// import App from './components/App.vue'
// import About from './components/About.vue'
// import Home from './components/Home.vue'

// import router from './router'


// createApp(App).use(router).mount('#app')
// createApp(About).use(router).mount('#about')
// createApp(Home).use(router).mount('#app')

import { createApp } from 'vue'
import App from './components/App.vue'
import router from './router'

createApp(App).use(router).mount('#app')


// import '../css/app.css';
// import './bootstrap';

// import { createInertiaApp } from '@inertiajs/vue3';
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import { createApp, h } from 'vue';
// import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// createInertiaApp({
//     title: (title) => `${title} - ${appName}`,
//     resolve: (name) =>
//         resolvePageComponent(
//             `./Pages/${name}.vue`,
//             import.meta.glob('./Pages/**/*.vue'),
//         ),
//     setup({ el, App, props, plugin }) {
//         return createApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(ZiggyVue)
//             .mount(el);
//     },
//     progress: {
//         color: '#4B5563',
//     },
// });
