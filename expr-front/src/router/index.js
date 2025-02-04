import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Home from '../views/Home.vue';
import Storage from '../views/Storage.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/storage',
    name: 'Storage',
    component: Storage,
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
