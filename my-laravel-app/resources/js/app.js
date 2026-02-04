
import { createApp } from 'vue'
import App from './components/App.vue'
import router from './router'
import "./bootstrap"
import axios from "axios"


axios.defaults.baseURL = "http://127.0.0.1:8000"
axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true


createApp(App).use(router).mount('#app')
