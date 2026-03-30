import { createRouter, createWebHistory } from 'vue-router';
import { user } from '../services/auth';
import Home from '../components/Home.vue';
import About from '../components/About.vue';
import Register from '../components/Register.vue';
import Login from '../components/Login.vue';
import Profile from '../components/Profile.vue'
import DesignGallery from '../components/DesignGallery.vue'
import DesignView from '../components/DesignView.vue'
import DesignUpload from '../components/DesignUpload.vue'
import CategoryManager from '../components/CategoryManager.vue'



const routes = [
    { path: '/', component: Home },
    { path: '/about', component: About },
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/profils', component: Profile },
    { path: '/dizaini', component: DesignGallery},
    { path: '/dizaini/:id', component: DesignView, props: true},
    { path: '/pievienot', component: DesignUpload, meta: { requiresAdmin: true }},
    { path: '/kategorijas', component: CategoryManager },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {

    // console.log("User in guard: ", user.value);
    // console.log("Route meta:", to.meta);
    // console.log("Navigating to: ", to.path);
    // console.log("User role", user.value?.role);

  if (to.meta.requiresAdmin && user.value?.role !== 'admin') {
    return next('/'); // redirect to home
  }

  next();
});

export default router;