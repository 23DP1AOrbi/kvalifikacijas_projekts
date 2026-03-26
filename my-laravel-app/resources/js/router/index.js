import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Home.vue';
import About from '../components/About.vue';
import Register from '../components/Register.vue';
import Login from '../components/Login.vue';
import Profile from '../components/Profile.vue'
import DesignGallery from '../components/DesignGallery.vue'
import DesignView from '../components/DesignView.vue'
import DesignUpload from '../components/DesignUpload.vue'



const routes = [
    { path: '/', component: Home },
    { path: '/about', component: About },
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/profils', component: Profile },
    { path: '/dizaini', component: DesignGallery},
    { path: '/dizaini/:id', component: DesignView, props: true},
    { path: '/pievienot', component: DesignUpload},

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;