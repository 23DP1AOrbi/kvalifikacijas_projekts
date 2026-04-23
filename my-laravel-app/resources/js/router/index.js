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
import NotFound from '../components/NotFound.vue'


const routes = [
    { path: '/', component: Home },
    { path: '/par-mums', component: About },
    { path: '/registracija', component: Register },
    { path: '/pieslegties', component: Login },
    { path: '/profils', component: Profile, meta: {requiresUser: true} },
    { path: '/dizaini', component: DesignGallery},
    { path: '/dizaini/:id', component: DesignView, props: true},
    { path: '/pievienot', component: DesignUpload, meta: { requiresAdmin: true }},
    { path: '/kategorijas', component: CategoryManager, meta: { requiresAdmin: true } },
    { path: '/dizaini/:id/edit', component: DesignEdit, meta: { requiresAdmin: true } },
    { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
  if (to.meta.requiresAdmin || to.meta.requiresUser) {

    if (user.value && user.value.role === 'admin') {
      next();
    } else if  (user.value && user.value.role === 'user') {
      next();
    } else {
      next('/pieslegties'); 
    }
  } else {
    next();
  }
});

export default router;