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
