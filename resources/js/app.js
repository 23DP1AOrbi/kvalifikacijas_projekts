
import { createApp } from 'vue'
import App from './components/App.vue'
import router from './router'
import vuetify from './plugins/vuetify'
import "./bootstrap"
import axios from "axios"
import { fetchUser } from './services/auth'
import '@mdi/font/css/materialdesignicons.css'


axios.defaults.baseURL = "http://localhost:8000"
axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true


const initApp = async () => {
    await fetchUser();

    const app = createApp(App);
    app.use(router).use(vuetify);
    app.mount('#app');
};

initApp();