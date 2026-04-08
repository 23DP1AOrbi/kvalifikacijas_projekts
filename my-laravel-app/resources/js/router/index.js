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
import DesignEdit from '../components/DesignEdit.vue'


const routes = [
    { path: '/', component: Home },
    { path: '/about', component: About },
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/profils', component: Profile },
    { path: '/dizaini', component: DesignGallery},
    { path: '/dizaini/:id', component: DesignView, props: true},
    { path: '/pievienot', component: DesignUpload, meta: { requiresAdmin: true }},
    { path: '/kategorijas', component: CategoryManager, meta: { requiresAdmin: true } },
    { path: '/dizaini/:id/edit', component: DesignEdit, meta: { requiresAdmin: true } },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
  if (to.meta.requiresAdmin) {

    if (user.value && user.value.role === 'admin') {
      next();
    } else {
      console.warn("User is not admin or not logged in");
      next('/login'); 
    }
  } else {
    next();
  }
});

export default router;